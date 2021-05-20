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

try {

	$EventRankings = new \Plugin\EventRankings\EventRankings();
	$EventRankings->loadModule('bloodcastle');
} catch (Exception $ex) {
	message('error', $ex->getMessage());
}
