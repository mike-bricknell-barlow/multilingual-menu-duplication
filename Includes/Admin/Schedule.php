<?php

namespace MultilingualMenuDuplication\Admin;

use MultilingualMenuDuplication\Admin\Translate;

class Schedule
{
    public function __construct()
    {
        add_action('translate_menu', [$this, 'translateMenu'], 10, 3);
        add_action('translate_menu_notify', [$this, 'notifyComplete'], 10, 2);
    }

    public function translateMenu($sourceLang, $destLang, $menuIds)
    {
        Translate::translateMenu($sourceLang, $destLang, $menuIds);
    }

    public function notifyComplete($count, $user)
    {
        $mailTo = $user->user_email;
        \InvezzPlugin\Log\Log::log(
            'User object',
            serialize($user)
        );
        
        $subject = sprintf(
            '%s menus successfully translated.',
            $count
        );

        $message = sprintf(
            'WordPress has completed translation on %s menus. Please log in to Invezz and check
            that all menus are working as expected.',
            $count
        );

        wp_mail(
            $mailTo,
            $subject,
            $message
        );

        exit();
    }
}
