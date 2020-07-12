<?php

	if(!defined('EXFRAME_VERSION'))
	{
		define('EXFRAME_VERSION','0.8.9');
	}
	
	function __exframe_version__($version)
	{
		$tmp1=explode(".",$version);
		$tmp2=explode(".",EXFRAME_VERSION);
		
		// 上からチェック
		for($i=0;$i<3;$i++) {
			if($tmp1[$i]>=$tmp2[$i])
				return true;
		}

		if((float)($version)>=(float)(EXFRAME_VERSION))
		{
			if(function_exists(xoops_error)) {
				xoops_error('EXFRAME VERSION ERROR');
			}
			else {
				print "EXFRAME VERSION ERROR";
			}
			die;
		}
	}

?>