<?php

/**
 * Event Rankings
 * https://webenginecms.org/
 * 
 * @version 1.1.0
 * @author Lautaro Angelico <http://lautaroangelico.com/>
 * @copyright (c) 2013-2019 Lautaro Angelico, All Rights Reserved
 * @build 78769f14e4ba7592617243b8d4529015
 */

try {

	echo '<div class="page-title"><span>' . lang('module_titles_txt_10', true) . '</span></div>';

	$Rankings = new Rankings();
	$Rankings->rankingsMenu();
	$Character = new Character();
	loadModuleConfigs('rankings');

	$EventRankings = new \Plugin\EventRankings\EventRankings();
	$EventRankings->setRankingType('bloodcastle');
	$rankingData = $EventRankings->getRankingData();
	if (!is_array($rankingData)) throw new Exception(lang('eventrankings_error_2', true));

	$showPlayerCountry = mconfig('show_country_flags') ? true : false;
	$charactersCountry = loadCache('character_country.cache');
	if (!is_array($charactersCountry)) $showPlayerCountry = false;

	if (mconfig('show_online_status')) $onlineCharacters = loadCache('online_characters.cache');
	if (!is_array($onlineCharacters)) $onlineCharacters = array();

	if (isset($_POST['event1'])) {
		$eventLevelSelected = 1;
	}
	if (isset($_POST['event2'])) {
		$eventLevelSelected = 2;
	}
	if (isset($_POST['event3'])) {
		$eventLevelSelected = 3;
	}
	if (isset($_POST['event4'])) {
		$eventLevelSelected = 4;
	}
	if (isset($_POST['event5'])) {
		$eventLevelSelected = 5;
	}
	if (isset($_POST['event6'])) {
		$eventLevelSelected = 6;
	}
	if (isset($_POST['event7'])) {
		$eventLevelSelected = 7;
	}
	if (isset($_POST['seeall'])) {
		$eventLevelSelected = "";
	}

	echo '<form method="post"> ';
	echo '<div class="btn-group btn-group-justified" role="group" aria-label="...">';
	echo '<div class="btn-group" role="group">';
	echo '	<button type="submit" class="btn btn-default" name="event1">' . lang('eventrankings_bc_1') . '</button>';
	echo '</div>';
	echo '<div class="btn-group" role="group">';
	echo '	<button type="submit" class="btn btn-default" name="event2">' . lang('eventrankings_bc_2') . '</button>';
	echo '</div>';
	echo '<div class="btn-group" role="group">';
	echo '	<button type="submit" class="btn btn-default" name="event3">' . lang('eventrankings_bc_3') . '</button>';
	echo '</div>';
	echo '<div class="btn-group" role="group">';
	echo '	<button type="submit" class="btn btn-default" name="event4">' . lang('eventrankings_bc_4') . '</button>';
	echo '</div>';
	echo '<div class="btn-group" role="group">';
	echo '	<button type="submit" class="btn btn-default" name="event5">' . lang('eventrankings_bc_5') . '</button>';
	echo '</div>';
	echo '<div class="btn-group" role="group">';
	echo '	<button type="submit" class="btn btn-default" name="event6">' . lang('eventrankings_bc_6') . '</button>';
	echo '</div>';
	echo '<div class="btn-group" role="group">';
	echo '	<button type="submit" class="btn btn-default" name="event7">' . lang('eventrankings_bc_7') . '</button>';
	echo '</div>';
	echo '<div class="btn-group" role="group">';
	echo '	<button type="submit" class="btn btn-default" name="seeall">' . lang('eventrankings_txt_6') . '</button>';
	echo '</div>';
	echo '</div>';
	echo '</form> ';

	echo '<table class="table rankings-table">';
	echo '<tr>';
	echo '<td></td>';
	if ($showPlayerCountry) echo '<td>' . lang('rankings_txt_33') . '</td>';
	if ($EventRankings->showClass()) echo '<td>' . lang('eventrankings_txt_1', true) . '</td>';
	echo '<td>' . lang('eventrankings_txt_2', true) . '</td>';
	if ($EventRankings->showLevel()) echo '<td>' . lang('eventrankings_txt_3', true) . '</td>';
	echo '<td>' . lang('eventrankings_txt_4', true) . '</td>';
	echo '<td>' . lang('eventrankings_txt_5') . '</td>';
	echo '</tr>';


	foreach ($rankingData as $i => $row) {

		$characterIMG = getPlayerClassAvatar($row['class'], true, true, 'rankings-class-image');
		$onlineStatus = mconfig('show_online_status') ? in_array($row['name'], $onlineCharacters) ? '<img src="' . __PATH_ONLINE_STATUS__ . '" class="online-status-indicator"/>' : '<img src="' . __PATH_OFFLINE_STATUS__ . '" class="online-status-indicator"/>' : '';

		if ($eventLevelSelected && number_format($row['eventlevel']) == $eventLevelSelected) {
			echo '<tr>';
			echo '<td>' . ($i + 1) . '</td>';
			if ($showPlayerCountry) echo '<td><img src="' . getCountryFlag($charactersCountry[$row['name']]) . '" /></td>';
			if ($EventRankings->showClass()) echo '<td>' . $characterIMG . '</td>';
			echo '<td>' . playerProfile($row['name']) . $onlineStatus . '</td>';
			if ($EventRankings->showLevel()) echo '<td>' . $row['level'] . '</td>';
			echo '<td>' . number_format($row['score']) . '</td>';

			echo '<td>Bloodcastle ' . number_format($row['eventlevel']) . '</td>';
			echo '</tr>';
		} else if ($eventLevelSelected == "") {
			echo '<tr>';
			echo '<td>' . ($i + 1) . '</td>';
			if ($showPlayerCountry) echo '<td><img src="' . getCountryFlag($charactersCountry[$row['name']]) . '" /></td>';
			if ($EventRankings->showClass()) echo '<td>' . $characterIMG . '</td>';
			echo '<td>' . playerProfile($row['name']) . $onlineStatus . '</td>';
			if ($EventRankings->showLevel()) echo '<td>' . $row['level'] . '</td>';
			echo '<td>' . number_format($row['score']) . '</td>';
			echo '<td>Blood Castle ' . number_format($row['eventlevel']) . '</td>';
			echo '</tr>';
		}
	}
	echo '</table>';
} catch (Exception $ex) {
	message('error', $ex->getMessage());
}
