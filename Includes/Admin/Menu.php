<?php

namespace MultilingualMenuDuplication\Admin;

use \MultilingualMenuDuplication\Helpers\Language;
use \MultilingualMenuDuplication\Helpers\Menu as MenuHelper;
use InvezzTheme\Domain\Cache\Transients;

class Menu
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'registerMenu']);
        add_action('admin_bar_menu', [$this, 'clearNavCacheButton'], 10000);
        add_action('wp_ajax_inv_purge_nav', [$this, 'purgeNav']);
    }

    public function registerMenu()
    {
        add_theme_page(
            'Multi-lingual menu duplicator',
            'Multi-lingual menu duplicator',
            'manage_options',
            'multilingual-menu-duplication',
            [$this, 'outputMenuContent']
        );
    }

    public function enqueue()
    {
        wp_enqueue_style(
            'mmd-admin',
            MULTILINGUAL_MENU_DUPLICATION_PLUGIN_DIR_URL.'/Dist/menu.css',
            [],
            filemtime(
                MULTILINGUAL_MENU_DUPLICATION_PLUGIN_DIR_PATH.'/Dist/menu.css',
            )
        );

        wp_enqueue_script(
            'mmd-admin',
            MULTILINGUAL_MENU_DUPLICATION_PLUGIN_DIR_URL.'/Dist/menu.js',
            [],
            filemtime(
                MULTILINGUAL_MENU_DUPLICATION_PLUGIN_DIR_PATH.'/Dist/menu.js',
            )
        );
    }

    public function outputMenuContent()
    {
        $this->enqueue();
        $languages = Language::getLanguages();
        $menus = MenuHelper::getMenus();
        
        include MULTILINGUAL_MENU_DUPLICATION_PLUGIN_DIR_PATH.
            DIRECTORY_SEPARATOR.
            'Templates'.
            DIRECTORY_SEPARATOR.
            'Admin'.
            DIRECTORY_SEPARATOR.
            'menu.php';
    }

    public function clearNavCacheButton($wp_admin_bar)
    {
        if (!current_user_can('manage_options')) {
            return $wp_admin_bar;
        }

        $wp_admin_bar->add_menu([
            'id' => 'inv_purge_nav',
            'parent' => 'cache',
            'group' => null,
            'title' => 'Purge nav menus',
            'href'  => admin_url('/'),
            'meta' => [
                'class' => 'ajax',
            ]
        ]);
        
        return $wp_admin_bar;
    }

    public function purgeNav()
    {
        $transientKeys = [
            'inv-navigation-',
            'menu-dropdown-',
        ];
        
        Transients::clear($transientKeys);

        echo 'Success';
        die();
    }
}
