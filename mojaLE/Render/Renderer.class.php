<?php
/**
@version $Id: Renderer.class.php,v 1.5 2005/03/28 10:05:16 minahito Exp $
*/

	class mojaLE_Renderer extends mojaLE_AbstractRenderer
	{
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

			$template=$this->attribute_;
			
			// do!
			if($controller->getRenderMode() == RENDER_VAR ) {
				ob_start();
				require($file);
				$this->result_ = ob_get_contents();
				ob_end_clean();
			}
			else {
				require($file);
			}
    	}
	}
?>