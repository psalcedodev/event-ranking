<?php
/**
 * Event Rankings
 * https://webenginecms.org/
 * 
 * @version 1.1.1
 * @author Lautaro Angelico <http://lautaroangelico.com/>
 * @copyright (c) 2013-2019 Lautaro Angelico, All Rights Reserved
 * @build 78769f14e4ba7592617243b8d4529015
 */

// namespace
namespace Plugin\EventRankings;

// plugin root
define('__PATH_EVENTRANKINGS_ROOT__', __PATH_PLUGINS__.'eventrankings/');

// admincp
$extra_admincp_sidebar[] = array(
    'Event Rankings', array(
        array('Settings','eventrankings&page=settings')
    )
);

// language
if(file_exists(__PATH_EVENTRANKINGS_ROOT__ . 'languages/'.config('language_default', true).'/language.php')) {
	if(!@include_once(__PATH_EVENTRANKINGS_ROOT__ . 'languages/'.config('language_default', true).'/language.php')) {
		throw new Exception('Error loading language file (eventrankings)');
	}
} else {
	// load default language file (en)
	if(!@include_once(__PATH_EVENTRANKINGS_ROOT__ . 'languages/en/language.php')) {
		throw new Exception('Error loading language file (eventrankings)');
	}
}

// load classes
if(!@include_once(__PATH_EVENTRANKINGS_ROOT__ . 'classes/class.eventrankings.php')) {
	throw new Exception(lang('topranking_error_1', true));
}

// rankings menu
addRankingMenuLink(lang('eventrankings_menu_bc'), 'bloodcastle', array('xteam', 'muemu', 'louis'));
addRankingMenuLink(lang('eventrankings_menu_ds'), 'devilsquare', array('xteam', 'muemu', 'louis'));
addRankingMenuLink(lang('eventrankings_menu_cc'), 'chaoscastle', array('xteam', 'muemu', 'louis'));