<?php
/**
@brief ���������ե����ब mode_ �ץ�ѥƥ�����äƤ���� hidden �����ߤ��������
@version $Id$
*/

require_once "exComponent/render/ConfirmModelRender.php";

class exConfirmComponentModeModelRender extends exConfirmComponentModelRender {
	var $model_;

	function render() {
		// PreviewForm::getStructure �Υ�����
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
						$form->addElement(new XoopsFormHidden($key,$val));	// FIXME:: �����ɬ�פʤΤ���is_numeric �˸��ꤹ��?
						$form->addElement(new XoopsFormLabel($keyname,$value));
				}

			}
		}

		// mode_ �ץ�ѥƥ������������
		if(isset($this->component_->form_->data_->mode_))
			$form->addElement(new XoopsFormHidden("mode",$this->component_->form_->data_->mode_));

       	$formtray = new XoopsFormElementTray("ACTION");
       	$formtray->addElement( new XoopsFormButton ( "", "save", "Submit", "submit" ) );
       	$backButton = new XoopsFormButton ( "", "back", "Back", "button" );
       	$backButton->setExtra('onclick="javascript:history.go(-1);"');
       	$formtray->addElement($backButton);
        	
       	$form->addElement($formtray);
       	
       	print $this->model_->headmessage_;
       	$form->display();
	}
}

?>
