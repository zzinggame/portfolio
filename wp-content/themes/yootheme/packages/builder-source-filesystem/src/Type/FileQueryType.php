<?php

namespace YOOtheme\Builder\Source\Filesystem\Type;

use function YOOtheme\app;
use YOOtheme\Builder\Source\Filesystem\FileHelper;
use function YOOtheme\trans;

class FileQueryType
{
    /**
     * @param string $rootDir
     *
     * @return array
     */
    public static function config($rootDir)
    {
        return [
            'fields' => [
                'file' => [
                    'type' => 'File',

                    'args' => [
                        'pattern' => [
                            'type' => 'String',
                        ],
                        'offset' => [
                            'type' => 'Int',
                        ],
                        'order' => [
                            'type' => 'String',
                        ],
                        'order_direction' => [
                            'type' => 'String',
                        ],
                    ],

                    'metadata' => [
                        'label' => trans('File'),
                        'group' => trans('External'),
                        'fields' => [
                            'pattern' => [
                                'label' => trans('Path Pattern'),
                                'description' => "Pick a folder to load file content dynamically. Alternatively, set a path <a href=\"https://www.php.net/manual/en/function.glob.php\" target=\"_blank\">glob pattern</a> to filter files. For example <code>{$rootDir}/*.{jpg,png}</code>. The path is relative to the system folder and has to be a subdirectory of <code>{$rootDir}</code>.",
                                'type' => 'select-file',
                            ],
                            'offset' => [
                                'label' => trans('Offset'),
                                'description' => trans(
                                    'Set the offset to specify which file is loaded.',
                                ),
                                'type' => 'number',
                                'default' => 0,
                                'modifier' => 1,
                                'attrs' => [
                                    'min' => 1,
                                    'required' => true,
                                ],
                            ],
                            '_order' => [
                                'type' => 'grid',
                                'width' => '1-2',
                                'description' => trans(
                                    'The Default order will follow the order set by the brackets or fallback to the default files order set by the system.',
                                ),
                                'fields' => [
                                    'order' => [
                                        'label' => trans('Order'),
                                        'type' => 'select',
                                        'default' => 'name',
                                        'options' => [
                                            trans('Default') => 'default',
                                            trans('Alphabetical') => 'name',
                                            trans('Random') => 'rand',
                                        ],
                                    ],
                                    'order_direction' => [
                                        'label' => trans('Direction'),
                                        'type' => 'select',
                                        'default' => 'ASC',
                                        'options' => [
                                            trans('Ascending') => 'ASC',
                                            trans('Descending') => 'DESC',
                                        ],
                                        'enable' => 'order != "rand"',
                                    ],
                                ],
                            ],
                        ],
                    ],

                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],
                ],
            ],
        ];
    }

    public static function resolve($root, array $args)
    {
        $files = app(FileHelper::class)->query(['limit' => 1] + $args);
        return array_shift($files);
    }
}
