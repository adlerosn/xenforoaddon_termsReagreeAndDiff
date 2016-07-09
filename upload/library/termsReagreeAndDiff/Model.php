<?php

class termsReagreeAndDiff_Model{
	public static function mysql_escape_mimic_fromPhpDoc($inp)
	{//http://php.net/manual/pt_BR/function.mysql-real-escape-string.php
		return str_replace(array('\\',    "\0",  "\n",  "\r",   "'",   '"', "\x1a"),
						   array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'),
						   $inp);
	}
	public static function seeingTerms($requestUri){
		$seeingTerms = strpos($requestUri,'help/termschanges/') > 0;
		return $seeingTerms;
	}
	public static function install(){
		self::createTablesDB();
		self::insertTosUnconditionally();
	}
	public static function uninstall(){
		self::dropTablesDB();
	}
	public static function createTablesDB(){
		$dbc=XenForo_Application::get('db');
		$q = 'CREATE TABLE IF NOT EXISTS `kiror_tos_reagree_history` (
			tid SERIAL PRIMARY KEY,
			uts TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			tos LONGTEXT
		) CHARACTER SET utf8 COLLATE utf8_general_ci;';
		$dbc->query($q);
		$q = 'CREATE TABLE IF NOT EXISTS `kiror_tos_reagree_users` (
			uid INT PRIMARY KEY,
			uts TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			tid BIGINT UNSIGNED REFERENCES `kiror_tos_reagree_history`(`tid`)
		) CHARACTER SET utf8 COLLATE utf8_general_ci;';
		$dbc->query($q);
	}
	public static function dropTablesDB(){
		$dbc=XenForo_Application::get('db');
		$q = 'DROP TABLE IF EXISTS `kiror_tos_reagree_users`;';
		$dbc->query($q);
		$q = 'DROP TABLE IF EXISTS `kiror_tos_reagree_history`;';
		$dbc->query($q);
	}
	public static function getCurrentTos(){
		$dbc=XenForo_Application::get('db');
		$q='SELECT `phrase_text` AS `tos` FROM `xf_phrase` WHERE `title`="terms_rules_text" ORDER BY `language_id` DESC LIMIT 1;';
		$r = $dbc->fetchRow($q);
		if($r==null || !is_array($r) || !array_key_exists('tos',$r)){
			return null;
		}else return $r['tos'];
	}
	public static function insertTosUnconditionally(){
		$dbc=XenForo_Application::get('db');
		$q = 'INSERT INTO `kiror_tos_reagree_history` (SELECT `nxt`.`next` AS `tid`, NOW() AS `uts`, `xf_phrase`.`phrase_text` AS `tos` FROM `xf_phrase`,(SELECT MAX(`tid`)+1 AS `next` FROM `kiror_tos_reagree_history`) AS `nxt` WHERE `xf_phrase`.`title`="terms_rules_text" ORDER BY `language_id` DESC LIMIT 1);';
		$dbc->query($q);
	}
	public static function insertTos(){
		$dbc=XenForo_Application::get('db');
		$q = 'SELECT (`latest`.`tos` != `xf_phrase`.`phrase_text`) AS `tos_change` FROM `xf_phrase`,(SELECT `tos` FROM `kiror_tos_reagree_history` ORDER BY `tid` DESC LIMIT 1) AS `latest` WHERE `xf_phrase`.`title`="terms_rules_text" ORDER BY `language_id` DESC LIMIT 1 ;';
		$r = $dbc->fetchRow($q);
		if($r!=null && is_array($r) && array_key_exists('tos_change',$r) && $r['tos_change']){
			self::insertTosUnconditionally();
		}
	}
	public static function hasEntry($uid){
		$uid = intval($uid);
		if(!$uid) return true;
		$dbc=XenForo_Application::get('db');
		$q='SELECT 1 AS `hasEntry` FROM `kiror_tos_reagree_users` WHERE `uid`='.$uid.' LIMIT 1;';
		$r = $dbc->fetchRow($q);
		if($r!=null && is_array($r) && array_key_exists('hasEntry',$r) && $r['hasEntry']){
			return true;
		}   return false;
	}
	public static function ensureEntry($uid){
		if(self::hasEntry($uid)) return;
		$uid = intval($uid);
		$dbc=XenForo_Application::get('db');
		$q='INSERT INTO `kiror_tos_reagree_users` (uid,uts,tid) VALUES ('.$uid.',NOW(),1);';
		$dbc->query($q);
	}
	public static function checkAgreementDismissable($uid){
		self::ensureEntry($uid);
		$uid = intval($uid);
		if(!$uid) return array(true,array());
		$dbc=XenForo_Application::get('db');
		$q='SELECT h.tid AS `NewTosId`, UNIX_TIMESTAMP(h.uts) AS `NewTosTs`, a.tid AS `OldTosId`, UNIX_TIMESTAMP(a.uts) AS `OldTosTs`, ((a.tid=h.tid) OR (a.tos=h.tos)) AS `eq` FROM `kiror_tos_reagree_history` AS h, `kiror_tos_reagree_users` AS u INNER JOIN `kiror_tos_reagree_history` AS a ON (a.tid=u.tid) WHERE u.uid='.$uid.' ORDER BY h.tid DESC LIMIT 1;';
		$r = $dbc->fetchRow($q);
		if($r!=null && is_array($r) && array_key_exists('eq',$r)){
			if($r['eq']){
				return array(true,$r);
			}
		}       return array(false,$r);
	}
	public static function getTosById($tid){
		$tid = intval($tid);
		$dbc=XenForo_Application::get('db');
		$q='SELECT `tid`,UNIX_TIMESTAMP(`uts`) AS `uts`,`uts` AS `ts`,`tos` FROM `kiror_tos_reagree_history` WHERE `tid`='.$tid.' LIMIT 1;';
		return $dbc->fetchRow($q);
	}
	public static function agreeWithLatestTerms($uid){
		self::ensureEntry($uid);
		$uid = intval($uid);
		if(!$uid) return;
		$dbc=XenForo_Application::get('db');
		$q='UPDATE `kiror_tos_reagree_users` SET uts=NOW(), tid=(SELECT MAX(`tid`) FROM `kiror_tos_reagree_history` LIMIT 1) WHERE uid='.$uid.';';
		$dbc->query($q);
	}
}
