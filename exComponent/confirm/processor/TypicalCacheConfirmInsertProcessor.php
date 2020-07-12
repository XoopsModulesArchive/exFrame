<?php
/**
@brief ŵ��Ū�ʥ���ݡ��ͥ�ȤΤ���Τ����ŵ��Ū�ʥ��󥵡��ȼ¹ԥץ��å��� 
*/

require_once "xoops/cache.php";
require_once "exComponent/confirm/processor/TypicalConfirmInsertProcessor.php";

require_once "exComponent/confirm/TypicalConfirm.php";

class TypicalCacheConfirmObjectInsertProcessor extends exTypicalConfirmComponentProcessor
{
	function _processPost($component)
	{
		$component->data_->setDirty();
		if($ret=$component->handler_->insert($component->data_)) {
			exXoopsCache::moduleCacheClear();
			return $ret;
		}
		else
			return $ret;
	}
}

class TypicalCacheConfirmFormInsertProcessor extends exTypicalConfirmComponentProcessor
{
	function _processPost($component)
	{
		$component->data_->data_->setDirty();
		if($ret=$component->handler_->insert($component->data_->data_)) {
			exXoopsCache::moduleCacheClear();
			return $ret;
		}
		else
			return $ret;
	}
}

?>
