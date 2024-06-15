<div class="wrap">

    <h2>Widgetkit</h2>

    <form name="form"
          action="<?= add_query_arg(array('page' => $app['name'] . '-config', 'action' => 'save'), admin_url('options-general.php')) ?>"
          method="post">

        <h3>API Keys</h3>
        <table class="form-table">
            <tr>
                <th><?= $app['translator']->trans('YOOtheme API Key') ?></th>
                <td>
                    <label for="config-apikey">
                        <input id="config-apikey" class="regular-text" type="text" name="config[apikey]"
                               value="<?= $app['config']->get('apikey') ?>">
                    </label>

                    <p class="description"><?= $app['translator']->trans('In order to update commercial extensions set up your API Key which can be found in your %link% account.', array('%link%' => '<a href="http://yootheme.com/account" target="_blank">YOOtheme</a>')) ?></p>
                </td>
            </tr>
            <tr>
                <th><?= $app['translator']->trans('Google Maps API Key') ?></th>
                <td>
                    <label for="google-maps-apikey">
                        <input id="google-maps-apikey" class="regular-text" type="text" name="config[googlemapseapikey]"
                               value="<?= $app['config']->get('googlemapseapikey') ?>">
                    </label>

                    <p class="description"><?= $app['translator']->trans('Custom API Key generated in your %link% account.', array('%link%' => '<a href="https://console.developers.google.com/apis" target="_blank">Google API Console</a>')) ?></p>
                </td>
            </tr>
        </table>

        <h3>UIkit</h3>
        <table class="form-table">
            <tr>
                <th><?= $app['translator']->trans('3rd Party Theme') ?></th>
                <td>
                    <label for="theme-support">
                        <select id="theme-support" name="config[theme.support]">
                            <option value="scoped"<?= !$app['config']->get('theme.support') || $app['config']->get('theme.support') == 'scoped' ? 'selected' : '' ?>><?= $app['translator']->trans('Load UIkit 3 with Scoped CSS and uk- Prefix (Theme doesn\'t use UIkit)') ?></option>
                            <option value="noconflict"<?= $app['config']->get('theme.support') == 'noconflict' ? 'selected' : '' ?>><?= $app['translator']->trans('Load UIkit 3 with Scoped CSS and wk- Prefix (Theme uses UIkit 2)') ?></option>
                            <option value="uikit3"<?= $app['config']->get('theme.support') == 'uikit3' ? 'selected' : '' ?>><?= $app['translator']->trans('Don\'t load UIkit (Theme already uses UIkit 3)') ?></option>
                        </select>
                    </label>

                    <p class="description"><?= $app['translator']->trans('If instead of YOOtheme Pro or Warp 7 a 3rd party theme is used, select whether the theme uses UIkit 3, UIkit 2 or no UIkit at all.') ?></p>
                </td>
            </tr>
        </table>

        <h3>Editor</h3>
        <table class="form-table">
            <tr>
                <th><?= $app['translator']->trans('System Editor') ?></th>
                <td>
                    <label for="config-editor">
                        <input id="config-editor" name="config[system_editor]" type="checkbox" value="1" <?php checked('1', $app['config']->get('system_editor')) ?>>
                        <?= $app['translator']->trans('Enable the Visual Editor for Widgetkit content fields.') ?>
                    </label>
                </td>
            </tr>
        </table>

        <h3>Cache</h3>

        <table class="form-table">
            <tr>
                <th><?= $app['translator']->trans('Clear Widgetkit Cache') ?></th>
                <td width="10">
                    <button id="wk-clear-cache" type="button" class="button">Clear Cache</button>
                </td>
                <td>
                    <p class="description wk-cache-size"></p>
                </td>
            </tr>
        </table>

        <?php wp_nonce_field() ?>
        <?php submit_button() ?>
    </form>
</div>

<script>
jQuery(function($) {

    var getCache = function() {

        $.get('admin-ajax.php?action=widgetkit&p=/cache/get', function(data) {
            $('.wk-cache-size').text(JSON.parse(data));
        });
    }

    $('#wk-clear-cache').on('click', function(e) {
        e.preventDefault();
        $('.wk-cache-size').text('<?= $app['translator']->trans('Clearing cache...') ?>');
        $.get('admin-ajax.php?action=widgetkit&p=/cache/clear', getCache);
    });

    getCache();

});
</script>
