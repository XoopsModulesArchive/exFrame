<?php
/**
@brief グローバル名前空間に依存しまくったコールバックによる実装が可能なテーブルモデル
@note 叩き台に大変便利です
@version $Id$
*/

require_once "exComponent/table/model/GenericListTableModel.php";

class exRoughListTableModel extends exGenericListTableModel
{
	function exRoughListTableModel(&$handler,$limit=20,$filter=null)
	{
		global $cb_add_columns, $cb_filter_columns;
		parent::exGenericListTableModel($handler,$limit,$filter);

		if(function_exists("cb_getValueAtAction")) {
			$this->_column_[]="ACTION";
		}

		if(count($cb_add_columns)) {
			foreach($cb_add_columns as $col) {
				if(array_search($col,$this->_column_)===false)
					$this->_column_[]=$col;
			}
		}
		if(count($cb_filter_columns)) {
			$ar=array();
			foreach($this->_column_ as $col ) {	// 配列を組み直さないといけない
				if(!in_array($col,$cb_filter_columns))
					$ar[]=$col;
			}
			unset($this->_column_);
			$this->_column_=$ar;
		}
	}
	
	function getHeadAt($column_name,$arr=null) {
		$func_name="cb_getHeadAt".ucfirst($column_name);
		if(function_exists($func_name))
			return call_user_func($func_name,array($column_name,$arr));
		else
			return $column_name;
	}

	function getValueAt($arr,$column_name) {
		$func_name="cb_getValueAt".ucfirst($column_name);
		if(function_exists($func_name))
			return call_user_func($func_name,$arr);
		else
			return $arr[$column_name];
	}
}

?>