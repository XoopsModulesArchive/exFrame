<?php
/**
@brief ŵ��Ū�ʥ���ݡ��ͥ�ȤΤ���Τ����ŵ��Ū�ʥ��󥵡��ȼ¹ԥץ��å��� 
*/

require_once "exComponent/confirm/TypicalConfirm.php";

class TypicalConfirmObjectInsertProcessor extends exTypicalConfirmComponentProcessor
{
	function _processPost($component)
	{
		$component->data_->setDirty();
		return $component->handler_->insert($component->data_);
	}
}

class TypicalConfirmFormInsertProcessor extends exTypicalConfirmComponentProcessor
{
	function _processPost($component)
	{
		$component->data_->data_->setDirty();
		return $component->handler_->insert($component->data_->data_);
	}
}

?>
