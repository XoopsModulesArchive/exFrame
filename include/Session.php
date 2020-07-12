<?php
/**
@file
@brief セッション記録のための class
@note システムのセッション記録方針にあわせるためのラッパーとその他。本コードは $_SESSION 直やりとり方式
*/

if(!defined('EXFRAME_SESSION_PREFIX'))
	define('EXFRAME_SESSION_PREFIX','__exf__');

/**
@brief セッションを取り扱うためのラッパーです
@deprecated このクラス名はコンフリクトを起こしやすい。代替として exSession クラスを使用してください
このクラスは 1.00 までに削除される予定です。
*/
class Session {
	/**
	@brief 指定した名前でセッション値を保存します（クラスメソッド）
	@param $name セッション名
	@param $value 値
	@note 実際の保存名には EXFRAME_SESSION_PREFIX とアンダースコア２個が加算されます。
	*/
	function register($name,$value) {
		$name=EXFRAME_SESSION_PREFIX."__".$name;
		$_SESSION[$name]=$value;
	}

	/**
	@brief 指定した名前でセッション値を返します（クラスメソッド）
	@param $name セッション名
	@return レジストされている値
	@note 実際の保存名には EXFRAME_SESSION_PREFIX とアンダースコア２個が加算されます。
	*/
	function &get($name) {
		$name=EXFRAME_SESSION_PREFIX."__".$name;
		if(isset($_SESSION[$name]))
			return $_SESSION[$name];
		else
			return false;
	}

	/**
	@brief 指定した名前でセッション値を返したあと、値を削除します（クラスメソッド）
	@param $name セッション名
	@return レジストされている値
	@note 実際の保存名には EXFRAME_SESSION_PREFIX とアンダースコア２個が加算されます。
	*/
	function &pop($name) {
		$name=EXFRAME_SESSION_PREFIX."__".$name;
		if(isset($_SESSION[$name])) {
			$ret=$_SESSION[$name];
			unset($_SESSION[$name]);
			return $ret;
		}
		else
			return false;
	}

	/**
	@brief POST リクエストの場合 get, そうでなければ pop を呼び出すラッパー（クラスメソッド）
	@param $name セッション名
	@return レジストされている値
	@note 実際の保存名には EXFRAME_SESSION_PREFIX とアンダースコア２個が加算されます。
	*/
	function &postPop($name) {
		if(exFrame::isPost())
			return Session::pop($name);
		else
			return Session::get($name);
	}
	
	/**
	@brief 指定した名前のセッション値があるかどうか調査します（クラスメソッド）
	@param $name セッション名
	@return 存在していれば真、していなければ偽を返します
	@note 実際の保存名には EXFRAME_SESSION_PREFIX とアンダースコア２個が加算されます。
	*/
	function is_registered($name){
		$name=EXFRAME_SESSION_PREFIX."__".$name;

		return isset($_SESSION[$name]);
	}

	/**
	@brief 指定した名前のセッション値を削除します（クラスメソッド）
	@param $name セッション名
	@note 実際の保存名には EXFRAME_SESSION_PREFIX とアンダースコア２個が加算されます。
	*/
	function unregister($name){
		$name=EXFRAME_SESSION_PREFIX."__".$name;
		unset($_SESSION[$name]);
	}

	function addRole($major,$minor,$lifetime=0) {
		$role=array();
		if(Session::is_registered('exRole'))
			$role=&Session::get('exRole');

		if($lifetime) $lifetime+=time();
		$role[$major.":".$minor]=$lifetime;

		Session::register('exRole',$role);
	}

	function clearRole($major,$minor) {
		if(Session::is_registered('exRole')) {
			$role=&Session::get('exRole');
			unset($role[$major.":".$minor]);
			
			Session::register('exRole',$role);
		}
	}

	function isRole($major,$minor) {
		if(Session::is_registered('exRole')) {
			$role=&Session::get('exRole');
			if(!isset($role[$major.":".$minor]))
				return false;

			if(time()>$role[$major.":".$minor] && $role[$major.":".$minor]!==0 ) {
				Session::clearRole($major,$minor);
				return false;
			}

			return true;
		}
		else
			return false;
	}
}

/**
@brief セッションとのやりとりを行うためのラッパークラス
この exSession はセッションへの登録時、値をシリアル化します。
これにより xoops 特有の session_start のシビアなタイミングを少しずらすことができます
get で取得するまでにクラスを宣言しておけば session_start 後であっても構いません
*/
class exSession extends Session {
	/**
	@brief 指定した名前でセッション値を保存します（クラスメソッド）
	このとき、シリアライズを使用し、復元を助けます
	@param $name セッション名
	@param $value 値
	@note 実際の保存名には EXFRAME_SESSION_PREFIX とアンダースコア２個が加算されます。
	*/
	function register($name,&$value) {
		$name=EXFRAME_SESSION_PREFIX."__".$name;
		$_SESSION[$name]=serialize($value);
	}

	/**
	@brief 指定した名前でセッション値を返します（クラスメソッド）
	@param $name セッション名
	@return レジストされている値
	@note 実際の保存名には EXFRAME_SESSION_PREFIX とアンダースコア２個が加算されます。
	*/
	function &get($name) {
		$ret=null;
		$name=EXFRAME_SESSION_PREFIX."__".$name;
		if(isset($_SESSION[$name]))
			$ret = unserialize($_SESSION[$name]);

		return $ret;
	}
}

?>