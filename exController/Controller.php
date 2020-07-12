<?php
/**
@note exController の基底を将来的に定義したいファイル
@version $Id: Controller.php,v 1.2 2004/08/17 12:03:56 minahito Exp $
*/

require_once "exForm/Form.php";

/**
@brief とりあえず形だけ
*/
class exAbstractGenericController extends exAbstractFormObject
{
	var $headfile_;
	
	function setHeadFile($file)
	{
		$this->headfile_;
	}
	
	function doService()
	{
	}
}


?>