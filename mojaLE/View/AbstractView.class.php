<?php
/**
@file
@author
@version $Id$
*/

/**
@brief mojavi �� View class �ȥ��󥿡��ե������򤢤碌����ݲ����饹
*/
class mojaLE_AbstractView
{
	/**
	@brief ���Υӥ塼���饹��¹Ԥ���ݤ˥����뤵���᥽�åɤǤ�
	@return Renderer ���饹
	*/
	function execute(&$controller,&$request,&$user)
	{
		$renderer = new mojaLE_NoneRenderer($controller,$request,$user);
		return $renderer;
	}
}

?>