<?php

// JS Options
$options = array();
$options[] = ($settings['viewport'] !== '') ? 'viewport: ' . $settings['viewport'] : '';
$options[] = ($settings['velocity'] !== '') ? 'easing: ' . $settings['velocity'] : '';

$options_bg = array();
$options_bg[] = ($settings['background_translatey'] !== '') ? 'bgy: ' . $settings['background_translatey'] : '';
$options_bg[] = ($settings['background_color_start'] && $settings['background_color_end']) ? 'background-color: ' . $settings['background_color_start'] . ',' . $settings['background_color_end'] : '';
$options_bg[] =  (
                trim($settings['media_query'])
                ? 'media:'.(is_numeric($settings['media_query']) ? $settings['media_query'] : "'" . $settings['media_query'] . "'")
                : ''
);

$options_title = array();
$options_title[] = ($settings['title_opacity_start'] !== '' && $settings['title_opacity_end'] !== '') ? 'opacity: ' . $settings['title_opacity_start'] . ',' . $settings['title_opacity_end'] : '';
$options_title[] = ($settings['title_translatex_start'] !== '' && $settings['title_translatex_end'] !== '') ? 'x: ' . $settings['title_translatex_start'] . ',' . $settings['title_translatex_end'] : '';
$options_title[] = ($settings['title_translatey_start'] !== '' && $settings['title_translatey_end'] !== '') ? 'y: ' . $settings['title_translatey_start'] . ',' . $settings['title_translatey_end'] : '';
$options_title[] = ($settings['title_scale_start'] !== '' && $settings['title_scale_end'] !== '') ? 'scale: ' . $settings['title_scale_start'] . ',' . $settings['title_scale_end'] : '';

$options_content = array();
$options_content[] = ($settings['content_opacity_start'] !== '' && $settings['content_opacity_end'] !== '') ? 'opacity: ' . $settings['content_opacity_start'] . ',' . $settings['content_opacity_end'] : '';
$options_content[] = ($settings['content_translatex_start'] !== '' && $settings['content_translatex_end'] !== '') ? 'x: ' . $settings['content_translatex_start'] . ',' . $settings['content_translatex_end'] : '';
$options_content[] = ($settings['content_translatey_start'] !== '' && $settings['content_translatey_end'] !== '') ? 'y: ' . $settings['content_translatey_start'] . ',' . $settings['content_translatey_end'] : '';
$options_content[] = ($settings['content_scale_start'] !== '' && $settings['content_scale_end'] !== '') ? 'scale: ' . $settings['content_scale_start'] . ',' . $settings['content_scale_end'] : '';

// Container
$container  = '{wk}-flex {wk}-flex-center {wk}-flex-middle {wk}-overflow-hidden';
$container .= ' {wk}-text-'.$settings['text_align'];
$container .= $settings['contrast'] ? ' {wk}-light' : '';
$container .= $settings['fullscreen'] ? ' {wk}-height-viewport' : '';

// Width
$width = '{wk}-width-'.$settings['width'];
$width .= $settings['width_small'] ? ' {wk}-width-'.$settings['width_small'].'@s' : '';
$width .= $settings['width_medium'] ? ' {wk}-width-'.$settings['width_medium'].'@m' : '';
$width .= $settings['width_large'] ? ' {wk}-width-'.$settings['width_large'].'@l' : '';

// Title Size
switch ($settings['title_size']) {
    case 'medium':
        $title_size = '{wk}-heading-medium {wk}-margin-remove-top';
        break;
    default:
        $title_size = '{wk}-' . $settings['title_size'] . ' {wk}-margin-remove-top';
}

// Content Size
switch ($settings['content_size']) {
    case 'large':
        $content_size = '{wk}-text-large';
        break;
    case 'h1':
    case 'h2':
    case 'h3':
    case 'h4':
    case 'h5':
    case 'h6':
        $content_size = '{wk}-' . $settings['content_size'];
        break;
    default:
        $content_size = '';
}

// Link Style
switch ($settings['link_style']) {
    case 'button':
        $link_style = '{wk}-button {wk}-button-default';
        break;
    case 'primary':
        $link_style = '{wk}-button {wk}-button-primary';
        break;
    case 'button-large':
        $link_style = '{wk}-button {wk}-button-large {wk}-button-default';
        break;
    case 'primary-large':
        $link_style = '{wk}-button {wk}-button-large {wk}-button-primary';
        break;
    case 'button-link':
        $link_style = '{wk}-button {wk}-button-link';
        break;
    default:
        $link_style = '';
}

// Link Target
$link_target = ($settings['link_target']) ? ' target="_blank"' : '';

foreach ($items as $i => $item) :

    // Media Type
    $media = '';

    if ($item->type('media') == 'image' && $settings['media']) {
        if ($settings['image_width'] != 'auto' || $settings['image_height'] != 'auto') {
            $img_width  = ($settings['image_width'] != 'auto') ? $settings['image_width'] : '';
            $img_height = ($settings['image_height'] != 'auto') ? $settings['image_height'] : '';

            $media = 'background-image: url(\'' . $item->thumbnail('media', $img_width, $img_height, array(), true) . '\');';
        }
        elseif ($media = $item->get('media')) {

            if ($img = $app['image']->create($media)) {
                $media = 'background-image: url(\'' . $img->getURL() . '\');';
            }
            else {
                $media = 'background-image: url(\'' . $media . '\');';
            }

        }
    }

    // `min-height` doesn't work in IE11 and IE10 if flex items are centered vertically
    $media = 'style="height: ' . $settings['min_height'] . 'px; ' . $media . '"';

    // Id
    $id = substr(uniqid(), -3);
    $target = ($settings['target']) ? 'target: #wk-' . $id . '; ': '';

    $parallax_bg      = implode(';', array_filter($options_bg));
    $parallax_title   = $target . implode(';', array_filter(array_merge($options, $options_title)));
    $parallax_content = $target . implode(';', array_filter(array_merge($options, $options_content)));

?>

    <div id="wk-<?= $id ?>" class="<?= $container ?> <?= $settings['class'] ?>" <?= $media ?> {wk}-parallax="<?= $parallax_bg ?>">
        <div class="<?= $width ?> {wk}-panel">

            <?php if ($item['title'] && $settings['title']) : ?>
            <<?= $settings['title_element'] ?> class="<?= $title_size ?> {wk}-margin-remove-top" {wk}-parallax="<?= $parallax_title ?>">

                <?php if ($item['link']) : ?>
                    <a class="{wk}-link-reset" href="<?= $item->escape('link') ?>"<?= $link_target ?>><?= $item['title'] ?></a>
                <?php else : ?>
                    <?= $item['title'] ?>
                <?php endif ?>

            </<?= $settings['title_element'] ?>>
            <?php endif ?>

            <?php if (($item['content'] && $settings['content']) || ($item['link'] && $settings['link'])) : ?>
            <div {wk}-parallax="<?= $parallax_content ?>">

                <?php if ($item['content'] && $settings['content']) : ?>
                <div class="<?= $content_size ?>"><?= $item['content'] ?></div>
                <?php endif ?>

                <?php if ($item['link'] && $settings['link']) : ?>
                <p class="{wk}-margin-remove-bottom"><a<?php if($link_style) echo ' class="' . $link_style . '"' ?> href="<?= $item->escape('link') ?>"<?= $link_target ?>><?= $app['translator']->trans($settings['link_text']) ?></a></p>
                <?php endif ?>

            </div>
            <?php endif ?>

        </div>
    </div>

<?php endforeach ?>
