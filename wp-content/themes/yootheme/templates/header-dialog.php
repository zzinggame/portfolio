<?php


// Toggle
$attrs_toggle = ['uk-toggle' => true];

if (in_array($position, ['navbar', 'navbar-push', 'header', 'header-push'])) {

    // Config
    $header = '~theme.header';
    $dialog = '~theme.dialog';

    $countModules = is_active_sidebar('dialog') || str_starts_with($config("$header.search", ''), 'dialog') || str_starts_with($config("$header.social", ''), 'dialog');

    $attrs_toggle['href'] ='#tm-dialog';

    if (str_starts_with($config("$header.layout"), 'stacked') && in_array($position, ['header', 'header-push'])) {
        $attrs_toggle['class'][] = 'uk-icon-link';
    } else {
        $attrs_toggle['class'][] = 'uk-navbar-toggle';
    }
    $attrs_toggle['class'][] = str_starts_with($config("$dialog.layout"), 'dropbar') ? 'uk-navbar-toggle-animate' : '';

} elseif (in_array($position, ['navbar-mobile', 'header-mobile'])) {

    // Config
    $header = '~theme.mobile.header';
    $dialog = '~theme.mobile.dialog';

    $countModules = is_active_sidebar('dialog-mobile') || str_starts_with($config("$header.search", ''), 'dialog') || str_starts_with($config("$header.social", ''), 'dialog');

    $attrs_toggle['href'] ='#tm-dialog-mobile';
    $attrs_toggle['class'][] = 'uk-navbar-toggle';
    $attrs_toggle['class'][] = str_starts_with($config("$dialog.layout"), 'dropbar') ? 'uk-navbar-toggle-animate' : '';

}

?>

<?php // Mind that `is_active_sidebar()` does not count the custom created modules (logo, search, socials) because this file is called from the module/widget listener ?>
<?php if ($countModules) : ?>

    <a<?= $this->attrs($attrs_toggle) ?>>

        <?php if ($config("$dialog.toggle_text") && explode(':', $config("$dialog.toggle"), 2)[1] == 'end') : ?>
        <span class="uk-margin-small-right uk-text-middle"><?= __('Menu', 'yootheme') ?></span>
        <?php endif ?>

        <div uk-navbar-toggle-icon></div>

        <?php if ($config("$dialog.toggle_text") && explode(':', $config("$dialog.toggle"), 2)[1] == 'start') : ?>
        <span class="uk-margin-small-left uk-text-middle"><?= __('Menu', 'yootheme') ?></span>
        <?php endif ?>

    </a>

<?php endif ?>
