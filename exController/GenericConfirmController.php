<?php
/**
@version $Id$
@note Processor �λŻ����⤷��ʤ��Τǡ������Ƥ
*/

require_once "exForm/Form.php";

define ( "CONTROLLER_INIT_FAIL", "__error__controller_init_fail__" );

class GenericConfirmControllerCondition {
	var $handler_=null;
}

class GenericConfirmController extends exAbstractForm {
	var $cond_;
	var $compo_;

	function GenericConfirmController (&$cond) {
		$this->cond_=&$cond;
	}

	// final
	function doGet() {}

	// final
	function doPost() {}

	function doService() {
		switch($ret=$this->compo_->init()) {
			case COMPONENT_INIT_FAIL:
				$ret=CONTROLLER_INIT_FAIL;
				break;

			case ACTIONFORM_POST_SUCCESS:
				$editform=&Session::get($this->compo_->name_);
				$editform->data_->setDirty();
				if($handler->insert($editform->data_)) {
					// ������쥯�Ƚ���
				}
				else {
					// Fatal ���顼����
				}
				break;

			case ACTIONFORM_INIT_FAIL:
			case ACTIONFORM_INIT_SUCCESS:
			case COMPONENT_INIT_SUCCESS:
				$this->compo_->display();
				break;
		}
	}
}

class GenericAdminConfirmController extends exAbstractForm {
	function doService() {
		switch($ret=$this->compo_->init()) {
			case COMPONENT_INIT_FAIL:
				$ret=CONTROLLER_INIT_FAIL;
				break;

			case ACTIONFORM_POST_SUCCESS:
				$editform=&Session::get($this->compo_->name_);
				$editform->data_->setDirty();
				if($handler->insert($editform->data_)) {
					// ������쥯�Ƚ���
				}
				else {
					// Fatal ���顼����
				}
				break;

			case ACTIONFORM_INIT_FAIL:
			case ACTIONFORM_INIT_SUCCESS:
			case COMPONENT_INIT_SUCCESS:
				xoops_cp_header();
				$this->compo_->display();
				xoops_cp_footer();
				break;
		}
	}
}

?>