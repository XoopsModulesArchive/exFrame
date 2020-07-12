<?php
/**
@version $Id: Input.php,v 1.6 2004/08/05 19:08:47 minahito Exp $
*/

require_once "exComponent/Component.php";
require_once "exConfig/ForwardConfig.php";

/**
@brief 入力検査には通常の exActionForm 使用のこと
*/
class exInputComponent extends exForwardComponent {
	function exInputComponent($processor=null,$render=null,$name=null,$form=null,$forwards=null) {

		if($processor===null)
			$processor= new exInputComponentProcessor();

		if($render===null)
			$render= new exInputComponentRender();

		parent::exForwardComponent($processor,$render,$name,$form);

		$this->setForwards($forwards);
	
	}

	function init($data=null) {
		return $this->doProcess();
	}
}

// これは TypicalProcessor に該当する？
class exInputComponentProcessor extends exComponentProcessor {
	function process(&$component) {
		// validation
		switch($ret=$component->form_->initSelf()) {
			case ACTIONFORM_INIT_FAIL:
				return($ret);
				break;

			case ACTIONFORM_INIT_SUCCESS:
				$ret = COMPONENT_INIT_SUCCESS;
				break;

			case ACTIONFORM_POST_SUCCESS:
				if(isset($component->forwards_['success'])) {
					Session::register($component->name_,$component->form_);
					$component->forwards_['success']->doForward();
				}
				break;
		}
		return ($ret);
	}
}

class exInputComponentRender extends exComponentRender {
	function init($component) {
		$this->component_=$component;
	}

	function render() { }
}

?>