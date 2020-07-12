<?php
/**
@version $Id: TypicalConfirmThrowProcessor.php,v 1.2 2004/10/04 11:41:38 minahito Exp $ 
*/

/**
@brief 戻り値に Component の値を使わず、ActionForm の値をスルーして返すことをバージョンアップ
\par 後も保証するロジックです。コントローラの中に処理を書きたい場合に便利です。 
\par このロジックを使用する場合、forward 等を渡す必要はありません
*/

require_once "exComponent/confirm/TypicalConfirm.php";

class TypicalConfirmObjectInsertProcessor extends exTypicalConfirmComponentProcessor
{
	function _processActionformInitFail(&$component)
	{
		return ACTIONFORM_INIT_FAIL;
	}
	
	function _processActionformInitSuccess(&$component)
	{
		return ACTIONFORM_INIT_SUCCESS;
	}

	function _processActionformPostSuccess(&$component)
	{
		return ACTIONFORM_POST_SUCCESS;
	}
}

?>