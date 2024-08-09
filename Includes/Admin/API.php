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
        $sourceLang = sanitize_text_field($params['sourceLang']);
        $destLang = sanitize_text_field($params['destLang']);
        $menuIds = array_map('intval', $params['menus']);
        
        if (!$sourceLang || !$destLang || !$menuIds) {
            return false;
        }
        
        $totalMenus = count($menuIds);
        $user = wp_get_current_user();

        foreach ($menuIds as $menuId) {
			as_schedule_single_action(
		        time() + 10,
		        'translate_menu',
		        [
			        'sourceLang' => $sourceLang,
			        'destLang' => $destLang,
			        'menuId' => $menuId,
		        ],
		        'menu_translations'
	        );
        }

	    as_schedule_single_action(
		    time() + 120,
		    'translate_menu_notify',
		    [
			    'totalMenus' => $totalMenus,
			    'userId' => $user->ID,
		    ],
		    'menu_translations'
	    );

        exit();
    }
}
