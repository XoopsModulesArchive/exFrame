<?php

require_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

/**
@brief �����åȥ��饹���� Hidden ������������ѤΥ������
*/
class exXoopsFormOnetimeTicket extends XoopsFormHidden
{
	function exXoopsFormOnetimeTicket(&$ticket)
	{
		parent::XoopsFormHidden($ticket->name_,$ticket->value_);
	}
}


?>