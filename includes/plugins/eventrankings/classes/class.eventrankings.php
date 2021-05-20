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

namespace Plugin\EventRankings;

class EventRankings
{

	private $_configXml = 'config.xml';
	private $_modulesPath = 'modules';
	private $_cronFile = 'event_rankings.php';

	private $_userColumn = 'Name';
	private $_scoreColumn = 'Score';

	private $_resultsLimit = 50;
	private $_showLevel = true;
	private $_showClass = true;
	private $_liveRankingData = false;
	private $_excludedPlayers;

	private $_ranking;
	private $_rankingList = array(
		'bloodcastle' => array(
			'table' => 'RankingBloodCastle',
			'cache' => 'rankings_bloodcastle.cache'
		),
		'devilsquare' => array(
			'table' => 'RankingDevilSquare',
			'cache' => 'rankings_devilsquare.cache'
		),
		'chaoscastle' => array(
			'table' => 'RankingChaosCastle',
			'cache' => 'rankings_chaoscastle.cache'
		)
	);

	// CONSTRUCTOR

	function __construct()
	{
		global $custom;

		// load databases
		$this->mu = \Connection::Database('MuOnline');
		$this->db = \Connection::Database('Me_MuOnline');

		// vars
		$this->custom = $custom;

		// config file path
		$this->configFilePath = __PATH_EVENTRANKINGS_ROOT__ . $this->_configXml;
		if (!file_exists($this->configFilePath)) throw new \Exception(lang('eventrankings_error_5', true));
		$xml = simplexml_load_file($this->configFilePath);
		if (!$xml) throw new \Exception(lang('eventrankings_error_5', true));
		$this->_configs = convertXML($xml->children());
		if (!is_array($this->_configs)) throw new \Exception(lang('eventrankings_error_5', true));

		// set configs
		$this->_resultsLimit = $this->_configs['results_limit'];
		$this->_showLevel = $this->_configs['show_level'];
		$this->_showClass = $this->_configs['show_class'];
		$this->_showOnlineStatus = $this->_configs['show_online'];
		$this->_liveRankingData = $this->_configs['live_ranking'];
		if (check_value($this->_configs['excluded_players'])) {
			$excludedPlayers = explode(",", $this->_configs['excluded_players']);
			$this->_excludedPlayers = $excludedPlayers;
		}

		// cron
		$this->_checkCron();
	}

	// PUBLIC FUNCTIONS

