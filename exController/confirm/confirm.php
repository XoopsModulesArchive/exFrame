<?php
/**
@version $Id$
*/

require_once "exComponent/confirm/TypicalConfirm.php";
require_once "exComponent/confirm/render/ConfirmModelRender.php";
require_once "exConfig/ForwardConfig.php";

class GenericConfirmController4Admin
{
	var $processor_=null;
	var $render_=null;
	var $name_;
	var $actionform_=null;
	var $forwards_;
	var $compo_;

	var $handler_;
	var $obj_;
	
	function doService()
	{
		if($this->render_===null)
			$this->render_=new exConfirmComponentModelRender();

		if($this->actionform_===null)
			$this->actionform_=new exBeanConfirmTicketForm();


		$this->compo_ = new exTypicalConfirmComponent(
			$this->processor_,
			$this->render_,
			$this->name_,
			$this->actionform_,
			$this->forwards_ );

		switch($ret=$this->compo_->init($this->obj_,$this->handler_)) {
			case COMPONENT_INIT_FAIL:
				xoops_cp_header();
				xoops_error("FATAL ERROR");
				xoops_cp_footer();
				break;

				default:
				xoops_cp_header();
				$this->compo_->display();
				xoops_cp_footer();
				break;
		}
	}
}

?>