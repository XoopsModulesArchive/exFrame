<?php
/**
@brief ��󥿥�������å��դΥ��������ե������������Ƥ���ե�����
@todo init �򥹥ȥ�ƥ����ڤ��ؤ�
*/

require_once "exForm/Form.php";
require_once "include/OnetimeTicket.php";

/**
@brief �����åȵ�ǽ�դ��Υ��������ե�����Ǥ�
@note �����åȤ������Ǽ�ư������������ʤˤϥ����åȤξȲ�ɬ�פǡ��Ȳ�˼��Ԥ�������
	�����åȤ���ľ���ޤ����Ѿ����饹�Ǥ� getTicketErrorMessage �򥪡��С��饤�ɤ���Ŭ
	�ڤʥ��顼��å�������������Ƥ������Ȥ򤪴��ᤷ�ޤ�
*/
class exTicketActionFormEx extends exActionForm
{
	var $ticket_=null;	///< ��󥿥�������åȤΥ��󥹥���
	
	function init(&$master) {
		if($_SERVER['REQUEST_METHOD']=='POST') {
			$this->fetch($master);
			if(count($this->msg_))
				return ACTIONFORM_INIT_FAIL;
			else {
				// �����åȤ򥻥å���󤫤�ä��Ƥ���
				exOnetimeTicket::unsetSession($this->getTicketName());
				return ACTIONFORM_POST_SUCCESS;
			}
		}
		else {
			$this->load($master);
			$this->createTicket();
			return count($this->msg_) ? ACTIONFORM_INIT_FAIL : ACTIONFORM_INIT_SUCCESS;
		}
	}

	/**
	@brief ��󥿥�������åȤ��䤤��碌��ԤäƤ��� fetch
	@note �Ѿ����饹�ǥ����С��饤�ɤ���ݡ�ɬ�� parent::fetch($master) ���Ƥ�������
	*/
	function fetch(&$master)
	{
		parent::fetch($master);
		if(!exOnetimeTicket::inquiry($this->getTicketName())) {
			$this->addError($this->getTicketErrorMessage());
			// �����������åȤ�ȯ��
			$this->createTicket();
		}
	}

	/**
	@brief ��󥿥�������åȥ��饹���֤��ޤ�
	*/
	function &getOnetimeTicket()
	{
		return $this->ticket_;
	}

	/**
	@brief ��󥿥�������åȤ�ȯ��
	*/
	function createTicket()
	{
		$this->ticket_ = new exOnetimeTicket($this->getTicketName(),$this->getTicketLifetime(),EXFRAME_SALT);
		$this->ticket_->setSession();
	}

	/**
	@brief �����åȤ�ǧ��̾���֤��ޤ�
	@note ɬ�פ˱����ƷѾ����饹�ǥ����С��饤�ɤ�ԤäƤ�������
	@return string
	*/
	function getTicketName()
	{
		return get_class($this);
	}

	/**
	@brief �����åȤΥ饤�ե�������֤��ޤ����á�
	@note ɬ�פ˱����ƷѾ����饹�ǥ����С��饤�ɤ�ԤäƤ�������
	@return int
	*/
	function getTicketLifetime()
	{
		// 60ʬ���֤�
		return 3600;
	}

	/**
	@brief �����åȥ��顼���Υ��顼��å��������֤��ޤ���
	@return string
	*/
	function getTicketErrorMessage()
	{
		return "TICKET ERROR";
	}
}


?>