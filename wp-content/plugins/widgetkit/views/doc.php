<!DOCTYPE html>
<html <?php language_attributes() ?>>
    <head>
        <meta charset="<?php bloginfo('charset') ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php wp_enqueue_style('colors') ?>
        <?php wp_enqueue_style('ie') ?>
        <?php wp_enqueue_script('utils') ?>

        <?php do_action('admin_enqueue_scripts', 'toplevel_page_widgetkit') ?>
        <?php do_action('admin_print_styles-toplevel_page_widgetkit') ?>
        <?php do_action('admin_print_styles') ?>
        <?php do_action('admin_print_scripts-toplevel_page_widgetkit') ?>
        <?php do_action('admin_print_scripts') ?>
        <?php do_action('admin_head-toplevel_page_widgetkit') ?>
        <?php do_action('admin_head') ?>

    </head>
    <body>
        <?= $output ?>
        <?php do_action('admin_footer', '') ?>
        <?php do_action('admin_footer-toplevel_page_widgetkit') ?>
        <?php do_action('admin_print_footer_scripts-toplevel_page_widgetkit') ?>
        <?php do_action('admin_print_footer_scripts') ?>
        <?php do_action('admin_footer-toplevel_page_widgetkit') ?>
    </body>
</html>
