<?php
/**
@version $Id: TypicalConfirmThrowProcessor.php,v 1.2 2004/10/04 11:41:38 minahito Exp $ 
*/

/**
@brief ����ͤ� Component ���ͤ�Ȥ鷺��ActionForm ���ͤ򥹥롼�����֤����Ȥ�С�����󥢥å�
\par ����ݾڤ�����å��Ǥ�������ȥ������˽�����񤭤������������Ǥ��� 
\par ���Υ��å�����Ѥ����硢forward �����Ϥ�ɬ�פϤ���ޤ���
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