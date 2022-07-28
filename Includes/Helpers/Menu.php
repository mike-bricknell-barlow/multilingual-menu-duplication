<?php

namespace MultilingualMenuDuplication\Helpers;

class Menu
{
    public static function getMenus()
    {
        return wp_get_nav_menus();
    }
}
