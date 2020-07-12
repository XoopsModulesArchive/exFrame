<?php
/**
@version $Id$
*/

define ( "EXFRAME_MOJALE", 1 );
define ( "EXFRAME_TOKEN", 2 );
define ( "EXFRAME_TICKET", 2 );
define ( "EXFRAME_CONFIRM", 4 );
define ( "EXFRAME_EXXOOPS", 8 );
define ( "EXFRAME_PERM", 16 );

class exFrame {

	/**
	@brief レジストリ用ハンドラを得る
	*/
	function &getRegistryHandler() {
		global $xoopsDB;
		global $__exframe_handler_cache__;
		if(!isset($__exframe_handler_cache__['registry'])) {
			if(!class_exists("exregistryobjecthandler"))
				require_once "exTra/Registry.php";
			$__exframe_handler_cache__['registry']= new exRegistryObjectHandler($xoopsDB,"exregistryobjecthandler");
		}
		return $__exframe_handler_cache__['registry'];
	}

	// ---------
	// functions
	// ---------

	/**
	@brief 現在のリクエストメソッドを調べ POST かどうかを返すクラスメソッド
	*/
	function isPost() {
		return $_SERVER['REQUEST_METHOD']=='POST';
	}

	/**
	@brief 現在のリクエストメソッドを調べ GET かどうかを返すクラスメソッド
	*/
	function isGet() {
		return $_SERVER['REQUEST_METHOD']=='GET';
	}

	/**
	@brief 管理者のときだけ値を表示
	*/
	function debug($val) {
		global $xoopsUser;
		if(is_object($xoopsUser)) {
    		if($xoopsUser->isAdmin()){
    			print nl2br(var_dump($val));
    			print "<br>";
    		}
		}
	}

	/**
	@brief XoopsFormDateTime で取得した値を unixtime で返します
	*/
	function decodeXoopsFormDateTime($arr) {
		if(isset($arr['date']) && isset($arr['time'])) {
			$tmp=explode('-',$arr['date']);
			return mktime(0,0,0,$tmp[1],$tmp[2],$tmp[0])+$arr['time'];
		}
		else {
			return 0;
		}
	}

	function file_get_contents($file) {
		if(!file_exists($file)) return false;

		if(function_exists("file_get_contents")) {
			return file_get_contents($file);
		}
		else {
			$ret="";

    		$fp=fopen($file,"r");
    		if(!$fp) return false;
    
			while($ret.=fread($fp,4096));
			
    		fclose($fp);
    		
    		return ($ret);
		}
	}

	/**
	@brief 指定したパッケージの環境をセットアップします
	*/
	function init ( $package ) {
		if( $package & EXFRAME_MOJALE )
			require_once __EXFRAME__PATH__."/mojaLE/Controller/controller.class.php";

		if( $package & EXFRAME_TOKEN )
			require_once __EXFRAME__PATH__."/include/OnetimeTicket.php";

		if( $package && EXFRAME_CONFIRM )
			require_once __EXFRAME__PATH__."/exForm/ConfirmTicketForm.php";

		if( $package && EXFRAME_EXXOOPS ) {
			require_once __EXFRAME__PATH__."/xoops/xoops.php";
			require_once __EXFRAME__PATH__."/xoops/user.php";
		}

		if( $package & EXFRAME_PERM )
			require_once __EXFRAME__PATH__."/xoops/perm.php";
	}

	function InjectionCheck($key) {
		return isset($_GET[$key]) || isset($_POST[$key]) || isset($_COOKIE[$key]) || isset($_SESSION[$key]);
	}

	function appendObjects(&$tpl,$name,&$objects) {
		foreach($objects as $obj) {
			$tpl->append(name,$obj->getStructure());
		}
	}
	
	function language($name) {
		global $xoopsConfig;
		if(isset($xoopsConfig['language'])) {
			$file=dirname(__FILE__)."/../language/".$xoopsConfig['language']."/".$name.".php";
			if(!file_exists($file))
				$file=dirname(__FILE__)."/../language/english/".$name.".php";
		}
		else
			$file=dirname(__FILE__)."/../language/english/".$name.".php";

		require_once($file);
	}

}

?>