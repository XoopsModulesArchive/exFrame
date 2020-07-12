<?php
/**
@file
@author
@version $Id$
*/

/**
@brief mojavi の View class とインターフェイスをあわせる抽象化クラス
*/
class mojaLE_AbstractView
{
	/**
	@brief このビュークラスを実行する際にコールされるメソッドです
	@return Renderer クラス
	*/
	function execute(&$controller,&$request,&$user)
	{
		$renderer = new mojaLE_NoneRenderer($controller,$request,$user);
		return $renderer;
	}
}

?>