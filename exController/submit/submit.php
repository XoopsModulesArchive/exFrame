<?php
/**
@version $Id$
*/

require_once "exComponent/Input.php";
require_once "exController/Controller.php";

class GenericSubmitController4User
{
	var $processor_=null;
	var $render_;
	var $name_;
	var $actionform_;
	var $forwards_;
	var $compo_;

	var $headfile_;

	function doService()
	{
		// 権限の確認

		// コンポーネントの構築
		$this->compo_ = new exInputComponent(
			$this->processor_,
			$this->render_,
			$this->name_,
			$this->actionform_,
			$this->forwards_ );

        switch($ret=$this->compo_->init()) {
        	case COMPONENT_INIT_FAIL:
        		xoops_error ( "FATAL ERROR" );
        		break;
        
        	case ACTIONFORM_INIT_FAIL:
				if($this->headfile_)
					include_once($this->headfile_);
        		print $this->compo_->form_->getHtmlErrors();
        		$this->compo_->display();
        		break;
        
        	case COMPONENT_INIT_SUCCESS:
				if($this->headfile_)
					include_once($this->headfile_);
        		$this->compo_->display();
        		break;
        }
	}
}

class GenericSubmitController4Admin extends exAbstractGenericController
{
	var $processor_=null;
	var $render_;
	var $name_;
	var $actionform_;
	var $forwards_;
	var $compo_;

	function doService()
	{
		// 権限の確認

		// コンポーネントの構築
		$this->compo_ = new exInputComponent(
			$this->processor_,
			$this->render_,
			$this->name_,
			$this->actionform_,
			$this->forwards_ );

        switch($ret=$this->compo_->init()) {
        	case COMPONENT_INIT_FAIL:
        		xoops_error ( "FATAL ERROR" );
        		break;
        
        	case ACTIONFORM_INIT_FAIL:
        		xoops_cp_header();
				if($this->headfile_)
					include_once($this->headfile_);
        		print $this->compo_->form_->getHtmlErrors();
        		$this->compo_->display();
        		xoops_cp_footer();
        		break;
        
        	case COMPONENT_INIT_SUCCESS:
        		xoops_cp_header();
				if($this->headfile_)
					include_once($this->headfile_);
        		$this->compo_->display();
        		xoops_cp_footer();
        		break;
        }
	}
}
?>