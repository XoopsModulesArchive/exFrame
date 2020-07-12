<?php
/**
@version $Id: Preview.php,v 1.7 2004/07/26 23:09:47 minahito Exp $
*/

require_once "exComponent/Component.php";
require_once "exForm/PreviewForm.php";

require_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

/**
@brief 入力検査には通常の exActionForm 使用のこと
*/
class exPreviewComponent extends exForwardComponent {
	var $render_model_=null;
	
	function exPreviewComponent($processor=null,$render=null,$name=null,$form=null,$forwards=null) {
		parent::exForwardComponent($processor,$render,$name,$form);

		if($this->processor_ === null)
			$this->processor_= new exPreviewComponentProcessor();
		if($this->render_ === null)
			$this->render_= new exPreviewComponentRender();
		if($this->form_ === null)
			$this->form_= new exBeanConfirmTicketForm();

		$this->setForwards($forwards);
	}

	function init($model=null) {
		$this->render_model_=&$model;
		return $this->doProcess();
	}

}

class exPreviewComponentProcessor extends exComponentProcessor {
	function process(&$component) {
		// セッションよりフォームの復元
		$editform=Session::get($component->name_);

		// validation
		switch($ret=$component->form_->init($editform,$component->name_)) {
			case ACTIONFORM_INIT_SUCCESS:
				$ret = COMPONENT_INIT_SUCCESS;
				break;
		}

		return ($ret);
	}
}

class exPreviewComponentRender extends exComponentRender {
	function init(&$component) {
		$this->component_=&$component;
	}

	function render() {
		// PreviewForm::getStructure のコール
		$arr = $this->component_->form_->getStructure('e');
		
		$form = new XoopsThemeForm("PREVIEW_CONFIRM", "preview_confirm", $_SERVER['SCRIPT_NAME'], "POST" );

		foreach ( $arr as $key=>$val ) {
			if ( $key=="__ticket__" )
				$form->addElement(new XoopsFormHidden($val['name'],$val['value']));
			else {
				$form->addElement(new XoopsFormHidden($key,$val));
				$form->addElement(new XoopsFormLabel($key,$val));
			}
		}
       	$formtray = new XoopsFormElementTray("ACTION");
       	$formtray->addElement( new XoopsFormButton ( "", "save", "Confirm", "submit" ) );
       	$backButton = new XoopsFormButton ( "", "back", "Back", "button" );
       	$backButton->setExtra('onclick="javascript:history.go(-1);"');
       	$formtray->addElement($backButton);
        	
       	$form->addElement($formtray);
       	$form->display();
	}
}

?>