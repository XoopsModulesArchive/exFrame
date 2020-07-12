<?php
/**
@brief pTemplate/ListTemplate の改修型
@version $Id:$
*/

require_once "exComponent/Component.php";

class exTableComponent extends exComponent {
	var $def_render_class_="exTableCellRender";
	var $def_render_=null;
	var $table_model_=null;
	var $column_model_=null;

	function exTableComponent($processor=null,$render=null,$name=null) {
		if(!is_object($processor))
			$processor= new exTableProcessor();

		if(!is_object($render))
			$render = new exTableRender();

		parent::exComponent($processor,$render,$name);
	}

	function setController($controller) {
		$this->controller_->$controller;
	}

	function setDefaultRender ($render) {
		$this->def_render_=$render;
	}

	function init($tm, $render=null) {
		$this->table_model_=$tm;
		if ($render) {
			$this->def_render_=$render;
		}
		else {
			$classname = $this->def_render_class_;
			$this->def_render_=new $classname();
		}

		return $this->doProcess();
	}
}

class exTableProcessor extends exComponentProcessor {
	function process(&$component) {
		$ret = $component->table_model_->init();

		if($component->table_model_->isError()) {
			$component->msg_=$component->table_model_->msg_;
			return $ret;
		}

		return COMPONENT_INIT_SUCCESS;
	}
}

class exTableRender extends exComponentRender {
	var $component_;
	
	function init($component) {
		$this->component_=$component;
	}

	function render() {
		$ret = $this->_fetchHtmlHead().
		         $this->_fetchHtmlBody().
		         $this->_fetchHtmlFoot();
		return $ret;
	}

	function _fetchHtmlHead () {
		$ret ="";
		$cols = $this->component_->table_model_->getColumnCount();

		$ret .= $this->component_->def_render_->getStartTable();
		$ret .= $this->component_->def_render_->getStartHeadRow();
		for ( $i=0 ; $i<$cols ; $i++ ) {
			$ret .= $this->component_->def_render_->getTableHeadCell($this->component_,$this->component_->table_model_->getColumnName($i),$i);
		}
		$ret .= $this->component_->def_render_->getEndHeadRow();

		return $ret;
	}

	function _fetchHtmlBody () {
		$ret = "";
		$rows = $this->component_->table_model_->getRowCount();
		$cols = $this->component_->table_model_->getColumnCount();
		$colmn_names = $this->component_->table_model_->getColumnNames();

		for ( $i=0 ; $i<$rows ; $i++ ) {
			$arr = $this->component_->table_model_->getRow($i);
			$ret .= $this->component_->def_render_->getStartRow();
			$col =0;

			for ( $c=0 ; $c<$cols ; $c++ ) {
				$ret .= $this->component_->def_render_->getTableCell($this->component_,$arr,$i,$c);
			}

			$ret .= $this->component_->def_render_->getEndRow();
		}
		return $ret;
	}

	function _fetchHtmlFoot () {
		return $this->component_->def_render_->getEndTable();
	}
}


class exTableModel extends exComponentModel {
	var $_column_=array();
	var $_row_data_=array();

	function init() {}

	/**
	@return int
	*/
	function getRowCount() {
		return count($this->_row_data_);
	}

	/**
	@return int
	*/
	function getColumnCount() {
		return count($this->_column_);
	}

	/**
	@return array string
	*/
	function getColumnNames() {
		return $this->_column_;
	}

	/**
	@return string
	*/
	function getColumnName($col) {
		$column_name=$this->_column_[$col];

		// CallBack
		if(method_exists($this,"getColumnAt".ucfirst($column_name))) {
			$value = call_user_func(array($this,"getColumnAt".ucfirst($column_name)),$arr);
		}
		else {
			$value = $column_name;
		}
		
		return $column_name;
	}

	/**
	@brief $column_name で指定された列の名前を返します。
	\par column_name は英文などですが、それに別名を割り当てる際に用います。
	\par このメソッドは、 getHeadAtXXXX(XXXX には $column_nameが入る）という
	\par メソッドが存在すると、そちらを優先して呼び出します。
	@param $column_name String
	@return string
	@note このメソッドをオーバーライドしたテーブルモデルを使用すれば、列名変換をもっと便利なものにできます。
	*/
	function getHeadAt($column_name,$arr=null)
	{
		$column_name = strtolower($column_name);
		if(method_exists($this,"getHeadAt".ucfirst($column_name))) {
			return call_user_func(array($this,"getHeadAt".ucfirst($column_name)),$arr);
		}
		else {
			return $column_name;
		}
	}

	/**
	@brief 標準の exTable はこちらを呼び出します。必ず実装して下さい。
	*/
	function getRow($rowIndex) {
		return $this->_row_data_[$rowIndex];
	}

	function getValueAt($arr,$column_name) {
		return $arr[$column_name];
	}
}


/**
@brief テーブルレンダーの抽象化 class
*/
class exTableCellRender {
	var $cycle_=false;

	function getStartTable() {
		return "<table class='outer'>";
	}
	
	function getEndTable() {
		return "</table>";
	}

	function getStartHeadRow() {
		return "<tr>";
	}

	function getEndHeadRow() {
		return "</tr>";
	}

	function getStartRow() {
		if($this->cycle_) {
			$this->cycle_=false;
			return "<tr class='even'>";
		}
		else {
			$this->cycle_=true;
			return "<tr class='odd'>";
		}
	}

	function getEndRow() {
		return "</tr>";
	}

	/**
	@param $table
	@param $value 列名
	@param $column 列順
	*/
	function getTableHeadCell($table,$value,$column) {
		$value = call_user_func(array($table->table_model_,"getHeadAt"),$value);
		return @sprintf("<th>%s</th>",$value);
	}

	function getTableCell($table,$arr,$row,$column) {
		$column_name = strtolower($table->table_model_->getColumnName($column));

		// CallBack
		if(method_exists($table->table_model_,"getValueAt".ucfirst($column_name))) {
			$value = call_user_func(array($table->table_model_,"getValueAt".ucfirst($column_name)),$arr);
		}
		else {
			$value = $table->table_model_->getValueAt($arr,$column_name);
		}
		return @sprintf("<td>%s</td>", $value);
	}
}

?>