<?php

// Resets
if ($props['overlay_link']) { $props['title_link'] = ''; }

if ($props['slider_height'] == 'viewport') {
    if ($props['slider_height_viewport'] > 100) {
        $props['slider_height_offset_top'] = false;
    } elseif (!$props['slider_height_viewport']) {
        $props['slider_height_viewport'] = 100;
    }
}
if ($props['slider_parallax']) {
    $props['slidenav'] = '';
}

// New logic shortcuts
$props['overlay_cover'] = $props['overlay_style'] && $props['overlay_mode'] == 'cover';
$props['has_placeholder'] = ($props['slider_min_height'] && !($props['slider_width'] && $props['slider_height'])) || !$props['slider_width'];
$props['has_cover'] = $props['slider_min_height'] || ($props['slider_width'] && $props['slider_height']) || !$props['slider_width'];

$el = $this->el('div', [

    'class' => [
        'uk-slider-container {@!slidenav: outside}',
    ],

    'uk-slider' => $this->expr([
        'sets: {slider_sets}; {@!slider_parallax}',
        'center: {slider_center};',
        'finite: {slider_finite};',
        'velocity: {slider_velocity}; {@!slider_parallax}',
        'autoplay: {slider_autoplay}; [pauseOnHover: false; {@!slider_autoplay_pause}] [autoplayInterval: {slider_autoplay_interval}000;] {@!slider_parallax}',
        // Parallax
        'parallax: true; {@slider_parallax}',
        'parallax-easing: {slider_parallax_easing}; {@slider_parallax}',
        'parallax-target: {slider_parallax_target}; {@slider_parallax}',
        'parallax-start: {slider_parallax_start}; {@slider_parallax}',
        'parallax-end: {slider_parallax_end}; {@slider_parallax}',
        // Overlay active
        'clsActivated: uk-transition-active; [active: first; {@overlay_active_first}] {@overlay_display: active}',
    ], $props) ?: true,

]);

// Slider Items
$slider_items = $this->el('ul', [

    'class' => [
        'uk-slider-items',
        'uk-grid [uk-grid-{!slider_gap: default}] {@slider_gap}',
        'uk-grid-divider {@slider_gap} {@slider_divider}',
        'uk-grid-match' => ($props['slider_width'] && $props['slider_height']) || !$props['slider_width'],
    ],

    'style' => [
        'min-height: max({0}px, {slider_height_viewport}vh); {@slider_height: viewport} {@!slider_height_offset_top}' => $props['slider_min_height'] ?: 0,
    ],

    // Height Viewport
    'uk-height-viewport' => $props['slider_width'] && $props['slider_height'] && !($props['slider_height'] == 'viewport' && !$props['slider_height_offset_top']) ? [
        'offset-top: true; {@slider_height_offset_top}',
        'minHeight: {slider_min_height};',
        'offset-bottom: {0}; {@slider_height: viewport}' => $props['slider_height_viewport'] && $props['slider_height_viewport'] < 100 ? 100 - (int) $props['slider_height_viewport'] : false,
        'offset-bottom: !:is(.uk-section-default,.uk-section-muted,.uk-section-primary,.uk-section-secondary) +; {@slider_height: section}',
    ] : false,

]);

$slider_item = $this->el('li', [

    'class' => [
        'uk-width-{slider_width_default} {@slider_width}',
        'uk-width-{slider_width_small}@s {@slider_width}',
        'uk-width-{slider_width_medium}@m {@slider_width}',
        'uk-width-{slider_width_large}@l {@slider_width}',
        'uk-width-{slider_width_xlarge}@xl {@slider_width}',
    ],

]);

// Container
$container = $this->el('div', [

    'class' => [
        'uk-position-relative',
        'uk-visible-toggle {@slidenav} {@slidenav_hover}',
    ],

    'tabindex' => ['-1 {@slidenav} {@slidenav_hover}'],

]);

?>

<?= $el($props, $attrs) ?>

    <?= $container($props) ?>

        <?php if ($props['slidenav'] == 'outside') : ?>
        <div class="uk-slider-container">
        <?php endif ?>

            <?= $slider_items($props) ?>
                <?php foreach ($children as $child) : ?>
                <?= $slider_item($props, $builder->render($child, ['element' => $props])) ?>
                <?php endforeach ?>
            <?= $slider_items->end() ?>

        <?php if ($props['slidenav'] == 'outside') : ?>
        </div>
        <?php endif ?>

        <?php if ($props['slidenav']) : ?>
        <?= $this->render("{$__dir}/template-slidenav") ?>
        <?php endif ?>

    <?= $container->end() ?>

    <?php if ($props['nav']): ?>
    <?= $this->render("{$__dir}/template-nav") ?>
    <?php endif ?>

<?= $el->end() ?>
