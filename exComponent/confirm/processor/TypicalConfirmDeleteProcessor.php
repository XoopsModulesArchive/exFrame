<?php
/**
@brief 典型的なコンポーネントのためのさらに典型的な削除プロセッサー 
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
