<?php
/**
@file
@author
@version $Id$
*/

/**
@brief mojavi �� Action class �ȥ��󥿡��ե������򤢤碌����ݲ����饹
*/
class mojaLE_AbstractAction
{
	/**
	@brief ���Υ��������Υӥ��ͥ����å���¹Ԥ���ݤ˥����뤵���᥽�åɤǤ�
		���Υ᥽�åɤϼ�� GET/POST �ꥯ�����Ȥ��ڤ�ʬ����ݤ˻��Ѥ��ޤ���
	@remark ���Υ᥽�åɤ� Controller �������뤹��褦�ˤ��뤿��ˤ� getRequestMethods �᥽�åɤ�
		Ĵ�����ʤ��ƤϤ����ޤ���
	@note mojavi �Ȱۤʤꡢ�ǥե���ȤǤ� VIEW_NONE ���֤��ޤ�
	*/
	function getDefaultView(&$controller,&$request,&$user)
	{
		return VIEW_NONE;
	}

	/**
	@brief ���Υ��������Υӥ��ͥ����å���¹Ԥ���ݤ˥����뤵���᥽�åɤǤ�
	@note mojavi �Ȱۤʤꡢ�ǥե���ȤǤ� VIEW_NONE ���֤��ޤ�
	*/
	function execute(&$controller,&$request,&$user)
	{
		return VIEW_NONE;
	}
	
	/**
	@brief GET/POST �ɤ���Υꥯ�����ȥ᥽�åɤ� execute �᥽�åɤ򥳡��뤹�뤫�����ξ�����֤��ޤ�
	return REQ_GET ��  GET�� execute POST �� getDefaultView
	return REQ_POST �� POST�� execute GET �� getDefaultView �������뤵���褦�ˤʤ�ޤ���
	�ǥե���ȤǤ� return ( REQ_GET | REQ_POST ) �ˤʤäƤ��ޤ��Τǡ�
	�ꥯ�����ȥ᥽�åɤˤ�����餺 execute �᥽�åɤ������뤵��ޤ�
	@return int ��� REQ_GET,REQ_POST ���Ȥ߹�碌�Ƽ�����
	@note ����ư��μ����ϥ���ȥ��饯�饹�ǹԤ��Ƥ���
	*/
	function getRequestMethods()
	{
		return (REQ_GET | REQ_POST);
	}
	
	/**
	@brief ���Υ���������¹ԤǤ���桼�����ϥ�����ѤߤǤ���ɬ�פ����뤫�ɤ����ξ�����֤��ޤ�
	@return bool true ����������ѤߤΥ桼�����Ǥʤ���Ф��Υ���������¹ԤǤ��ʤ��ʤ�ޤ�
	@note ����ư��μ����ϥ���ȥ��饯�饹�ǹԤ��Ƥ���
	*/
	function isSecure()
	{
		return false;
	}
	
	/// original
	function isAdmin()
	{
		return false;
	}
}

?>