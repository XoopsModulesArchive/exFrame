<?php
	/**
	@brief XoopsTpl �Υ�å�
	@remark �Хåե�����оݤ��ѿ��ΤȤ��� Smarty ���ڤ��ؤ��ޤ�
	@todo Smarty �ڤ��ؤ�����������ʤ�
	*/
	class mojaLE_XoopsTplRenderer extends mojaLE_AbstractRenderer
	{
    	function execute(&$controller,&$request,&$user)
    	{
			global $xoopsOption;
			global $xoopsTpl;

    		if($controller->getRenderMode() == RENDER_VAR ) {
				// ������ Smarty ���ڤ��ؤ�
				$smarty = new XoopsTpl();
				foreach($this->attribute_ as $key=>$val) {
					$smarty->assign($key,$val);
				}
				$this->result_ = $smarty->fetch("db:".$this->template_name_);
    		}
    		else {
    			// XoopsTpl ���Ф��ƻŻ��򤹤�
				$xoopsOption['template_main'] = $this->template_name_;
				foreach($this->attribute_ as $key=>$val) {
					$xoopsTpl->assign($key,$val);
				}
    		}
    	}
	}
?>