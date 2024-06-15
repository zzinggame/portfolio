<?php

// Display
$hasContent = false;
foreach (['title', 'meta', 'content', 'image', 'link'] as $key) {
    if (!$element["show_{$key}"]) {
        $props[$key] = '';
    }
    $hasContent = $hasContent || $props[$key];
}

if (!$hasContent) {
    // Do not render the marker content, but the marker itself is still rendered
    return;
}

// Image
$props['image'] = $this->render("{$__dir}/template-image", compact('props'));

// Item
$el = $this->el($props['item_element'] ?: 'div', [
    'class' => [
        'el-item',
        'uk-text-default uk-font-default', // Reset Google Maps Style
        'uk-text-{item_text_align}{@item_text_align: justify}',
        'uk-text-{item_text_align}[@{item_text_align_breakpoint} [uk-text-{item_text_align_fallback}]] {@!item_text_align: justify}',
        'uk-margin-remove-first-child',
    ],
]);

// Link
$link = include "{$__dir}/template-link.php";

?>

<?= $el($element) ?>

    <?= $props['image'] ?>

    <?= $this->render("{$__dir}/template-content", compact('props', 'link')) ?>

<?= $el->end() ?>
