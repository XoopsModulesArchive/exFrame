<?php
/**
@brief 実装を楽にするための典型的な処理を行うテーブルモデル群
@version $Id: GenericListTableModel.php,v 1.1 2004/08/04 10:12:46 minahito Exp $
*/

require_once "exComponent/table/ListTable.php";
require_once "exComponent/table/model/TypicalListTableModel.php";
require_once "exForm/Filter.php";

class exGenericListTableModel extends exTypicalListTableModel
{
	function exGenericListTableModel(&$handler,$limit=20,$filter=null)
	{
		$this->handler_=&$handler;
		$this->limit_=$limit;
		if($filter!==null)
			$this->filter_=&$filter;	// wmm..
		else
			$this->filter_=new exGenericListTableModelFilter($this->handler_);

		// column の確定
		$object =& $handler->create();
		$arr=$object->getVars();
		foreach(array_keys($arr) as $key) {
			$this->_column_[]=$key;
		}
	}
}

/**
@brief 動的に自分の sort 設定を定める Dyna フィルタ。事実上、exGenericListTableModel 専用(?)
*/
class exGenericListTableModelFilter extends exAbstractFilterForm
{
	/**
	@param $object サンプルとして渡される XoopsObject もしくは exXoopsObject のhandler
	\par create() メソッドで空のインスタンスが生成できなくてはいけません
	*/
	function exGenericListTableModelFilter(&$handler)
	{
		$object =& $handler->create();
		if(!is_object($object)) return false;
		$arr = array();
		$arr = $object->getVars();
		foreach(array_keys($arr) as $key){
			$this->sort_[]=$key;
		}

		parent::exAbstractFilterForm();
	}

	function fetch()
	{
	}
	
	function getCriteria($start=0,$limit=0,$sort=0)
	{
		$criteria = $this->getSortCriteria($start,$limit,$sort);
		return $criteria;
	}

	function getDefaultCriteria($start,$limit)
	{
		$criteria=parent::getDefaultCriteria($start,$limit);
		return $criteria;
	}
}


?>