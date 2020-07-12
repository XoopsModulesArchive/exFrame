<?php
/**
@brief 実装を楽にするための典型的な処理を行うテーブルモデル群
@version $Id: TypicalListTableModel.php,v 1.1 2004/08/02 11:28:18 minahito Exp $
*/

require_once "exComponent/table/ListTable.php";

class exTypicalListTableModel extends exListTableModel {
	var $filter_;
	var $handler_;
	var $limit_;

	function init() {
		$this->listController_=new ListController();
		$this->listController_->filter_=&$this->filter_;
		$this->listController_->fetch($this->handler_->getCount($this->filter_->getCriteria()),$this->limit_);

		$objs = $this->handler_->getObjects($this->listController_->getCriteria());
		foreach ( $objs as $obj ) {
			$this->_row_data_[]=$obj->getStructure();
		}

		return COMPONENT_MODEL_INIT_SUCCESS;
	}
}

?>