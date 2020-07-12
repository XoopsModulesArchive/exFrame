<?php
/**
@brief 画面遷移のためのコンフィグで、まだ暫定的
@note 変更の可能性が高い
@version $Id: ForwardConfig.php,v 1.3 2004/08/05 19:08:51 minahito Exp $
*/

define ("EXFORWARD_LOCATION",1);
define ("EXFORWARD_REDIRECT",2);
define ("EXFORWARD_REDIRECT_LONG",3);
define ("EXFORWARD_THROW",4);

class exSimpleForwardConfig {
	var $name_=null;
	var $path_=null;
	var $message_=null;
	var $type_=null;

	function exSimpleForwardConfig($name,$type,$path,$message=null) {
		$this->name_=$name;
		$this->type_=$type;
		$this->path_=$path;
		$this->message_=$message;
	}

	function doForward() {
		switch($this->type_) {
			case EXFORWARD_LOCATION:
				header("location: ".$this->path_);
				exit;
				break;
			
			case EXFORWARD_REDIRECT:
				redirect_header($this->path_,1,$this->message_);
				break;

			case EXFORWARD_REDIRECT_LONG:
				redirect_header($this->path_,3,$this->message_);
				break;

			case EXFORWARD_THROW:
				break;
		}
	}
}

class exSuccessForwardConfig extends exSimpleForwardConfig {
	function exSuccessForwardConfig($type,$path,$message=null) {
		parent::exSimpleForwardConfig('success',$type,$path,$message);
	}
}

class exFailForwardConfig extends exSimpleForwardConfig {
	function exFailForwardConfig($type,$path,$message=null) {
		parent::exSimpleForwardConfig('fail',$type,$path,$message);
	}
}

?>