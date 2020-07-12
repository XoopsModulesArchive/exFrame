<?php
/**
@brief モジュール用の Singleton 感覚の基底クラスメソッドを定義したクラス（参考用）
@version $Id: global.php,v 1.1 2004/07/28 11:25:50 minahito Exp $
*/

require_once 'xoops/object.php';

class exAbstractGlobal {
	var $prefix_;
	var $suffix_='Object';

	/**
	@brief キャッシュに使用する変数名を共通ハッシュにし、実装しやすくしたもの
	@note 継承すればそのままクラスメソッドで使えます。
	*/
	function &getHandler($name)
	{
		global $xoopsModule;
		global $__modulename_handlers_cache__;
		global $xoopsDB;

		// 先に名前（キャッシュ名）を取得
		$name=strtolower(trim($name));
		$class = $this->prefix_.ucfirst($name).$this->suffix_;

		if(!isset($__modulename_handelrs_cache__[$class])) {
			if(!class_exists($class) && is_object($xoopsModule)) { // １回だけ読み込む
				$filename = XOOPS_ROOT_PATH."/modules/".$xoopsModule->dirname()."/class/".$name.".php";
				if(file_exists($filename)) {
					require_once($filename);
				}
			}

			$handler_class = $class."Handler";
			if(class_exists($handler_class)) {
				$__modulename_handelers_cache__[$class] = new $handler_class($xoopsDB,$class);
			}
			else {
				$__modulename_handelers_cache__[$class] = new exXoopsObjectHandler($xoopsDB,$class);
			}
			return $__modulename_handelers_cache__[$class];
		}
		else {
			return $__modulename_handelers_cache__[$class];
		}
	}

	/**
	@brief 指定した名前のハンドラを取得するひな形
	*/
	function &_getHandler($name)
	{
		global $xoopsModule;
		global $__sample_handelers_cache__;
		global $xoopsDB;

		$name=strtolower(trim($name));
		if(!isset($__modulename_handelrs_cache__[$name])) {
			$class = $this->prefox_.ucfirst($name).$this->suffix_;

			if(!class_exists($class)) { // １回だけ読み込む
				$filename = XOOPS_ROOT_PATH."/modules/".$xoopsModule->dirname()."/class/".$name.".php";
				if(file_exists($filename)) {
					require_once($filename);
				}
			}

			$handler_class = $class."Handler";
			if(class_exists($handler_class)) {
				$__sample_handelers_cache__[$name] = new $handler_class($xoopsDB,$class);
			}
			else {
				$__sample_handelers_cache__[$name] = new exXoopsObjectHandler($xoopsDB,$class);
			}
			return $__sample_handelers_cache__[$name];
		}
		else {
			return $__sample_handelers_cache__[$name];
		}
	}
}

?>