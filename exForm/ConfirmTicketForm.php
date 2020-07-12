<?php
/**
@file
@brief Ticket 付の ConfirmForm 集
@note Ticket 無の ConfirmForm を作り、その継承とする設計に変更となる可能性があります
@version $Id$
*/

require_once "exForm/Form.php";
require_once "include/OnetimeTicket.php";

/**
@brief チケット付きのアクションフォームです
このアクションフォームは init 時に指定された name のチケットをセッションから取得できなかったときに
新しいチケットを発行してセッションに登録し、 ACTIONFORM_INIT_SUCCESS を返します
セッションにチケットがあった場合は親クラスの動作に従います。
@note 新しい PreviewForm にして既に非推奨
*/
class exConfirmTicketForm extends exAbstractActionForm {
	var $_name_;
	var $_message_= "Ticket Error";	///< チケットエラー時のメッセージ
	var $ticket_;

	function init($name) {
		$this->_name_=$name;

		if(exOnetimeTicket::isSession($this->_name_)) {
    		$dmy=null;
    		return parent::init($dmy);
		}
		else {
			$this->doGet();
			return count($this->msg_) ? ACTIONFORM_INIT_FAIL : ACTIONFORM_INIT_SUCCESS;
		}
	}

	function doGet($data=null) {
		$this->ticket_=new exOnetimeTicket($this->_name_,300,EXFRAME_SALT);
		$this->ticket_->setSession();
	}

	function doPost($data=null) {
		if (!exOnetimeTicket::inquiry($this->_name_)) {
			$this->msg_[]=$this->_message_;
		}
	}
	
	function setErrorMessage($str)
	{
		$this->_message_=$str;
	}

	function release()
	{
		$this->sessionClear();
	}

	function sessionClear() {
		exOnetimeTicket::unsetSession($this->_name_);
	}
}

?>
