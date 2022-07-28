<?php

namespace MultilingualMenuDuplication;

use \MultilingualMenuDuplication\Admin\API;
use \MultilingualMenuDuplication\Admin\Menu;
use \MultilingualMenuDuplication\Admin\Schedule;
use \MultilingualMenuDuplication\Admin\Translate;

class Bootstrap
{
    public function __construct()
    {
        require_once MULTILINGUAL_MENU_DUPLICATION_PLUGIN_DIR_PATH.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
        new API();
        new Menu();
        new Schedule();
        new Translate();
    }
}
