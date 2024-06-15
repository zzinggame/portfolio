<?php

namespace YOOtheme\Widgetkit;

class WidgetkitBlock
{
	protected $name = 'widgetkit';
	protected $namespace = 'yootheme';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		add_action('enqueue_block_editor_assets', [$this, 'registerEditorAssets']);
	}

	/**
	 * Registers the block type with WordPress.
	 */
	public function register()
	{
		$prefix = "{$this->namespace}-{$this->name}";

		register_block_type("{$this->namespace}/{$this->name}", [
			'editor_script' => "{$prefix}-block-editor",
		]);
	}

	/**
	 * Register assets used for rendering the block in editor context.
	 */
	public function registerEditorAssets()
	{
	    $app = Application::getInstance();
		$script = 'assets/js/wordpress.block.js';
		$prefix = "{$this->namespace}-{$this->name}";

		wp_register_script("{$prefix}-block-editor", plugins_url($script, __FILE__), ['wp-block-editor', 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-polyfill'], filemtime(__DIR__ . "/{$script}"));

		add_thickbox();
        wp_localize_script("{$prefix}-block-editor", 'widgetkit', ['iframe' => $app['url']->route('/picker', ['action' => 'widgetkit', 'width' => '1000', 'TB_iframe' => 'true'])]);
	}
}
