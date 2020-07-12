<?php
/**
@brief レンダーにモデルを持たせて、そこからどう出力するかの情報を貰う方式の ConfirmRender
@version $Id: ConfirmModelRender.php,v 1.4 2004/08/07 12:03:14 minahito Exp $
*/

require_once "exComponent/Component.php";

class exConfirmComponentModelRender extends exComponentRender {
	var $model_;
	
	function exConfirmComponentModelRender($model=null) {
		if(is_object($model))
			$this->model_=$model;
		else
			$this->model_=new exConfirmRenderModel();
	}
	
	function init(&$component) {
		$this->component_=&$component;
	}
	
	function render() {
		// PreviewForm::getStructure のコール
		$arr = $this->component_->form_->getStructure('s');

		$this->model_->init($arr);

		$form = new XoopsThemeForm($this->model_->caption_,
			"preview_confirm", $_SERVER['SCRIPT_NAME'], "POST" );

		foreach ( $arr as $key=>$val ) {
			if(!$this->model_->isFilter($key)) {
				if ( $key=="__ticket__" ) {
					$form->addElement(new XoopsFormHidden($val['name'],$val['value']));
				}
				else {
					$keyname = $this->model_->getKeyName($key);
					$value = $this->model_->getValueAt($key);

					if(is_object($value))
						$form->addElement($value);
					else
						$form->addElement(new XoopsFormHidden($key,$val));	// FIXME:: これは必要なのか？is_numeric に限定する?
						$form->addElement(new XoopsFormLabel($keyname,$value));
				}

			}
		}
       	$formtray = new XoopsFormElementTray("ACTION");
       	$formtray->addElement( new XoopsFormButton ( "", "save", "Confirm", "submit" ) );
       	$backButton = new XoopsFormButton ( "", "back", "Back", "button" );
       	$backButton->setExtra('onclick="javascript:history.go(-1);"');
       	$formtray->addElement($backButton);
        	
       	$form->addElement($formtray);
       	
       	print $this->model_->headmessage_;
       	$form->display();
	}
}

/**
@brief 誤字バージョン対応
*/
class exConrimComponentModelRender extends exConfirmComponentModelRender { }

/**
@brief レンダーに対するモデル...;;
*/
class exConfirmRenderModel {
	var $caption_="PREVIEW_CONFIRM";
	var $headmessage_="";
	var $_keyname_=array();
	var $filter_=array();
	var $_array_=array();

	function init($array=null) {
		$this->_array_=$array;
	}

	function isFilter($key) {
		return in_array($key,$this->filter_);
	}

	/**
	@return int
	*/
	function getRowCount() {
		return (count($this->_array_)-count($this->filter_));
	}

	/**
	@return array string
	*/
	function getKeyName($key) {
		return isset($this->_keyname_[$key]) ? $this->_keyname_[$key] : strtoupper($key); 
	}

	function getValueAt($key) {
		if(!$this->isFilter($key)) {
    		if(strlen($key)) {
    			$method="getValueAt".ucfirst(strtolower($key));
        		if(method_exists($this,$method)) {
        			return call_user_func(array($this,$method),$key);
        		}
        		else {
	    			return $this->_array_[$key];
        		}
    		}
    		else
    			return null;
		}
		return null;
	}
}
?>
