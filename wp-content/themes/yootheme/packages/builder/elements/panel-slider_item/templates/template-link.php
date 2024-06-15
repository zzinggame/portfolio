<?php

$link = $props['link'] ? $this->el('a', [
    'href' => $props['link'],
    'aria-label' => $props['link_aria_label'] ?: $element['link_aria_label'],
    'target' => ['_blank {@link_target}'],
    'uk-scroll' => str_contains((string) $props['link'], '#'),
]) : null;

if ($link && $element['panel_link']) {

    $link_container->attr($link->attrs + [

        'class' => [
            'uk-link-toggle',
        ],

    ]);

    $props['title'] = $this->striptags($props['title']);
    $props['meta'] = $this->striptags($props['meta']);
    $props['content'] = $this->striptags($props['content']);

    if ($props['title'] && $element['title_hover_style'] != 'reset') {
        $props['title'] = $this->el('span', [
            'class' => [
                'uk-link-{title_hover_style: heading}',
                'uk-link {!title_hover_style}',
            ],
        ], $props['title'])->render($element);
    }

}

if ($link && $props['title'] && $element['title_link']) {

    $props['title'] = $link($element, [
        'class' => [
            'uk-link-{title_hover_style}',
        ],
    ], $this->striptags($props['title']));

}

if ($link && $props['image'] && $element['image_link']) {

    $props['image'] = $link($element, [
        'class' => [
            'uk-flex {@panel_expand: image}',
            // Flex-1 only necessary if flex-column is parent which is the case for image not wrapped in card media or grid
            'uk-flex-1 {@panel_expand: image}' => !($element['panel_style'] && $props['image'] && $element['panel_image_no_padding'] && $element['image_align'] != 'between') && !in_array($element['image_align'], ['left', 'right']),
        ],
    ], $props['image']);

}

if ($link && ($props['link_text'] || $element['link_text'])) {

    if ($element['panel_link']) {
        $link = $this->el('div');
    }

    $link->attr('class', [
        'el-link',
        'uk-{link_style: link-(muted|text)}',
        'uk-button uk-button-{!link_style: |link-muted|link-text} [uk-button-{link_size}] [uk-width-1-1 {@link_fullwidth}]',
        // Keep link style if panel link
        'uk-link {@link_style:} {@panel_link}',
        'uk-text-muted {@link_style: link-muted} {@panel_link}',
    ]);

}

return $link;
