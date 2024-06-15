<?php

$config = [
    'name' => 'framework/wordpress',

    'main' => 'YOOtheme\\Framework\\Wordpress\\WordpressPlugin',

    'autoload' => [
        'YOOtheme\\Framework\\Wordpress\\' => 'src',
    ],
];

return defined('WPINC') ? $config : false;
