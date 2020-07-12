<?php
/**
@brief ŵ��Ū�ʺ������ȥ�����󶡤��륳��ݡ��ͥ��
*/

require_once "exComponent/Preview.php";
require_once "exForm/PreviewForm.php";

require_once "exConfig/ForwardConfig.php";

require_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

class exDeleteConfirmTypicalComponent extends exPreviewComponent {
	var $confirm_message_;
	var $obj_;
	var $handler_;

	function exDeleteConfirmTypicalComponent($processor=null,$render=null,$name=null,$form=null,$forwards=null) {
		if(!is_object($processor))
			$processor = new exDeleteConfirmTypicalComponentProcessor();
		if(!is_object($render))
			$render = new exDeleteConfirmTypicalComponentRender();
		if(!is_object($form))
			$form = new exPreviewTokenForm();

		parent::exPreviewComponent($processor,$render,$name,$form);

		$this->setForwards($forwards);
	}

	function init($obj,$msg,$handler=null) {
		$this->obj_=&$obj;
		$this->confirm_message_=$msg;
		return $this->doProcess();
	}
}


/**
@brief ��Ϣ�κ��������ŵ��Ū����ȥ���Ȥ��Ƥ����᤿���ϤǱ��Ѥ������ʤ������פΥץ��å������饹
@return COMPONENT_INIT_SUCCESS �� FAIL �����֤��ʤ�
@note ̾��Ĺ�ä�!
*/
class exDeleteConfirmTypicalComponentProcessor extends exComponentProcessor {
	function process(&$component) {
		// validation
		switch($ret=$component->form_->init($component->obj_,$component->name_)) {
			case ACTIONFORM_INIT_FAIL:
				$ret = COMPONENT_INIT_FAIL;
				break;

			case ACTIONFORM_INIT_SUCCESS:
				$ret = COMPONENT_INIT_SUCCESS;
				break;

			case ACTIONFORM_POST_SUCCESS:
				if($this->_processDelete($component)) {
					if(isset($component->forwards_['success']))
						$component->forwards_['success']->doForward();
				}
				else {
					if(isset($component->forwards_['fail']))
						$component->forwards_['fail']->doForward();
					else {
						// �����˥ե�������������
						die;
					}
				}
				$ret = COMPONENT_INIT_FAIL;
				break;
		}

		return ($ret);
	}
	
	function _processDelete($component) {
		$this->component_->handler_->delete($this->component_->obj_);
	}
}

/**
@brief exPreviewComponentRender �˳�ǧ��å���������Ϳ�������
@note ToDo ���ޤ��ʤ��ġ���������ġĶ��̤� PreviewRender ���Ȥ��ʤ�����ʤ�����PreviewModel �Τ褦�ʤ�Τ�ɬ�פ���
@note ���ξ��ľ�ܲ��̽��Ϥ�����������Τ� Processor �ǤϤʤ��Τ�����ľ��(���ޤϻ����ʤ���
*/
class exDeleteConfirmTypicalComponentRender extends exPreviewComponentRender {
	function render() {
		print $this->component_->confirm_message_;
		parent::render();
	}
}

?>