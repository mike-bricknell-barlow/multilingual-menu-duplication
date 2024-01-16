<?php

namespace MultilingualMenuDuplication\Helpers;

class Internal
{
	public static function isInternalLink($url): bool
	{
		return str_contains($url, 'https://invezz.com') || str_contains($url, 'https://staging.invezz.com')
		       || str_contains($url, 'invezz.test');
	}
}