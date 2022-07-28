<?php

namespace MultilingualMenuDuplication\Admin;

use \MultilingualMenuDuplication\Helpers\Language;
use \MultilingualMenuDuplication\Helpers\Menu as MenuHelper;

class Menu
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'registerMenu']);
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
}
