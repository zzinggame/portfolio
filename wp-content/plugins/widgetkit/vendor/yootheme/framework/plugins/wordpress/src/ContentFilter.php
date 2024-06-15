<?php

namespace YOOtheme\Framework\Wordpress;

use YOOtheme\Framework\Filter\FilterInterface;

class ContentFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        $value = wp_filter_content_tags($value);
        $value = wp_replace_insecure_home_url($value);
        $value = do_shortcode($value); // 11
        $value = convert_smilies($value); // 20
        return $value;
    }
}
