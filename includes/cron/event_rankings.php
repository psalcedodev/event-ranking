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

// File Name
$file_name = basename(__FILE__);

// Run Cron
$EventRankings = new \Plugin\EventRankings\EventRankings();

$EventRankings->setRankingType('bloodcastle');
$EventRankings->updateCache();

$EventRankings->setRankingType('devilsquare');
$EventRankings->updateCache();

$EventRankings->setRankingType('chaoscastle');
$EventRankings->updateCache();

// UPDATE CRON
updateCronLastRun($file_name);