<?php
/**
@brief ŵ��Ū�ʥ���ݡ��ͥ�ȤΤ���Τ����ŵ��Ū�ʺ���ץ��å��� 
*/

require_once "exComponent/confirm/TypicalConfirm.php";

class TypicalConfirmObjectDeleteProcessor extends exTypicalConfirmComponentProcessor
{
	function _processPost($component)
	{
		return $component->handler_->delete($component->data_);
	}
}

class TypicalConfirmFormDeleteProcessor extends exTypicalConfirmComponentProcessor
{
	function _processPost($component)
	{
		return $component->handler_->delete($component->data_->data_);
	}
}

?>
