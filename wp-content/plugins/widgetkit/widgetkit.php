<?php

/*
    Plugin Name: Widgetkit
    Plugin URI: http://www.yootheme.com/widgetkit
    Description: A widget toolkit by YOOtheme.
    Version: 3.1.27
    Author: YOOtheme
    Author URI: https://yootheme.com
    License: GNU General Public License v2 or later
*/

use YOOtheme\Framework\Wordpress\Option;
use YOOtheme\Widgetkit\Application;
use YOOtheme\Widgetkit\WidgetkitBlock;

$loader = require __DIR__ . '/vendor/autoload.php';
$config = require __DIR__ . '/config.php';

$app = new Application($config);
$app['autoloader'] = $loader;
$app['templates'] = function () {
    $dirs = [];

    foreach (array_unique([get_template_directory(), get_stylesheet_directory()]) as $dir) {
        if (file_exists($dir = "{$dir}/widgetkit")) {
            $dirs[] = $dir;
        }
    }

    return $dirs;
};
$app['option'] = function () {
    return new Option('widgetkit-option');
};

$app->boot();

$app->on('init', function ($event, $app) {
    // set the API Keys
    $app['config']->set('apikey', get_option('yootheme_apikey'));
    $app['config']->set('googlemapseapikey', get_option('yootheme_googlemapseapikey'));

    // system editor setting
    $app['config']->set('system_editor', get_option('yootheme_system_editor'));

    // 3rd party theme setting
    $app['config']->set('theme.support', get_option('yootheme_theme_support'));

    // init 1click Update
    $app['update']->register('widgetkit', 'plugin', 'https://yootheme.com/api/update/widgetkit_wp', [
        'key' => $app['config']->get('apikey'),
    ]);

    // set theme support
    if (!$app['admin']) {
        if (current_theme_supports('uikit3')) {
            $app['config']->set('theme.support', 'uikit3');
        } elseif (
            current_theme_supports('widgetkit') ||
            current_theme_supports('widgetkit-noconflict')
        ) {
            $app['config']->set('theme.support', 'noconflict');
        } elseif (!$app['config']->get('theme.support')) {
            $app['config']->set('theme.support', 'scoped');
        }
    }
});

$app->on('view', function ($event, $app) {
    // add to admin styles
    if ($app['admin']) {
        $app['styles']->add('widgetkit-wordpress', 'assets/css/wordpress.css');
    }
});

$app->on('response', function ($event, $app) {
    if ($event['request']->get('p') == '/picker') {
        $event['response'] = $app['view']->render('views/doc.php', [
            'output' => (string) $event['response'],
        ]);
    }
});

$app->on('init.admin', function ($event, $app) {
    $app['scripts']->add('widgetkit-wordpress', 'assets/js/wordpress.js', ['widgetkit-admin']);

    add_action('admin_enqueue_scripts', function () {
        wp_enqueue_media();
        if (is_callable('wp_enqueue_editor')) {
            wp_enqueue_editor();
        }
    });
});

// init on admin ajax
add_action('admin_init', function () use ($app) {
    $app['config']->set('settings-page', admin_url('options-general.php?page=widgetkit-config'));

    if (
        defined('DOING_AJAX') &&
        $app['request']->get('action') == $app['name']
    ) {
        $app->trigger('init.admin', [$app]);
    }
});

// init on certain admin screens
add_action('current_screen', function ($screen) use ($app) {
    if ($screen->base == 'toplevel_page_widgetkit') {
        $app->trigger('init.admin', [$app]);
    }
});

