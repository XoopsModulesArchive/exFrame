<?php
/**
@version $Id: xoops.php,v 1.4 2004/08/26 04:49:52 minahito Exp $
*/

require_once "include/common.php";

/**
@brief xoops 関係の補助クラスメソッド集
*/
class exXoops
{
	/**
	@brief $xoopsModule 以外の方法で dirname を求める（主にブロックから __FILE__ を渡して求める際に使用する）
	@note dirname 決め打ちがなくなり、複製化に対応しやすくなります。
	*/
	function getDirname($source)
	{
		if(is_string($source)) {
			$tmpstr = strtr($source,DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,"/");
			if(false !== ($pos=strpos($tmpstr,"modules/"))) {
				$tmpstr = substr($tmpstr,$pos+strlen("modules/"));
				return substr($tmpstr,0,strpos($tmpstr,"/"));
			}
			else {
				return false;	// モジュールではない
			}
		}
	}
	
	function getScriptName($scriptname)
	{
		return exCommon::getScriptName($scriptname);
	}

	function getMidByDirname($dirname)	// FIXME:: ハンドラを使うように修正
	{
		$handler=&xoops_gethandler("module");
		$mod=&$handler->getByDirname($dirname);
		return $mod->getVar('mid');
	}

	function getModuleConfigByDirname($dirname)	// FIXME:: ハンドラを使うように修正
	{
		$db=&Database::getInstance();
		return exXoops::getModuleConfig(exXoops::getMidByDirname($dirname));
	}

	function getModuleConfig($mid)	// FIXME:: ハンドラを使うように修正
	{
		static $__mconfig_cache__;

		if(isset($__mconfig_cache__[$mid]))
			return $__mconfig_cache__[$mid];

		$db=&Database::getInstance();
		$sql="SELECT conf_name,conf_value FROM ".$db->prefix('config')." WHERE conf_modid=".$mid;
		$res=$db->query($sql);
		$ret=array();
		while($row=$db->fetchArray($res)) {
			$ret[$row['conf_name']]=$row['conf_value'];
		}
		$__mconfig_cache__[$mid]=$ret;
		return $ret;
	}
}

?>