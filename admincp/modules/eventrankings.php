<?php
/**
 * Event Rankings
 * https://webenginecms.org/
 * 
 * @version 1.0.0
 * @author Lautaro Angelico <http://lautaroangelico.com/>
 * @copyright (c) 2013-2019 Lautaro Angelico, All Rights Reserved
 * @build 78769f14e4ba7592617243b8d4529015
 */

$subModules = array(
	'settings' => 'settings.php'
);

$subModulePath = __PATH_ADMINCP_MODULES__ . $_REQUEST['module'] . '/';

if(check_value($_REQUEST['page'])) {
    if(!array_key_exists($_REQUEST['page'], $subModules)) throw new Exception('The requested sub-module is not valid.');
	
	$subModulePath = $subModulePath . $subModules[$_REQUEST['page']];
	if(!file_exists($subModulePath)) throw new Exception('The requested sub-module doesn\'t exist, please re-upload the plugin files.');
	
	try {
		
		if(!@include_once($subModulePath)) throw new Exception('The requested sub-module could not be loaded.');
		
	} catch (Exception $ex) {
		message('error', $ex->getMessage());
	}
	
} else {
    message('error','Please select a sub-module.');
}