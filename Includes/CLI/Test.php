<?php

namespace MultilingualMenuDuplication\CLI;

use MultilingualMenuDuplication\Admin\Translate;
use WP_CLI;

class Test
{
	public function __construct()
	{
		add_action('init', [$this, 'registerCommand']);
	}

	public function registerCommand()
	{
		if (!defined('WP_CLI') || !WP_CLI) {
			return;
		}

		WP_CLI::add_command('test-menu-duplication', [$this, 'callback']);
	}

	public function callback($args = [])
	{
		if (!isset($args[0])) {
			WP_CLI::error('Please specify a menu ID');
			return;
		}

		$menuId = absint($args[0]);

		if (!isset($args[1])) {
			WP_CLI::error('Please specify a target language');
			return;
		}

		$targetLang = sanitize_text_field($args[1]);

		Translate::translateMenu('en', $targetLang, $menuId);
	}
}