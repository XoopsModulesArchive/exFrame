<?php
/**
@brief ��ǧ���̤� Confirm �򲡤���Form �����˹�ʤ���ȡ��ץ��å����� _process()
\par ��ƤӽФ�����ݡ��ͥ�ȥ��åȡ�
*/

require_once "exComponent/Preview.php";
require_once "exForm/PreviewForm.php";

require_once "exConfig/ForwardConfig.php";

require_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

/**
@note �������� ConfirmModelRender.php ���������Ƥ����Τ�侩
*/
class exTypicalConfirmComponent extends exPreviewComponent {
	var $quick_message_=null;
	var $handler_;
	
	function exTypicalConfirmComponent($processor=null,$render=null,$name=null,$form=null,$forwards=null) {
		if(!is_object($processor))
			$processor = new exTypicalConfirmComponentProcessor();
		if(!is_object($render))
			$render = new exComponentRender();
		if(!is_object($form))
			$form = new exConfirmTicketForm();

		parent::exPreviewComponent($processor,$render,$name,$form);

		$this->setForwards($forwards);
	}

	function init($obj,$handler=null,$mes=null) {
		$this->data_=&$obj;
		if($handler!==null)
			$this->handler_=$handler;
		if($mes!==null)
			$this->quick_message_=$mes;
		return $this->doProcess();
	}
}


/**
@brief ��Ϣ�ν�����ŵ��Ū����ȥ���Ȥ��Ƥ����᤿���ϤǱ��Ѥ������ʤ������פΥץ��å������饹
@return COMPONENT_INIT_SUCCESS �� FAIL �����֤��ʤ�
*/
class exTypicalConfirmComponentProcessor extends exComponentProcessor {
	function process(&$component) {
		// �Х�ǡ�������Ԥ�
		switch($ret=$component->form_->init($component->data_,$component->name_)) {
			// ���������ե�����˼��Ԥ������� COMPONENT_INIT_FAIL �˥��顼���ѹ������᤹
			// _processActionformInitFail() ���������Ƥ���Ф���򥳡��뤹��
			// ���顼���Ѵ������⤤�ޤϤ��������Ǽ������Ƥ���
			case ACTIONFORM_INIT_FAIL:
				$ret = $this->_processActionformInitFail($component);
				break;

			// ���������ե�������̾���������λ�������ϡ�COMPONENT_INIT_SUCCESS ���ѹ������᤹
			// _processActionformInitSuccess() ���������Ƥ���Ф���򥳡��뤹��
			// ���顼���Ѵ������⤤�ޤϤ��������Ǽ������Ƥ���
			case ACTIONFORM_INIT_SUCCESS:
				$ret = $this->_processActionformInitSuccess($component);
				break;

			case ACTIONFORM_POST_SUCCESS:
				$ret = $this->_processActionformPostSuccess($component);
				break;
		}

		return ($ret);
	}
	
	function _processActionformInitFail(&$component)
	{
		return COMPONENT_INIT_FAIL;
	}
	
	function _processActionformInitSuccess(&$component)
	{
		return COMPONENT_INIT_SUCCESS;
	}

	function _processActionformPostSuccess(&$component)
	{
		if($this->_processPost($component)) {
			if(isset($component->forwards_['success']))
				$component->forwards_['success']->doForward();
			else
				$ret=COMPONENT_INIT_SUCCESS;
		}
		else {
			if(isset($component->forwards_['fail']))
				$component->forwards_['fail']->doForward();
			else {
				$ret = $this->_processPostFail();
			}
		}

		return $ret;
	}
	
	// ����¼������٤����å�
	function _processPost($component)
	{
		return false;
	}

	// FailForward ����������Ƥ��ʤ��ä����˸ƤӽФ�������
	function _processPostFail($component)
	{
		return COMPONENT_INIT_FAIL;
	}

}

?>