// add to admin menu
add_action('admin_menu', function () use ($app) {
    add_action('load-toplevel_page_widgetkit', function () use ($app) {
        $response = $app->handle(null, false);

        add_action('toplevel_page_widgetkit', function () use ($response) {
            $response->send();
        });
    });

    add_action('load-settings_page_' . $app['name'] . '-config', function () use ($app) {
        if (
            $app['request']->get('action') === 'save' and
            wp_verify_nonce($app['request']->get('_wpnonce'))
        ) {
            $config = $app['request']->get('config', []);

            // save the YOOtheme API key outside the config
            if (isset($config['apikey'])) {
                update_option('yootheme_apikey', trim($config['apikey']));
            }

            // save the Google API key outside the config
            if (isset($config['googlemapseapikey'])) {
                update_option('yootheme_googlemapseapikey', trim($config['googlemapseapikey']));
            }

            // save editor setting
            if (isset($config['system_editor'])) {
                update_option('yootheme_system_editor', $config['system_editor']);
            } else {
                update_option('yootheme_system_editor', 0);
            }

            // save 3rd party theme setting
            if (isset($config['theme.support'])) {
                update_option('yootheme_theme_support', $config['theme.support']);
            }

            $app['config']->set('apikey', get_option('yootheme_apikey'));
            $app['config']->set('googlemapseapikey', get_option('yootheme_googlemapseapikey'));
            $app['config']->set('system_editor', get_option('yootheme_system_editor'));
            $app['config']->set('theme.support', get_option('yootheme_theme_support'));
        }
    });

    add_menu_page(
        'Widgetkit',
        'Widgetkit',
        'manage_widgetkit',
        $app['name'],
        function () {},
        'dashicons-admin-widgetkit',
        '50'
    );

    add_options_page(
        'Widgetkit Settings',
        'Widgetkit',
        'manage_options',
        $app['name'] . '-config',
        function () use ($app) {
            require __DIR__ . '/widgetkit-config.php';
        }
    );
});

// add settings link
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) use ($app) {
    array_unshift(
        $links,
        '<a href="' .
            admin_url('options-general.php?page=widgetkit-config') .
            '">' .
            $app['translator']->trans('Settings') .
            '</a>'
    );
    return $links;
});

// add Widget media buttons
$addMediaButtonAssets = function () use ($app) {
    add_thickbox();
    wp_enqueue_script(
        'widgetkit-widget-picker',
        $app['url']->to('assets/js/wordpress.picker.js', [], true),
        [],
        true
    );
    wp_localize_script('widgetkit-widget-picker', 'widgetkit', [
        'iframe' => $app['url']->route('/picker', [
            'action' => 'widgetkit',
            'width' => '1000',
            'TB_iframe' => 'true',
        ]),
    ]);
};

$addMediaButton = function () use ($app) {
    $title = $app['translator']->trans('Add Widget');
    echo '<a href="" class="button add_media widgetkit-editor" title="' .
        $title .
        '"><span></span> Widgetkit</a>';
};

add_action('media_buttons', $addMediaButtonAssets);
add_action('media_buttons', $addMediaButton);
add_action('admin_print_scripts-widgets.php', $addMediaButtonAssets);

add_action(
    'in_widget_form',
    function ($widget, $return, $instance) use ($app, $addMediaButton) {
        if ($widget->id_base == 'text' && !$widget->updated) {
            $addMediaButton();
        }
    },
    10,
    3
);

// add shortcode
add_shortcode('widgetkit', function ($attrs, $content, $code) use ($app) {
    return $app->renderWidget($attrs);
});

// add widget
add_action('widgets_init', function () {
    require_once __DIR__ . '/widgetkit-widget.php';
    register_widget('WP_Widget_Widgetkit');
});

// add widget block
add_action('init', function () {
    require_once __DIR__ . '/widgetkit-block.php';
    call_user_func([new WidgetkitBlock(), 'register']);
});

// apply shortcodes in text widgets
add_filter('widget_text', 'do_shortcode');

// enable svg upload
add_filter('upload_mimes', function ($mimes) {
    $mimes['svg|svgz'] = 'image/svg+xml';
    return $mimes;
});

add_filter(
    'wp_check_filetype_and_ext',
    function ($data, $file, $filename, $mimes) {
        if (empty($data['type']) && substr($filename, -4) === '.svg') {
            $data['ext'] = 'svg';
            $data['type'] = 'image/svg+xml';
        }

        return $data;
    },
    10,
    4
);

$roles = ['administrator', 'editor', 'author'];

// add activation hook
register_activation_hook(__FILE__, function () use ($app, $roles) {
    $oldVersion = get_option('widgetkit.version');

    if ($oldVersion && version_compare($oldVersion, '2.2.0', '<')) {
        $update = require $app['path'] . '/updates/2.2.0.php';
        $update->run($app);
    }

    if ($oldVersion && version_compare($oldVersion, '3.0.0-beta.0.2', '<')) {
        $update = require $app['path'] . '/updates/3.0.0-beta.0.2.php';
        $update->run($app);
    }

    foreach ($roles as $name) {
        get_role($name)->add_cap('manage_widgetkit');
    }

    $app->install();

    update_option('widgetkit.version', '3.0.13');
});

// add deactivation hook
register_deactivation_hook(__FILE__, function () use ($app, $roles) {
    foreach ($roles as $name) {
        get_role($name)->remove_cap('manage_widgetkit');
    }
});
