<?php

namespace MultilingualMenuDuplication\Admin;

class API
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'endpoint']);
    }

    public function endpoint()
    {
        register_rest_route('multilingualmenuduplication/v1', '/schedule/', [
            'methods' => 'POST',
            'callback' => [$this, 'addToQueue'],
        ]);
    }

    public function addToQueue($request)
    {
        $params = $request->get_params();
        \InvezzPlugin\Log\Log::log(
            'API request',
            serialize($request)
        );
        $sourceLang = sanitize_text_field($params['sourceLang']);
        $destLang = sanitize_text_field($params['destLang']);
        $menuIds = array_map('intval', $params['menus']);
        
        if (!$sourceLang || !$destLang || !$menuIds) {
            return false;
        }
        
        $totalMenus = count($menuIds);
        $user = wp_get_current_user();

        $date = new \DateTime();
        $now = $date->getTimestamp();

        foreach ($menuIds as $menuId) {
            if (wp_next_scheduled('translate_menu', [
                $sourceLang,
                $destLang,
                $menuId,
            ])) {
                // Already scheduled
                continue;
            }
    
            $now = $now + 10;
            wp_schedule_single_event(
                $now,
                'translate_menu',
                [
                    $sourceLang,
                    $destLang,
                    $menuId,
                ]
            );
        }

        // Schedule the email notification on completion
        if (wp_next_scheduled('translate_menu_notify', [
            $totalMenus,
            $user,
        ])) {
            exit();
        }

        $now = $now + 30;
        wp_schedule_single_event(
            $now,
            'translate_menu_notify',
            [
                $totalMenus,
                $user,
            ]
        );

        exit();
    }
}
