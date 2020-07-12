<?php
/**
@version $Id$
*/

class exCommon
{
	function getScriptName($scriptname)
	{
		if(false !== ($pos=strrpos($scriptname,"/"))) {
			return substr($scriptname,$pos);
		}
		return $scriptname;
	}
}

class exFacadeCommon
{
	function getScriptFilename()
	{
		static $filename;
		if($filename!=null)
			return $filename;
		return $filename=exCommon::getScriptName($_SERVER['SCRIPT_NAME']);
	}

	function getPath()
	{
		static $path;
		if($path!=null)
			return $path;

		return $path = exCommon::getScriptName($_SERVER['SCRIPT_NAME']).
			isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : ""; 
	}
}

?>