<?php
/**
@brief アクセストリガーを利用した擬似的なタスク実装のための補助
@version $Id: VTask.php,v 1.1 2004/07/21 11:20:32 minahito Exp $
*/

require_once "xoops/class.object.php";

class exFrameTaskObject extends exXoopsObject {
	function exFrameTaskObject($id=null)
	{
		$this->initVar('tid', XOBJ_DTYPE_INT, 0, true);
		$this->initVar('time', XOBJ_DTYPE_INT, 0, true);
		$this->initVar('serial', XOBJ_DTYPE_OTHER );

		if ( is_array ( $id ) )
			$this->assignVars ( $id );
	}

	function &getTableInfo()
	{
		$tinfo = new exTableInfomation('exframe_task','tid');
		return ($tinfo);
	}
}

class exVirtualTask {
	var $classname_="exVirtualTask";
	var $include_;
	var $map_;
	
	/**
	@brief インクルードファイルを追加する
	*/
	function addInclude($file) {
		if(is_array($file)) {
			foreach($file as $f) {
				$this->addInclude($f);
			}
		}
		else {
			if(strpos($f,XOOPS_ROOT_PATH)==0) {
				$this->include_[] = $f;
				return true;
			}
			else
				return false;
		}
	}

	/**
	@brief require_once の実行
	*/
	function processInclude() {
		foreach($this->include_ as $f) {
			if(strpos($f,XOOPS_ROOT_PATH)==0)
				require_once($f);
		}
	}

	/**
	@brief インスタンスの復元
	*/
	function create() {
		$ret = unserialize($this->getVar('serial','e'));
		return $ret;
	}

	/**
	@brief 連想配列プロパティに値を登録 
	*/
	function setAttribute($key,$value) {
		$this->map_[$key]=$value;
	}

	/**
	@brief 連想配列プロパティから値を取り出す 
	*/
	function getAttribute($key) {
		return ($this->map_[$key]);
	}
}

?>