<?php

namespace YOOtheme\Builder\Wordpress\PopularPosts\Listener;

use WordPressPopularPosts\Helper as PopularPostsHelper;
use YOOtheme\Builder\Wordpress\Source\Helper as SourceHelper;

class ResolveSourcePosts
{
    public function handle($query)
    {
        if (!class_exists(PopularPostsHelper::class)) {
            return;
        }

        if (!in_array($query['orderby'], ['comments', 'views', 'avg'])) {
            return;
        }

        $now = new \DateTime(PopularPostsHelper::now(), wp_timezone());
        $range = $query['order'];
        $order = $query['orderby'];

        SourceHelper::filterOnce('posts_where', $this->postsWhereFn($range, $order));
        SourceHelper::filterOnce('posts_fields', $this->postsFieldsFn($range, $order, $now));
        SourceHelper::filterOnce('posts_orderby', $this->postsOrderbyFn($range, $order));
        SourceHelper::filterOnce('posts_groupby', $this->postsGroupbyFn($range, $order));
        SourceHelper::filterOnce('posts_join', $this->postsJoinFn($range, $order, $now));
    }

    protected function postsWhereFn($range, $order)
    {
        return function ($where) use ($range, $order) {
            if ($range === 'all' && $order === 'comments') {
                return $where . 'AND comment_count > 0';
            }

            return $where;
        };
    }

    protected function postsFieldsFn($range, $order, $now)
    {
        return function ($fields) use ($range, $order, $now) {
            if ($range === 'all') {
                if ($order === 'avg') {
                    return "{$fields}, (v.pageviews/(IF (DATEDIFF('{$now->format(
                        'Y-m-d',
                    )}', MIN(v.day)) > 0, DATEDIFF('{$now->format(
                        'Y-m-d',
                    )}', MIN(v.day)), 1))) AS avg_views";
                }

                return $fields;
            }

            if ($order === 'views') {
                return "{$fields}, pageviews";
            }
            if ($order === 'avg') {
                return "{$fields}, avg_views";
            }
            return "{$fields}, c.comment_count";
        };
    }

    protected function postsOrderbyFn($range, $order)
    {
        return function () use ($range, $order) {
            if ($order === 'views') {
                return 'pageviews DESC';
            }
            if ($order === 'avg') {
                return 'avg_views DESC';
            }

            return ($range === 'all' ? '' : 'c.') . 'comment_count DESC';
        };
    }

    protected function postsGroupbyFn($range, $order)
    {
        return function ($groupby) use ($range, $order) {
            if ($range === 'all' && $order === 'avg') {
                return 'v.postid';
            }

            return $groupby;
        };
    }

    protected function postsJoinFn($range, $order, $now)
    {
        return function ($join) use ($range, $order, $now) {
            global $wpdb;

            if ($range === 'all') {
                if ($order !== 'comments') {
                    return "{$join} INNER JOIN `{$wpdb->prefix}popularpostsdata` v ON {$wpdb->posts}.ID = v.postid";
                }

                return $join;
            }

            $startDate = clone $now;

            switch ($range) {
                case 'daily':
                    $startDate = $startDate->sub(new \DateInterval('P1D'));
                    $startDatetime = $startDate->format('Y-m-d H:i:s');
                    $views_time_range = "view_datetime >= '{$startDatetime}'";
                    break;
                case 'weekly':
                    $startDate = $startDate->sub(new \DateInterval('P6D'));
                    $startDatetime = $startDate->format('Y-m-d');
                    $views_time_range = "view_date >= '{$startDatetime}'";
                    break;
                case 'monthly':
                default:
                    $startDate = $startDate->sub(new \DateInterval('P29D'));
                    $startDatetime = $startDate->format('Y-m-d');
                    $views_time_range = "view_date >= '{$startDatetime}'";
                    break;
            }

            if ($order === 'views') {
                return "{$join} INNER JOIN (SELECT SUM(pageviews) AS pageviews, postid FROM `{$wpdb->prefix}popularpostssummary` WHERE {$views_time_range} GROUP BY postid) v ON {$wpdb->posts}.ID = v.postid";
            }

            if ($order === 'avg') {
                return "{$join} INNER JOIN (SELECT SUM(pageviews)/(IF (DATEDIFF('{$now->format(
                    'Y-m-d H:i:s',
                )}', '{$startDatetime}') > 0, DATEDIFF('{$now->format(
                    'Y-m-d H:i:s',
                )}', '{$startDatetime}'), 1)) AS avg_views, postid FROM `{$wpdb->prefix}popularpostssummary` WHERE {$views_time_range} GROUP BY postid) v ON {$wpdb->posts}.ID = v.postid";
            }

            if ($order === 'comments') {
                return "{$join} INNER JOIN (SELECT COUNT(comment_post_ID) AS comment_count, comment_post_ID FROM `{$wpdb->comments}` WHERE comment_date_gmt >= '{$startDatetime}' AND comment_approved = '1' GROUP BY comment_post_ID) c ON {$wpdb->posts}.ID = c.comment_post_ID";
            }
        };
    }
}
