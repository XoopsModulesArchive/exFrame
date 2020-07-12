<?php
	require_once XOOPS_ROOT_PATH."/class/smarty/Smarty.class.php";

	class mojaLE_SmartyRenderer extends mojaLE_AbstractRenderer
	{
		var $tpl_;

		function mojaLE_SmartyRenderer(&$controller,&$request,&$user)
    	{
    		parent::mojaLE_AbstractRenderer($controller,$request,$user);
    		$this->tpl_ = new Smarty();
    		$this->tpl_->compile_dir = XOOPS_COMPILE_PATH;
    		$this->tpl_->template_dir = $controller->getModuleDir() . "/" . MOJALE_TEMPLATE_DIRNAME;
    	}

    	function execute(&$controller,&$request,&$user)
    	{
    		if(!$this->template_name_) {
    			$error="A template has not been specified";
				trigger_error($error, E_USER_ERROR);
				exit;
    		}
    		
    		// テンプレートファイルの合成
			$file = $controller->getModuleDir() . "/" . MOJALE_TEMPLATE_DIRNAME . "/" . $this->template_name_;
			if(!is_readable($file)) {
    			$error="Template file $file does not exist or is not readble";
	            trigger_error($error, E_USER_ERROR);
				exit;
			}
			
			if($controller->getRenderMode() == RENDER_VAR ) {
				$this->result_ = $this->tpl_->fetch("file:".$this->template_name_);
			}
			else {
	    		$this->tpl_->display("file:".$this->template_name_);
			}
    	}
    	
    	function setAttribute($key,&$value) {
    		parent::setAttribute($key,$value);
    		$this->tpl_->assign($key, $value);
    	}
    	
	}
?>