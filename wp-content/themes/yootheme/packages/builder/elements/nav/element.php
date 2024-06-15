<?php

namespace YOOtheme;

return [
    'updates' => [
        // moved from 4.0.0-beta.9 to 4.3.9 (previously missing @import)
        '4.3.9' => function ($node) {
            if (Arr::get($node->props, 'nav_element') === 'nav') {
                $node->props['html_element'] = 'nav';
                unset($node->props['nav_element']);
            }
        },
    ],
];
