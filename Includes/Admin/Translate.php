<?php

namespace MultilingualMenuDuplication\Admin;

use MultilingualMenuDuplication\Helpers\Slug;
use InvezzPlugin\Domain\Languages\GoogleTranslate;
use InvezzTheme\Domain\Cache\Transients;

class Translate
{
    public static function translateMenu($sourceLang, $destLang, $menuId)
    {
        if (!$sourceLang || !$destLang || !$menuId) {
            return;
        }

        $menuObj = wp_get_nav_menu_object($menuId);
        $menuItems = wp_get_nav_menu_items($menuId);
        $translator = new GoogleTranslate($destLang);
        
        $menuName = $menuObj->name;

        $newMenuName = false;

        $previouslyExistingMenu = false;

        if (strpos($menuName, strtoupper($sourceLang)) !== false) {
            // If current nav name already has a language code, replace it
            $newMenuName = str_replace(strtoupper($sourceLang), strtoupper($destLang), $menuName);
        } else {
            // If not, append the language code
            $newMenuName = sprintf(
                '%s - %s',
                $menuName,
                strtoupper($destLang)
            );
        }

        if (is_nav_menu($newMenuName)) {
            // If the nav menu exists already, append the new menu name with a temp designator
            $newMenuName = $newMenuName.'-temp';
            $previouslyExistingMenu = true;
        }

        if (!$newMenuName) {
            return;
        }
        
        // Create the new nav menu
        $newMenuId = wp_create_nav_menu($newMenuName);

        // Copy menu meta fields
        self::menuMeta($menuObj, $newMenuId, $sourceLang, $destLang);

        // Translate each menu item and add to new menu
        $newMenuItems = [];
        foreach ($menuItems as $menuItem) {
            $postId = $menuItem->object_id;

            if (!$postId) {
                continue;
            }

            if ($menuItem->type === 'custom') {
                $itemUrl = $menuItem->url;
                
                if ($itemUrl !== '#') {
                    $itemUrl = str_replace(get_home_url(), '', $itemUrl);
                    $itemUrl = str_replace('https://invezz.com', '', $itemUrl);
                    $urlArr = explode('/', $itemUrl);

                    $lastFragment = array_pop($urlArr);
                    if ($lastFragment == '') {
                        $lastFragment = array_pop($urlArr);
                    }
                    
                    $itemUrl = Slug::getTranslatedSlug($lastFragment, $destLang);
                }

                $itemTitle = $menuItem->title;

                if ($itemTitle != 'INV_MORE') {
                    $itemTitle = $translator->fetchTranslation($itemTitle);
                }
                
                $newItemArgs = [
                    'menu-item-object-id' => $menuItem->ID,
                    'menu-item-object' => 'custom',
                    'menu-item-title' => $itemTitle,
                    'menu-item-url' => $itemUrl,
                    'menu-item-type' => 'custom',
                    'menu-item-status' => 'publish',
                ];

                /**
                 * If the old menu item has a parent, get the new parent item's ID
                 * from the new items array
                 * 
                 * Disable adding parents for all except secondary drop or main menus
                 */
                if (
                    (
                        (strpos($newMenuName, 'Drop') !== false && strpos($newMenuName, 'Secondary') !== false) ||
                        strpos($newMenuName, 'Main') !== false
                    ) &&
                    $menuItem->menu_item_parent &&
                    isset($newMenuItems[$menuItem->menu_item_parent])) {
                    $newItemArgs['menu-item-parent-id'] = $newMenuItems[$menuItem->menu_item_parent];
                }
                
                $newMenuItem = wp_update_nav_menu_item(
                    $newMenuId,
                    0,
                    $newItemArgs
                );

                $newMenuItems[$menuItem->ID] = $newMenuItem;

                self::menuItemMeta($menuItem, $newMenuItem, $sourceLang, $destLang);
                continue;
            }

            if ($menuItem->type === 'taxonomy' && function_exists('pll_get_term')) {
                $translated = pll_get_term($postId, $destLang);
                $translatedTerm = get_term($translated);

                if (!$translatedTerm) {
                    continue;
                }

                $newItemArgs = [
                    'menu-item-object-id' => $translatedTerm->term_id,
                    'menu-item-object' => $translatedTerm->taxonomy,
                    'menu-item-type' => 'taxonomy',
                    'menu-item-status' => 'publish',
                ];

                $newMenuItem = wp_update_nav_menu_item(
                    $newMenuId,
                    0,
                    $newItemArgs
                );

                $newMenuItems[$menuItem->ID] = $newMenuItem;
                self::menuItemMeta($menuItem, $newMenuItem, $sourceLang, $destLang);
                continue;
            }

            if (function_exists('pll_get_post')) {
                $translated = pll_get_post($postId, $destLang);
                $translatedPost = get_post($translated);

                if (!$translatedPost) {
                    continue;
                }

                $newItemArgs = [
                    'menu-item-object-id' => $translatedPost->ID,
                    'menu-item-object' => $translatedPost->post_type,
                    'menu-item-type' => 'post_type',
                    'menu-item-status' => 'publish',
                ];

                /**
                 * If the old menu item has a parent, get the new parent item's ID
                 * from the new items array
                 * 
                 * Disable adding parents for all except secondary drop or main menus
                 */
                if (
                    (
                        (strpos($newMenuName, 'Drop') !== false && strpos($newMenuName, 'Secondary') !== false) ||
                        strpos($newMenuName, 'Main') !== false
                    ) &&
                    $menuItem->menu_item_parent &&
                    isset($newMenuItems[$menuItem->menu_item_parent])) {
                    $newItemArgs['menu-item-parent-id'] = $newMenuItems[$menuItem->menu_item_parent];
                }
                
                $newMenuItem = wp_update_nav_menu_item(
                    $newMenuId,
                    0,
                    $newItemArgs
                );

                $newMenuItems[$menuItem->ID] = $newMenuItem;

                self::menuItemMeta($menuItem, $newMenuItem, $sourceLang, $destLang);
            }
        }

        if ($previouslyExistingMenu) {
            // Delete the old menu and rename the new, temporary menu
            $newMenuName = str_replace('-temp', '', $newMenuName);

            wp_delete_nav_menu($newMenuName);
            
            wp_update_term(
                $newMenuId,
                'nav_menu',
                [
                    'name' => $newMenuName,
                    'slug' => sanitize_title($newMenuName),
                ]
            );
        }

        if (strpos($newMenuName, 'Main') !== false) {
            // Assign the new Main menu to the nav menu location
            $locations = get_theme_mod('nav_menu_locations');
            $menuKey = ($destLang == 'en') ? 'header_menu' : 'header_menu___'.$destLang;
            $locations[$menuKey] = $newMenuId;
            set_theme_mod('nav_menu_locations', $locations);
            
            // Flush any caches by re-saving the menu
            wp_update_nav_menu_object($newMenuId);
        }

        // Clear transients
        $transientKeys = [
            'inv-navigation-',
            'menu-dropdown-',
        ];
        
        Transients::clear($transientKeys);
    }

