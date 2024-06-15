<?php

if (!$props['image']) {
    return;
}

echo $this->el('image', [

    'class' => [
        'el-image',
        'uk-responsive-width',
        'uk-margin-auto',
        'uk-display-block',
        'uk-border-{image_border}',
        'uk-text-{image_svg_color} {@image_svg_inline}' => $this->isImage($props['image']) == 'svg',
    ],

    'src' => $props['image'],
    'alt' => $props['image_alt'],
    'loading' => $element['image_loading'] ? false : null,
    'width' => $element['image_width'],
    'height' => $element['image_height'],
    'focal_point' => $props['image_focal_point'],
    'uk-svg' => $element['image_svg_inline'],
    'thumbnail' => true,

])->render($element);
