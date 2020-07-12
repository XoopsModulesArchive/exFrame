<?php
/**
@brief ページナビゲータ付のテーブルコンポーネント
@version $Id: ListTable.php,v 1.9 2005/04/05 02:25:49 minahito Exp $
*/

require_once "exComponent/Table.php";
require_once "include/ListController.php";

class exListTableComponent extends exTableComponent {
	var $def_render_class_="exListTableCellRender";

	function exListTableComponent($processor=null,$render=null,$name=null) {
		if(is_object($processor))
			$processor = new exTableProcessor();

		if(!is_object($render)) {
			$render= new exListTableRender();
		}

		parent::exTableComponent($processor,$render,$name);
	}

}

class exListTableModel extends exTableModel {
	var $listController_=null;

	function getSort($sort)
	{
		// Filter から番号を求める
		if(is_object($this->listController_->filter_)) {
			$sortkeys=$this->listController_->filter_->getSortProperties();
			$count=count($sortkeys);
			for($i=0;$i<$count;$i++) {
				if($sortkeys[$i]==$sort)
					return $this->_renderSort($i+1);
			}
		}
	}

	/**
	@brief ソートを HTML として書き込むためのヘルパ
	*/
	function _renderSort($sort)
	{
		$base = $this->listController_->renderUrl4Sort();
		$ret = @sprintf("<a href='%s&amp;sort=%u'><img src='%s/modules/exFrame/images/up.gif' alt='ASC'></a>",$base,$sort,XOOPS_URL).
			@sprintf("<a href='%s&amp;sort=-%u'><img src='%s/modules/exFrame/images/down.gif' alt='DESC'></a>",$base,$sort,XOOPS_URL);
		return $ret;
	}
}

class exListTableRender extends exTableRender {
	function _fetchHtmlHead ()
	{
		$ret="";

		$this->component_->table_model_->listController_->freeze();

		if($this->component_->table_model_->listController_) {
			$ret.="<div align='center'>".
				$this->component_->table_model_->listController_->renderNavi()."</div>";
		}

		$ret.=parent::_fetchHtmlHead();
		return $ret;
	}

	function _fetchHtmlFoot ()
	{
		$ret="";
		$ret.=parent::_fetchHtmlFoot();

		if($this->component_->table_model_->listController_) {
			$ret.="<div align='center'>".
				$this->component_->table_model_->listController_->renderNavi()."</div>";
		}

		return $ret;
	}
}

class exListTableCellRender extends exTableCellRender {
	function getTableHeadCell($table,$value,$column) {
		$disp_name = call_user_func(array($table->table_model_,"getHeadAt"),$value);
		return @sprintf("<th nowrap>%s %s</th>",$disp_name,$table->table_model_->getSort(strtolower($value)));
	}
}


?>