    public static function menuItemMeta($menuItem, $newMenuItem, $sourceLang, $destLang)
    {
        $oldItemMeta = get_fields($menuItem->ID);

        // Update chosen submenu to new language version
        $oldItemMeta['choose_submenu'] = str_replace(
            $sourceLang,
            $destLang,
            $oldItemMeta['choose_submenu']
        );

        // Tidy old meta keys
        unset($oldItemMeta['section_color']);
        unset($oldItemMeta['submenu_heading']);
        unset($oldItemMeta['display_when_url_begins_with']);

        foreach ($oldItemMeta as $metaKey => $metaValue) {
            update_field($metaKey, $metaValue, $newMenuItem);
        }
    }

    public static function menuMeta($menuObj, $newMenuId, $sourceLang, $destLang)
    {
        $oldMenuMeta = get_fields($menuObj);
        $translator = new GoogleTranslate($destLang);

        if ($oldMenuMeta['submenu_heading'] != '') {
            $oldMenuMeta['submenu_heading'] = $translator->fetchTranslation(
                $oldMenuMeta['submenu_heading']
            );
        }
        
        if ($oldMenuMeta['on_page_nav_description'] != '') {
            $oldMenuMeta['on_page_nav_description'] = $translator->fetchTranslation(
                $oldMenuMeta['on_page_nav_description']
            );
        }

        $urlFragments = str_replace("\n", '', $oldMenuMeta['display_when_url_begins_with']);
        $urlFragments = str_replace("\r", '', $urlFragments);
        $urlFragments = str_replace("//", '/', $urlFragments);
        $urlFragmentsArr = explode('/', $urlFragments);
        foreach ($urlFragmentsArr as $key => $urlFragment) {
            if ($urlFragment == "") {
                unset($urlFragmentsArr[$key]);
                continue;
            }
            
            $translatedUrlFragment = Slug::getTranslatedSlug($urlFragment, $destLang);
            $urlFragmentsArr[$key] = $translatedUrlFragment;
        }

        $oldMenuMeta['display_when_url_begins_with'] = implode(PHP_EOL, $urlFragmentsArr);
        
        $newMenuObj = wp_get_nav_menu_object($newMenuId);
        foreach ($oldMenuMeta as $metaKey => $metaValue) {
            update_field($metaKey, $metaValue, $newMenuObj);
        }
    }
}
