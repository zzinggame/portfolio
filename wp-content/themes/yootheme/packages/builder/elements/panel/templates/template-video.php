<?php

if ($iframe = $this->iframeVideo($src)) {

    $video = $this->el('iframe', [

        'class' => [
            'uk-disabled',
        ],

        'src' => $iframe,
        'allow' => 'autoplay',
        'uk-responsive' => true,
        'loading' => ['lazy {@image_loading}'],

    ]);

} else {

    $video = $this->el('video', [

        'src' => $src,
        'controls' => false,
        'loop' => true,
        'autoplay' => true,
        'muted' => true,
        'playsinline' => true,
        'preload' => ['none {@image_loading}'],

        'class' => [
            // Imitate cropping like an image
            'uk-object-cover [uk-object-{image_focal_point}] {@image_width} {@image_height} {@!panel_expand: image|both}', // Already set if expand
        ],

        'style' => [
            // Keep video responsiveness but with new proportions (because video isn't cropped like an image)
            'aspect-ratio: {image_width} / {image_height}; {@!panel_expand: image|both}',
        ],

    ]);

}

$video->attr([

    'width' => $props['image_width'],
    'height' => $props['image_height'],

    'uk-video' => [
        'automute: true' => true,
    ],

]);

return $video;