	public function loadModule($module)
	{
		if (!\Validator::Alpha($module)) throw new \Exception(lang('eventrankings_error_4', true));
		if (!$this->_moduleExists($module)) throw new \Exception(lang('eventrankings_error_4', true));
		if (!@include_once(__PATH_EVENTRANKINGS_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) throw new \Exception(lang('eventrankings_error_4', true));
	}

	public function setRankingType($ranking)
	{
		if (!array_key_exists($ranking, $this->_rankingList)) throw new \Exception(lang('eventrankings_error_6', true));
		$this->_ranking = $ranking;
	}

	public function updateCache()
	{
		if (!check_value($this->_ranking)) return;

		$rankingData = $this->_getRankingData();
		if (!is_array($rankingData)) {
			$this->_updateCacheFile("");
			return;
		}

		$rankingDataJson = $this->_prepareCacheData($rankingData);
		if (!check_value($rankingDataJson)) {
			$this->_updateCacheFile("");
			return;
		}

		$this->_updateCacheFile($rankingDataJson);
	}

	public function getRankingData()
	{
		if ($this->_liveRankingData) {
			return $this->_getRankingData();
		} else {
			$cacheData = $this->_loadCacheData();
			if (!is_array($cacheData)) return;
			return $cacheData;
		}
	}

	public function showLevel()
	{
		return $this->_showLevel;
	}

	public function showClass()
	{
		return $this->_showClass;
	}

	// PRIVATE FUNCTIONS

	private function _moduleExists($module)
	{
		if (!check_value($module)) return;
		if (!file_exists(__PATH_EVENTRANKINGS_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
		return true;
	}

	private function _prepareCacheData($data)
	{
		if (!is_array($data)) return;
		return json_encode($data);
	}

	private function _updateCacheFile($data)
	{
		$file = __PATH_CACHE__ . $this->_rankingList[$this->_ranking]['cache'];
		if (!file_exists($file)) return;
		if (!is_writable($file)) return;

		$fp = fopen($file, 'w');
		fwrite($fp, $data);
		fclose($fp);
		return true;
	}

	private function _loadCacheData()
	{
		$file = __PATH_CACHE__ . $this->_rankingList[$this->_ranking]['cache'];
		if (!file_exists($file)) return;

		$cacheData = file_get_contents($file);
		if (!check_value($cacheData)) return;

		$cacheDataArray = json_decode($cacheData, true);
		if (!is_array($cacheDataArray)) return;

		return $cacheDataArray;
	}

	private function _checkCron()
	{
		$result = $this->db->query_fetch_single("SELECT * FROM " . WEBENGINE_CRON . " WHERE cron_file_run = ?", array($this->_cronFile));
		if (is_array($result)) return;
		$this->_createCron();
	}

	private function _createCron()
	{
		if (!file_exists(__PATH_CRON__ . $this->_cronFile)) throw new \Exception(lang('eventrankings_error_3', true));
		$cronMd5 = md5_file(__PATH_CRON__ . $this->_cronFile);
		if (!check_value($cronMd5)) throw new \Exception(lang('eventrankings_error_3', true));
		$insertData = array(
			'Event Rankings',
			$this->_cronFile,
			300,
			1,
			0,
			$cronMd5
		);
		$result = $this->db->query("INSERT INTO " . WEBENGINE_CRON . " (cron_name, cron_file_run, cron_run_time, cron_status, cron_protected, cron_file_md5) VALUES (?, ?, ?, ?, ?, ?)", $insertData);
		if (!$result) throw new \Exception(lang('eventrankings_error_3', true));
	}

	private function _getRankingData()
	{
		if (!check_value($this->_ranking)) return;

		$result = $this->mu->query_fetch("SELECT TOP " . $this->_resultsLimit . " * FROM " . $this->_rankingList[$this->_ranking]['table'] . " WHERE " . _CLMN_CHR_NAME_ . " NOT IN(" . $this->_excludedPlayersArray() . ") AND " . $this->_scoreColumn . " > 0 ORDER BY " . $this->_scoreColumn . " DESC");

		foreach ($result as $row) {
			$Character = new \Character();
			$characterData = $Character->CharacterData($row[$this->_userColumn]);

			$masterLevelInfo = $Character->getMasterLevelInfo($row[_CLMN_CHR_NAME_]);

			if ($characterData[_CLMN_CHR_LVL_] + $masterLevelInfo[_CLMN_ML_LVL_] <= 399) {
				$players[] = array(
					'name' => $characterData[_CLMN_CHR_NAME_],
					'level' => $characterData[_CLMN_CHR_LVL_] + $masterLevelInfo[_CLMN_ML_LVL_],
					'class' => $characterData[_CLMN_CHR_CLASS_],
					'score' => $row[$this->_scoreColumn],
					'eventlevel' => 1,
				);
			} else if ($characterData[_CLMN_CHR_LVL_] + $masterLevelInfo[_CLMN_ML_LVL_] <= 599) {
				$players[] = array(
					'name' => $characterData[_CLMN_CHR_NAME_],
					'level' => $characterData[_CLMN_CHR_LVL_] + $masterLevelInfo[_CLMN_ML_LVL_],
					'class' => $characterData[_CLMN_CHR_CLASS_],
					'score' => $row[$this->_scoreColumn],
					'eventlevel' => 2,
				);
			} else if ($characterData[_CLMN_CHR_LVL_] + $masterLevelInfo[_CLMN_ML_LVL_] <= 699) {
				$players[] = array(
					'name' => $characterData[_CLMN_CHR_NAME_],
					'level' => $characterData[_CLMN_CHR_LVL_] + $masterLevelInfo[_CLMN_ML_LVL_],
					'class' => $characterData[_CLMN_CHR_CLASS_],
					'score' => $row[$this->_scoreColumn],
					'eventlevel' => 3,
				);
			} else if ($characterData[_CLMN_CHR_LVL_] + $masterLevelInfo[_CLMN_ML_LVL_] <= 799) {
				$players[] = array(
					'name' => $characterData[_CLMN_CHR_NAME_],
					'level' => $characterData[_CLMN_CHR_LVL_] + $masterLevelInfo[_CLMN_ML_LVL_],
					'class' => $characterData[_CLMN_CHR_CLASS_],
					'score' => $row[$this->_scoreColumn],
					'eventlevel' => 4,
				);
			} else if ($characterData[_CLMN_CHR_LVL_] + $masterLevelInfo[_CLMN_ML_LVL_] <= 899) {
				$players[] = array(
					'name' => $characterData[_CLMN_CHR_NAME_],
					'level' => $characterData[_CLMN_CHR_LVL_] + $masterLevelInfo[_CLMN_ML_LVL_],
					'class' => $characterData[_CLMN_CHR_CLASS_],
					'score' => $row[$this->_scoreColumn],
					'eventlevel' => 5,
				);
			} else if ($characterData[_CLMN_CHR_LVL_] + $masterLevelInfo[_CLMN_ML_LVL_] <= 999) {
				$players[] = array(
					'name' => $characterData[_CLMN_CHR_NAME_],
					'level' => $characterData[_CLMN_CHR_LVL_] + $masterLevelInfo[_CLMN_ML_LVL_],
					'class' => $characterData[_CLMN_CHR_CLASS_],
					'score' => $row[$this->_scoreColumn],
					'eventlevel' => 6,
				);
			} else if ($characterData[_CLMN_CHR_LVL_] + $masterLevelInfo[_CLMN_ML_LVL_] >= 1000) {
				$players[] = array(
					'name' => $characterData[_CLMN_CHR_NAME_],
					'level' => $characterData[_CLMN_CHR_LVL_] + $masterLevelInfo[_CLMN_ML_LVL_],
					'class' => $characterData[_CLMN_CHR_CLASS_],
					'score' => $row[$this->_scoreColumn],
					'eventlevel' => 7,
				);
			}
		}

		return $players;
	}

	private function _excludedPlayersArray()
	{
		if (!is_array($this->_excludedPlayers)) return "''";
		$return = array();
		foreach ($this->_excludedPlayers as $characterName) {
			$return[] = "'" . $characterName . "'";
		}
		return implode(",", $return);
	}
}
