<?php
/**
@version $Id: PreviewForm.php,v 1.10 2004/08/05 19:08:43 minahito Exp $
*/

require_once "exForm/Form.php";
require_once "include/OnetimeTicket.php";

/**
@brief POST/GET を yes/no とするシンプルな ticket 付き Form
*/
class exConfirmTicketForm extends exAbstractActionForm {
	var $name_;
	var $ticket_;

	function init($name) {
		$this->name_=$name;

		$dmy=null;
		return parent::init($dmy);
	}

	function doGet($data=null) {
		$this->ticket_=new OnetimeTicket('ticket__'.$this->name_,300,EXFRAME_SALT);
		$this->ticket_->setSession();
	}

	function doPost($data=null) {
		if (!OnetimeTicket::inquiry('ticket__'.$this->name_)) {
			$this->msg_[]="Ticket Error";
		}
	}

	function getStructure($type='s') {
		$arr=array();

		if(is_object($this->ticket_)) {
    		$arr['__ticket__']['name']=$this->ticket_->getName();
    		$arr['__ticket__']['value']=$this->ticket_->getValue();
		}
		return $arr;
	}
}

/**
@brief init に exXoopsObject を必要とする
*/
class exObjectConfirmTicketForm extends exConfirmTicketForm {
	function init($data,$name) {
		$this->name_=$name;
		$this->data_=$data;
		
		$dmy=null;
		return parent::init($dmy);
	}

	function getStructure($type='s') {
		$arr=parent::getStructure($type);
		
		$arr=array_merge($arr,$this->data_->getArray($type));
		return $arr;
	}
}

/**
@brief init に ActionForm を必要とする
*/
class exFormConfirmTicketForm extends exConfirmTicketForm {
	function init($form,$name) {
		$this->name_=$name;
		$this->data_=$form;
		
		$dmy=null;
		return parent::init($dmy);
	}

	function getStructure($type='s') {
		$arr=parent::getStructure($type);
		
		$arr=array_merge($arr,$this->data_->data_->getArray($type));
		return $arr;
	}
}

/**
@brief バージョン互換のため
*/
class exBeanConfirmTicketForm extends exFormConfirmTicketForm
{
}

?>