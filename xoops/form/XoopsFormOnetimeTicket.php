<?php

require_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

/**
@brief チケットクラスから Hidden を作成する専用のエレメント
*/
class exXoopsFormOnetimeTicket extends XoopsFormHidden
{
	function exXoopsFormOnetimeTicket(&$ticket)
	{
		parent::XoopsFormHidden($ticket->name_,$ticket->value_);
	}
}


?>