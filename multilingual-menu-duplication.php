<?php
/**
 * Plugin Name:       Multilingual menu duplication
 * Plugin URI:        
 * Description:       Requires a multilanguage plugin such as Polylang. Allows you to duplicate a menu from one language into another, with all menu links being replaced with the appropriate translated page.
 * Version:           1.0.1
 * Requires at least: 5.0.0
 * Requires PHP:      7.0
 * Author:            Mike Bricknell-Barlow
 * Author URI:        https://bricknellbarlow.co.uk
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       multilingual-menu-duplication
*/

define('MULTILINGUAL_MENU_DUPLICATION_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('MULTILINGUAL_MENU_DUPLICATION_PLUGIN_DIR_PATH', dirname(__FILE__));

require_once 'Includes'.DIRECTORY_SEPARATOR.'Bootstrap.php';
new MultilingualMenuDuplication\Bootstrap;
