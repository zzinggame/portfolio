<?php

if ($props['image']) {
    $src = $props['image'];
    $focal = $props['image_focal_point'];
    $media = include "{$__dir}/template-image.php";
} elseif ($props['video']) {
    $src = $props['video'];
    $media = include "{$__dir}/template-video.php";
} elseif ($props['hover_image']) {
    $src = $props['hover_image'];
    $focal = $props['hover_image_focal_point'];
    $media = include "{$__dir}/template-image.php";
}

// Min-height Placeholder
$placeholder = '';
if ($element['has_placeholder']) {

    $placeholder = clone $media;

    $placeholder->attr('class', ['uk-invisible']);

    if (!$props['image'] && $props['video']) {
        $placeholder->attr('autoplay', false);
    }

}

// Media
$media->attr([

    'class' => [
        'el-image',
        'uk-blend-{0}' => $props['media_blend_mode'],
        'uk-transition-{image_transition}',
        'uk-transition-opaque' => $props['image'] || $props['video'],
        'uk-transition-fade {@!image_transition}' => $props['hover_image'] && !($props['image'] || $props['video']),
    ],

    'uk-cover' => $element['has_cover'],

]);

// Hover Media
$hover_media = '';
if ($props['hover_image'] && ($props['image'] || $props['video'])) {

    $src = $props['hover_image'];
    $hover_media = include "{$__dir}/template-image.php";

    $hover_media->attr([
        'alt' => true,
        'loading' => false,

        'class' => [
            'el-hover-image',
            'uk-transition-{image_transition}',
            'uk-transition-fade {@!image_transition}',
        ],

        'uk-cover' => true,

    ]);

}

?>

<?php if ($placeholder) : ?>
<?= $placeholder($props, '') ?>
<?php endif ?>

<?= $media($element, '') ?>

<?php if ($hover_media) : ?>
<?= $hover_media($element, '') ?>
<?php endif ?>
