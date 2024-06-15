<?php
// TODO Remove later
$props['video'] = '';
$props['hover_image'] = '';
$props['hover_video'] = '';
?>

<?php if ($props['image'] || $props['video'] || $props['hover_image'] || $props['hover_video'] || $props['title'] || $props['meta'] || $props['content'] || $props['link']) : ?>
<div>

    <?php if ($props['image']) : ?>
    <img src="<?= $props['image'] ?>" alt="<?= $props['image_alt'] ?>">
    <?php endif ?>

    <?php if ($props['video']) : ?>
        <?php if ($this->iframeVideo($props['video'], [], false)) : ?>
        <iframe src="<?= $props['video'] ?>"></iframe>
        <?php else : ?>
        <video src="<?= $props['video'] ?>"></video>
        <?php endif ?>
    <?php endif ?>

    <?php if ($props['hover_image']) : ?>
    <img src="<?= $props['hover_image'] ?>" alt>
    <?php endif ?>

    <?php if ($props['hover_video']) : ?>
        <?php if ($this->iframeVideo($props['hover_video'], [], false)) : ?>
        <iframe src="<?= $props['hover_video'] ?>"></iframe>
        <?php else : ?>
        <video src="<?= $props['hover_video'] ?>"></video>
        <?php endif ?>
    <?php endif ?>

    <?php if ($props['title']) : ?>
    <<?= $props['title_element'] ?>><?= $props['title'] ?></<?= $props['title_element'] ?>>
    <?php endif ?>

    <?php if ($props['meta']) : ?>
    <p><?= $props['meta'] ?></p>
    <?php endif ?>

    <?php if ($props['content']) : ?>
    <div><?= $props['content'] ?></div>
    <?php endif ?>

    <?php if ($props['link']) : ?>
    <p><a href="<?= $props['link'] ?>"><?= $props['link_text'] ?></a></p>
    <?php endif ?>

</div>
<?php endif ?>
