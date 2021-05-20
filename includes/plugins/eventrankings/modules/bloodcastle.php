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
	
	echo '<div class="page-title"><span>'.lang('module_titles_txt_10',true).'</span></div>';
	
	$Rankings = new Rankings();
	$Rankings->rankingsMenu();
	$Character = new Character();
	loadModuleConfigs('rankings');
	
	$EventRankings = new \Plugin\EventRankings\EventRankings();
	$EventRankings->setRankingType('bloodcastle');
	$rankingData = $EventRankings->getRankingData();
	if(!is_array($rankingData)) throw new Exception(lang('eventrankings_error_2',true));
	
	$showPlayerCountry = mconfig('show_country_flags') ? true : false;
	$charactersCountry = loadCache('character_country.cache');
	if(!is_array($charactersCountry)) $showPlayerCountry = false;
	
	if(mconfig('show_online_status')) $onlineCharacters = loadCache('online_characters.cache');
	if(!is_array($onlineCharacters)) $onlineCharacters = array();
	
	echo '<table class="table rankings-table">';
		echo '<tr>';
			echo '<td></td>';
			if($showPlayerCountry) echo '<td>'.lang('rankings_txt_33').'</td>';
			if($EventRankings->showClass()) echo '<td>'.lang('eventrankings_txt_1',true).'</td>';
			echo '<td>'.lang('eventrankings_txt_2',true).'</td>';
			if($EventRankings->showLevel()) echo '<td>'.lang('eventrankings_txt_3',true).'</td>';
			echo '<td>'.lang('eventrankings_txt_4',true).'</td>';
		echo '</tr>';
		foreach($rankingData as $i => $row) {
			$characterIMG = getPlayerClassAvatar($row['class'], true, true, 'rankings-class-image');
			$onlineStatus = mconfig('show_online_status') ? in_array($row['name'], $onlineCharacters) ? '<img src="'.__PATH_ONLINE_STATUS__.'" class="online-status-indicator"/>' : '<img src="'.__PATH_OFFLINE_STATUS__.'" class="online-status-indicator"/>' : '';
			echo '<tr>';
				echo '<td>'.($i+1).'</td>';
				if($showPlayerCountry) echo '<td><img src="'.getCountryFlag($charactersCountry[$row['name']]).'" /></td>';
				if($EventRankings->showClass()) echo '<td>'.$characterIMG.'</td>';
				echo '<td>'.playerProfile($row['name']).$onlineStatus.'</td>';
				if($EventRankings->showLevel()) echo '<td>'.$row['level'].'</td>';
				echo '<td>'.number_format($row['score']).'</td>';
			echo '</tr>';
		}
	echo '</table>';
	
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}