<?php
/**
@file
@brief Ticket �դ� ConfirmForm ��
@note Ticket ̵�� ConfirmForm ���ꡢ���ηѾ��Ȥ����߷פ��ѹ��Ȥʤ��ǽ��������ޤ�
@version $Id$
*/

require_once "exForm/Form.php";
require_once "include/OnetimeTicket.php";

/**
@brief �����å��դ��Υ��������ե�����Ǥ�
���Υ��������ե������ init ���˻��ꤵ�줿 name �Υ����åȤ򥻥å���󤫤�����Ǥ��ʤ��ä��Ȥ���
�����������åȤ�ȯ�Ԥ��ƥ��å�������Ͽ���� ACTIONFORM_INIT_SUCCESS ���֤��ޤ�
���å����˥����åȤ����ä����Ͽƥ��饹��ư��˽����ޤ���
@note ������ PreviewForm �ˤ��ƴ�����侩
*/
class exConfirmTicketForm extends exAbstractActionForm {
	var $_name_;
	var $_message_= "Ticket Error";	///< �����åȥ��顼���Υ�å�����
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
