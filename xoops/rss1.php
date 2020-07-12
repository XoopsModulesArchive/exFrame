<?php

require_once "exTra/RSS1Handler.php";

class XoopsRSS1Handler extends RSS1Handler
{
	function setChannelTitle($title)
	{
		parent::setChannelTitle(xoops_utf8_encode(htmlspecialchars($title)));
	}

	function addItem($title=null,$link=null,$desc=null,$pubdate=null,$guid=null)
	{
		$title=xoops_utf8_encode(htmlspecialchars($title));
		$desc=xoops_utf8_encode(htmlspecialchars($desc));
		$this->items_[]=new RSS1ItemHandler($title,$link,$desc,$pubdate,$guid);
	}
	
	function display()
	{
		header ('Content-Type:text/xml; charset=utf-8');
		print $this->render();
	}

	function save($file)
	{
		$fp = fopen($file,"w");
		if($fp) {
			$text=$this->render();
			fwrite($fp,$text);
			fclose ($fp);
			return true;
		}
		else
			return false;
	}
	
}

?>
