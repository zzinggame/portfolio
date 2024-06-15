<?php

namespace YOOtheme\Builder\Wordpress\Source\Listener;

use YOOtheme\Builder\BuilderConfig;
use YOOtheme\Builder\Wordpress\Source\Helper;
use YOOtheme\Config;
use function YOOtheme\trans;

class LoadBuilderConfig
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param BuilderConfig $config
     */
    public function handle($config): void
    {
        $archives = [];
        $templates = [];

        require_once ABSPATH . 'wp-admin/includes/translation-install.php';

        $translations = wp_get_available_translations();
        $languages = [['text' => 'English (United States)', 'value' => 'en_US']];

        foreach (get_available_languages() as $lang) {
            if (isset($translations[$lang])) {
                $languages[] = ['text' => $translations[$lang]['native_name'], 'value' => $lang];
            }
        }

        foreach (Helper::getPostTypes() as $name => $type) {
            $templates["single-{$name}"] = [
                'label' => trans('Single %post_type%', [
                    '%post_type%' => $type->labels->singular_name,
                ]),
                'group' => trans('Single Post'),
            ];

            $taxonomies = get_object_taxonomies($name);

            sort($taxonomies);

            if ($taxonomies) {
                $label_lower = mb_strtolower($type->labels->name);

                $templates["single-{$name}"] += [
                    'fieldset' => [
                        'default' => [
                            'fields' => [
                                'terms' => [
                                    'label' => trans('Limit by Terms'),
                                    'description' => trans(
                                        'The template is only assigned to %post_types_lower% with the selected terms. %post_types% from child terms are not included. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple terms.',
                                        [
                                            '%post_types_lower%' => $label_lower,
                                            '%post_types%' => $type->labels->name,
                                        ],
                                    ),
                                    'type' => 'select',
                                    'default' => [],
                                    'options' => array_map(
                                        fn($taxonomy) => [
                                            'evaluate' => "yootheme.builder.taxonomies['{$taxonomy}']",
                                        ],
                                        $taxonomies,
                                    ),
                                    'attrs' => [
                                        'multiple' => true,
                                        'class' => 'uk-height-medium',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
            }

            if (count($languages) > 1) {
                $templates["single-{$name}"]['fieldset']['default']['fields'][
                    'locale'
                ] = static::getLocaleField();
            }

            if ($name === 'post' || $type->has_archive) {
                $archives["archive-{$name}"] = [
                    'label' => trans('%post_type% Archive', ['%post_type%' => $type->label]),
                    'group' => trans('Post Type Archive'),
                    'fieldset' => [
                        'default' => [
                            'fields' => [
                                'pages' => static::getPageField(),
                                'locale' => static::getLocaleField(),
                            ],
                        ],
                    ],
                ];
            }
        }

        $archives['author-archive'] = [
            'label' => trans('Author Archive'),
            'group' => trans('Archive Pages'),
            'fieldset' => [
                'default' => [
                    'fields' => [
                        'pages' => static::getPageField(),
                        'locale' => static::getLocaleField(),
                    ],
                ],
            ],
        ];

        $archives['date-archive'] = [
            'label' => trans('Date Archive'),
            'group' => trans('Archive Pages'),
            'fieldset' => [
                'default' => [
                    'fields' => [
                        'archive' => [
                            'label' => trans('Limit by Date Archive Type'),
                            'description' => trans(
                                'The template is only assigned to the selected pages.',
                            ),
                            'type' => 'select',
                            'options' => [
                                trans('Any') => '',
                                trans('Year Archive') => 'year',
                                trans('Month Archive') => 'month',
                                trans('Day Archive') => 'day',
                                trans('Time Archive') => 'time',
                            ],
                        ],
                        'pages' => static::getPageField(),
                        'locale' => static::getLocaleField(),
                    ],
                ],
            ],
        ];

        $templates += $archives;

        $taxonomies = [];
        $allTaxonomies = [['text' => trans('None'), 'value' => '']];

        foreach (Helper::getTaxonomies() as $name => $taxonomy) {
            $templates["taxonomy-{$name}"] = static::getTaxonomyArchive($taxonomy);
            $allTaxonomies[] = ['text' => $taxonomy->label, 'value' => $name];

            if ($terms = static::getTaxonomyTerms($taxonomy)) {
                $taxonomies[$name] = [
                    'label' => $taxonomy->label,
                    'options' => $terms,
                ];
            }
        }

        $templates['search'] = [
            'label' => trans('Search'),
            'fieldset' => [
                'default' => [
                    'fields' => [
                        'pages' => static::getPageField(),
                        'locale' => static::getLocaleField(),
                    ],
                ],
            ],
        ];

        $templates['error-404'] =
            [
                'label' => trans('Error 404'),
            ] +
            (count($languages) > 1
                ? [
                    'fieldset' => [
                        'default' => [
                            'fields' => [
                                'locale' => static::getLocaleField(),
                            ],
                        ],
                    ],
                ]
                : []);

        $authors = [];
        foreach (
            get_users(['fields' => ['ID', 'display_name'], 'capability' => ['edit_posts']])
            as $user
        ) {
            $authors[] = ['text' => $user->display_name, 'value' => (int) $user->ID];
        }

        $roles = [];
        foreach (wp_roles()->get_names() as $id => $name) {
            $roles[] = ['text' => $name, 'value' => $id];
        }

        $config->merge([
            'templates' => $templates,
            'taxonomies' => $taxonomies,
            'allTaxonomies' => $allTaxonomies,
            'authors' => $authors,
            'languages' => $languages,
            'roles' => $roles,
        ]);
    }

    public static function getTaxonomyArchive($taxonomy): array
    {
        $label_lower = mb_strtolower($taxonomy->labels->name);

        $termsFilter = [];
        foreach (Helper::getTaxonomies() as $relatedTaxonomy) {
            if (
                $relatedTaxonomy->name !== $taxonomy->name &&
                array_intersect($taxonomy->object_type, $relatedTaxonomy->object_type)
            ) {
                $termsFilter[] = [
                    'evaluate' => "yootheme.builder.taxonomies['{$relatedTaxonomy->name}']",
                ];
            }
        }

        return [
            'label' => "{$taxonomy->labels->singular_name} Archive",
            'group' => trans('Taxonomy Archive'),
            'fieldset' => [
                'default' => [
                    'fields' => [
                        'terms' => [
                            'label' => trans('Limit by %taxonomies%', [
                                '%taxonomies%' => $taxonomy->label,
                            ]),
                            'description' => trans(
                                'The template is only assigned to the selected %taxonomies%. %has_archive% Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple %taxonomies%.',
                                [
                                    '%taxonomies%' => $label_lower,
                                    '%has_archive%' => $taxonomy->hierarchical
                                        ? trans('Child %taxonomies% are not included.', [
                                            '%taxonomies%' => $label_lower,
                                        ])
                                        : '',
                                ],
                            ),
                            'type' => 'select',
                            'default' => [],
                            'options' => [
                                [
                                    'evaluate' => "yootheme.builder.taxonomies['{$taxonomy->name}'].options",
                                ],
                            ],
                            'attrs' => [
                                'multiple' => true,
                                'class' => 'uk-height-small',
                            ],
                        ],
                        'terms_filter' => [
                            'label' => 'Limit by Terms',
                            'description' => trans(
                                'The template is only assigned to %taxonomies% if the selected terms are set in the URL. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple terms.',
                                [
                                    '%taxonomies%' => $label_lower,
                                ],
                            ),
                            'type' => 'select',
                            'default' => [],
                            'options' => $termsFilter,
                            'attrs' => [
                                'multiple' => true,
                                'class' => 'uk-height-small',
                            ],
                        ],
                        'pages' => static::getPageField(),
                        'locale' => static::getLocaleField(),
                    ],
                ],
            ],
        ];
    }

    public static function getTaxonomyTerms($taxonomy): array
    {
        $terms = get_terms([
            'taxonomy' => $taxonomy->name,
            'hide_empty' => false,
        ]);

        return array_map(
            fn($term) => [
                'value' => $term->term_id,
                'text' =>
                    str_repeat('- ', count(get_ancestors($term->term_id, $term->taxonomy))) .
                    html_entity_decode($term->name),
            ],
            _get_term_children(0, $terms, $taxonomy->name),
        );
    }

    protected static function getPageField(): array
    {
        return [
            'label' => trans('Limit by Page Number'),
            'description' => trans('The template is only assigned to the selected pages.'),
            'type' => 'select',
            'options' => [
                trans('All pages') => '',
                trans('First page') => 'first',
                trans('All except first page') => 'except_first',
            ],
        ];
    }

    protected static function getLocaleField(): array
    {
        return [
            'label' => trans('Limit by Language'),
            'type' => 'select',
            'defaultIndex' => 0,
            'options' => [
                ['text' => __('All languages', 'yootheme'), 'value' => ''],
                ['evaluate' => 'yootheme.builder.languages'],
            ],
            'show' => 'yootheme.builder.languages.length > 1 || lang',
        ];
    }
}
