<?php

namespace YOOtheme;

return [
    'updates' => [
        '2.8.0-beta.0.13' => function ($node) {
            foreach (['title_style', 'meta_style', 'content_style'] as $prop) {
                if (in_array(Arr::get($node->props, $prop), ['meta', 'lead'])) {
                    $node->props[$prop] = 'text-' . Arr::get($node->props, $prop);
                }
            }
        },

        '2.2.0-beta.2.1' => function ($node) {
            if (Arr::get($node->props, 'title_style') === 'strong') {
                $node->props['title_style'] = 'text-bold';
            }

            if (
                in_array(Arr::get($node->props, 'title_style'), [
                    'h1',
                    'h2',
                    'h3',
                    'h4',
                    'h5',
                    'h6',
                ])
            ) {
                $node->props['title_element'] = 'h3';
            }
        },

        '2.1.0-beta.0.1' => function ($node) {
            if (Arr::get($node->props, 'list_size') === true) {
                $node->props['list_size'] = 'large';
            } else {
                $node->props['list_size'] = '';
            }
        },

        '1.22.0-beta.0.1' => function ($node) {
            if (
                Arr::get($node->props, 'layout') === 'grid-2-m' &&
                Arr::get($node->props, 'width') === 'expand' &&
                isset($node->props['leader'])
            ) {
                $node->props['gutter'] = 'small';
            }

            Arr::updateKeys($node->props, [
                'gutter' => fn($value) => [
                    'title_grid_column_gap' => $value,
                    'title_grid_row_gap' => $value,
                ],
                'breakpoint' => 'title_grid_breakpoint',
                'width' => 'title_grid_width',
                'leader' => 'title_leader',
            ]);
        },

        '1.20.0-beta.1.1' => function ($node) {
            Arr::updateKeys($node->props, ['maxwidth_align' => 'block_align']);
        },

        '1.20.0-beta.0.1' => function ($node) {
            /** @var Config $config */
            $config = app(Config::class);

            [$style] = explode(':', $config('~theme.style'));

            if (
                in_array($style, [
                    'craft',
                    'district',
                    'jack-backer',
                    'tomsen-brody',
                    'vision',
                    'florence',
                    'max',
                    'nioh-studio',
                    'sonic',
                    'summit',
                    'trek',
                ])
            ) {
                if (Arr::get($node->props, 'title_style') === 'h1') {
                    $node->props['title_style'] = 'heading-small';
                }
            }

            if (in_array($style, ['florence', 'max', 'nioh-studio', 'sonic', 'summit', 'trek'])) {
                if (Arr::get($node->props, 'title_style') === 'h2') {
                    $node->props['title_style'] = 'h1';
                }
            }

            if ($style == 'copper-hill') {
                if (Arr::get($node->props, 'title_style') === 'h1') {
                    $node->props['title_style'] = 'h2';
                }
            }
        },

        '1.19.0-beta.0.1' => function ($node) {
            if (Arr::get($node->props, 'meta_align') === 'top-title') {
                $node->props['meta_align'] = 'above-title';
            }

            if (Arr::get($node->props, 'meta_align') === 'bottom-title') {
                $node->props['meta_align'] = 'below-title';
            }

            if (Arr::get($node->props, 'meta_align') === 'top-content') {
                $node->props['meta_align'] = 'above-content';
            }

            if (Arr::get($node->props, 'meta_align') === 'bottom-content') {
                $node->props['meta_align'] = 'below-content';
            }
        },

        '1.18.0' => function ($node) {
            if (Arr::get($node->props, 'title_style') === 'muted') {
                $node->props['title_style'] = '';
                $node->props['title_color'] = 'muted';
            }

            if (
                !isset($node->props['meta_color']) &&
                in_array(Arr::get($node->props, 'meta_style'), ['muted', 'primary'], true)
            ) {
                $node->props['meta_color'] = $node->props['meta_style'];
                $node->props['meta_style'] = '';
            }

            switch (Arr::get($node->props, 'layout')) {
                case '':
                    $node->props['width'] = 'auto';
                    $node->props['layout'] = 'grid-2';
                    break;
                case 'width-small':
                    $node->props['width'] = 'small';
                    $node->props['layout'] = 'grid-2';
                    break;
                case 'width-medium':
                    $node->props['width'] = 'medium';
                    $node->props['layout'] = 'grid-2';
                    break;
                case 'space-between':
                    $node->props['width'] = 'expand';
                    $node->props['layout'] = 'grid-2';
                    break;
            }
        },
    ],
];
