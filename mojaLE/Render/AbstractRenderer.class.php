<?php
	define("MOJALE_TEMPLATE_DIRNAME", "templates" );

    class mojaLE_AbstractRenderer
    {
    	var $controller_;
    	var $request_;
    	var $user_;

    	var $attribute_;
    	
    	var $template_name_;
    	
    	var $result_;	///< レンダーモード指定による内部バッファリング用変数
    	
    	function mojaLE_AbstractRenderer(&$controller,&$request,&$user)
    	{
    		$this->controller_=$controller;
    		$this->request_=$request;
    		$this->user_=$user;
    		$this->template_name_="";
    		$this->attribute_ = array();
    	}
    	
		function &getAttribute($key)
		{
			return isset($this->attribute_[$key]) ? $this->attribute_[$key] : null;
		}

		function setAttribute($key,$value)
		{
			$this->attribute_[$key] = $value;
		}
		
		function setAttributeByRef($key,&$value)
		{
			$this->attribute_[$key] =& $value;
		}

		/// researve
    	function isIncludeFooter()
    	{
    		return false;
    	}
    	
		/// researve
    	function isCpFooter()
    	{
    		return false;
    	}
    	
    	function execute(&$controller,&$request,&$user)
    	{
    	}
    	
    	function setTemplate($name)
    	{
    		$this->template_name_ = trim($name);
    	}

		// FIXME::
		function templateExists($template, $dir=null)
		{
			$dir = dirname($template) . "/";
			$template = basename($template);
			
			return is_readble($dir.$template);
		}

		function &fetchResult()
		{
			return $this->result_;
		}
    }
?>