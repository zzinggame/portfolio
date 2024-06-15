<?php

// Override default settings
$element['text_color'] = $props['text_color'] ?: $element['text_color'];

// New logic shortcuts
$element['has_link'] = $props['link'] && $element['overlay_link'];
$element['has_transition'] = $element['overlay_display'] || $element['image_transition'] || $props['hover_image'];
$element['text_color_inverse'] = $element['text_color'] === 'light' ? 'dark' : 'light';

$el = $this->el($props['item_element'] ?: 'div', [

    'class' => [
        'el-item',

        // Needs to be parent of `uk-link-toggle`
        'uk-{0}' => !$element['overlay_style'] || $element['overlay_cover'] ? $element['text_color'] : false,
        // Only for transparent navbar
        'uk-inverse-{0}' => $element['overlay_style'] && !$element['overlay_cover'] ? $element['text_color'] : false,

        // Match link container height because image covers if height is set to viewport or if width is set to auto
        'uk-grid-item-match {@has_link}' => ($element['slider_width'] && $element['slider_height']) || !$element['slider_width'],
    ],

]);

// Link Container
$link_container = $element['has_link'] ? $this->el('a') : null;

($element['has_link'] ? $link_container : $el)->attr([

    'class' => [
        'uk-cover-container',
        'uk-transition-toggle {@has_transition}',
    ],

    'style' => [
        'min-height: {slider_min_height}px; {@!slider_height}',
        'background-color: ' . $props['media_background'] . ';' => $props['media_background'],
    ],

    'tabindex' => $element['has_transition'] && !$element['has_link'] ? 0 : null,

    // Needs to be on anchor to have just one focusable toggle when using keyboard navigation
    'uk-toggle' => ($props['text_color_hover'] || $element['text_color_hover']) && ((!$element['overlay_style'] && $props['hover_image']) || ($element['overlay_cover'] && $element['overlay_display'] && $element['overlay_transition_background'])) ? [
        'cls: uk-{text_color_inverse} [uk-{text_color}];',
        'mode: hover;',
        'target: !*; {@has_link}',
    ] : false,
    
]);

$overlay = $this->el('div', [

    'class' => [
        'uk-{overlay_style}',
        'uk-transition-{overlay_transition} {@overlay_display} {@overlay_cover}',

        'uk-position-cover {@overlay_cover}',
        'uk-position-{overlay_margin} {@overlay_cover}',
    ],

]);

$position = $this->el('div', [

    'class' => [
        'uk-position-{overlay_position} [uk-position-{overlay_margin} {@overlay_style}]',
        'uk-transition-{overlay_transition} {@overlay_display}' => !$element['overlay_transition_background'] || !$element['overlay_cover'],
    ],

]);

$content = $this->el('div', [

    'class' => [
        $element['overlay_style'] ? 'uk-overlay' : 'uk-panel',
        'uk-padding {@!overlay_padding} {@!overlay_style}',
        'uk-padding-{!overlay_padding: |none}',
        'uk-padding-remove {@overlay_padding: none} {@overlay_style}',
        'uk-width-{overlay_maxwidth} {@!overlay_position: top|bottom}',
        'uk-margin-remove-first-child',
    ],

]);

if (!$element['overlay_cover']) {
    $position->attr($overlay->attrs);
}

// Link
$link = include "{$__dir}/template-link.php";

?>

<?= $el($element, $attrs) ?>

    <?php if ($link_container) : ?>
    <?= $link_container($element) ?>
    <?php endif ?>

        <?= $this->render("{$__dir}/template-media", compact('props')) ?>

        <?php if ($props['media_overlay']) : ?>
        <div class="uk-position-cover" style="background-color:<?= $props['media_overlay'] ?>"></div>
        <?php endif ?>

        <?php if ($element['overlay_cover']) : ?>
        <?= $overlay($element, '') ?>
        <?php endif ?>

        <?php if ($props['title'] || $props['meta'] || $props['content'] || ($props['link'] && ($props['link_text'] || $element['link_text']))) : ?>
        <?= $position($element, $content($element, $this->render("{$__dir}/template-content", compact('props', 'link')))) ?>
        <?php endif ?>

    <?php if ($link_container) : ?>
    <?= $link_container->end() ?>
    <?php endif ?>

<?= $el->end() ?>