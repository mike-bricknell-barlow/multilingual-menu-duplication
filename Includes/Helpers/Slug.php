<?php

namespace MultilingualMenuDuplication\Helpers;

class Slug
{
    public static function getTranslatedSlug($slug, $destLang, $postType = false)
    {
        global $wpdb;
        $query = sprintf(
            'SELECT ID FROM %s WHERE post_name = "%s"',
            $wpdb->posts,
            $slug
        );

        if ($postType) {
            $query .= sprintf(
                ' AND post_type = "%s"',
                $postType
            );
        }
        
        $postIDs = $wpdb->get_results($query);
        
        if (!$postIDs) {
            return $slug;
        }

        $postId = $postIDs[0]->ID;
        $translatedPostId = pll_get_post($postId, $destLang);
        $translatedPostSlug = get_the_permalink($translatedPostId);
        $return = str_replace(get_home_url(), '', $translatedPostSlug);
        return $return;
    }
}
