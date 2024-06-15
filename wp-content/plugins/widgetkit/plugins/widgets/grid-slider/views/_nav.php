<?php

// Nav
$nav = '';
$nav_item = '';

switch ($settings['nav']) {
    case 'dotnav':
        $nav  = '{wk}-dotnav';
        break;
    case 'thumbnails':
        $nav = '{wk}-thumbnav';
        $nav_item = ($settings['nav_align'] == 'justify') ? ' class="{wk}-width-1-' . count($items) . '"' : '';
        break;
}

// Alignment
$nav .= ($settings['nav_align'] != 'justify') ? ' {wk}-flex-' . $settings['nav_align'] : '';

?>

<?php if ($settings['nav'] != 'none') : ?>
<ul class="<?= $nav ?>" {wk}-margin>
<?php foreach ($fields as $i => $field) :

        // Thumbnails
        $thumbnail = '';
        if ($settings['nav'] == 'thumbnails' &&  ($item->type($field) == 'image' || $item[$field . '.poster'])) {

            $attrs           = array();
            $width           = ($settings['thumbnail_width'] != 'auto') ? $settings['thumbnail_width'] : $item[$field . '.width'];
            $height          = ($settings['thumbnail_height'] != 'auto') ? $settings['thumbnail_height'] : $item[$field . '.height'];

            $attrs['alt']    = strip_tags($item['title']);
            $attrs['width']  = $width;
            $attrs['height'] = $height;

            if ($settings['thumbnail_width'] != 'auto' || $settings['thumbnail_height'] != 'auto') {
                $thumbnail = $item->thumbnail($item->type($field) == 'image' ? $field : $field . '.poster', $width, $height, $attrs);
            } else {
                $thumbnail = $item->media($item->type($field) == 'image' ? $field : $field . '.poster', $attrs);
            }
        }

        ?>
    <li<?= $nav_item ?> {wk}-slideshow-item="<?= $i ?>"><a href="#"><?= $thumbnail ?: $item['title'] ?></a></li>
<?php endforeach ?>
</ul>
<?php endif ?>
