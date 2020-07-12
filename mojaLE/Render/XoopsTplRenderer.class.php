<?php
	/**
	@brief XoopsTpl のラッパ
	@remark バッファリング対象が変数のときは Smarty に切り替えます
	@todo Smarty 切り替えが完全じゃない
	*/
	class mojaLE_XoopsTplRenderer extends mojaLE_AbstractRenderer
	{
    	function execute(&$controller,&$request,&$user)
    	{
			global $xoopsOption;
			global $xoopsTpl;

    		if($controller->getRenderMode() == RENDER_VAR ) {
				// ここで Smarty に切り替え
				$smarty = new XoopsTpl();
				foreach($this->attribute_ as $key=>$val) {
					$smarty->assign($key,$val);
				}
				$this->result_ = $smarty->fetch("db:".$this->template_name_);
    		}
    		else {
    			// XoopsTpl に対して仕事をする
				$xoopsOption['template_main'] = $this->template_name_;
				foreach($this->attribute_ as $key=>$val) {
					$xoopsTpl->assign($key,$val);
				}
    		}
    	}
	}
?>