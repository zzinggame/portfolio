<?php

// Display
if (!$element['show_title']) {
    $props['title'] = '';
}

// Item
$el = $props['item_element'] ? $this->el($props['item_element']) : null;

// Image
$image = $this->render("{$__dir}/template-image", compact('props'));

// Image Align
$grid = $this->el('div', [

    'class' => [
        'uk-child-width-expand',
        $element['image_grid_column_gap'] == $element['image_grid_row_gap'] ? 'uk-grid-{image_grid_column_gap}' : '[uk-grid-column-{image_grid_column_gap}] [uk-grid-row-{image_grid_row_gap}]',
        'uk-flex-middle {@image_vertical_align}',
    ],

    'uk-grid' => true,
]);

$cell_image = $this->el('div', [

    'class' => [
        'uk-width-{image_grid_width}@{image_grid_breakpoint}',
        'uk-flex-last@{image_grid_breakpoint} {@image_align: right}',
    ],

]);

$cell_content = $this->el('div', [

    'class' => [
        'uk-margin-remove-first-child',
    ],

]);

?>

<?php if ($el) : ?>
<?= $el($element) ?>
<?php endif ?>

<?php if ($image && in_array($element['image_align'], ['left', 'right'])) : ?>

    <?= $grid($element) ?>
        <?= $cell_image($element, $image) ?>
        <?= $cell_content($element, $this->render("{$__dir}/template-content", compact('props'))) ?>
    <?= $grid->end() ?>

<?php else : ?>

    <?= $element['image_align'] == 'top' ? $image : '' ?>
    <?= $this->render("{$__dir}/template-content", compact('props')) ?>
    <?= $element['image_align'] == 'bottom' ? $image : '' ?>

<?php endif ?>

<?php if ($el) : ?>
<?= $el->end() ?>
<?php endif ?>
