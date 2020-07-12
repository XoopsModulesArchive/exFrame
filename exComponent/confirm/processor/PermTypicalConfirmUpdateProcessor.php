<?php
/**
@brief ŵ��Ū�ʥѡ��ߥå���󥳥�ݡ��ͥ�ȤΤ���˻��Ѥ��빹���ѥץ��å��� 
*/

require_once "xoops/cache.php";
require_once "exComponent/confirm/TypicalConfirm.php";
require_once "exComponent/confirm/processor/TypicalConfirmInsertProcessor.php";

require_once "xoops/perm.php";

class PermTypicalConfirmUpdateProcessor extends exTypicalConfirmComponentProcessor
{
	function _processPost($component)
	{
		global $xoopsModule;
		$handler=exXoopsGroupPermHandler::getInstance();

		// �ޤ��ѡ��ߥå��������ƾä�
		$mid=$xoopsModule->mid();

		$criteria=new CriteriaCompo();
		$criteria->add(new Criteria("gperm_modid",$mid));
		$criteria->add(new Criteria("gperm_itemid",$component->data_->item_id_));

		$handler->deletes($criteria);
		
		// �ҤȤĤ��ļ��Ф��ʤ������
		$perms=&$component->data_->group_perms_;
		
		$ret = true;

		foreach(array_keys($perms) as $name) {
			foreach(array_keys($perms[$name]) as $gid) {
				$perm = $handler->create();
				$perm->setVar('gperm_groupid',$gid);
				$perm->setVar('gperm_modid',$mid);
				$perm->setVar('gperm_itemid',$component->data_->item_id_);
				$perm->setVar('gperm_name',$name);
				if(!$handler->insert($perm))
					$ret=false;
			}
		}
		
		return $ret;
	}
}

?>
