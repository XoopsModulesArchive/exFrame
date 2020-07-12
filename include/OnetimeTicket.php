<?php
/**
@note ESruts ����� port
@version $Id: OnetimeTicket.php,v 1.8 2005/03/20 05:29:20 minahito Exp $

\section copyright Copyright and license
 Copyright (c) 2003-2004, minahito
 All rights reserved.

 Redistribution and use in source and binary forms, with or without
 modification, are permitted provided that the following conditions are met:

1 Redistributions of source code must retain the above copyright notice, 
  this list of conditions and the following disclaimer.

2 Redistributions in binary form must reproduce the above copyright notice, 
  this list of conditions and the following disclaimer in the documentation
  and/or other materials provided with the distribution.

3 Neither the name of the nor the names of its contributors may be used to 
  endorse or promote products derived from this software without specific 
  prior written permission.

 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS
 BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, 
 OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
 SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
 INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
 WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF 
 SUCH DAMAGE.
*/

/**
@brief ��󥿥�������åȥ��饹
@remark ���Υ�󥿥�������åȥ��饹�� Session ���饹���Ѥ��ƥ��å����ؤ���Ͽ��ԤäƤ���
���ᡢ�ȡ�������ͤ�Ȳ񤹤�ݡ����å���󥹥����ȤΥ����ߥ󥰤��θ���������ǥ��󥰤�ɬ�פȤʤꡢ
�����ߥ󥰤����ӥ��Ǥ���
@deprecated exFrame �ˤ����Ƥϡ�����ڤ˼�갷���� exOnetimeTicket ��侩���ޤ�
*/
class OnetimeTicket {
	var $name_;		///< �����åȤθ��̤μ��̻ҡ�̾����
	var $value_;		///< �����åȤΥȡ�������
	var $lifetime_;	///< �ȡ������ͭ�����֡��á�

	/**
	@param $name �����åȤμ��̻�
	@param $lifetime �ȡ������ͭ������
	@param $salt �ȡ������ͤ�ȯ��������ݤ�Ϳ����SALT(��)
	*/
	function OnetimeTicket($name,$lifetime=300,$salt='') {
		$this->name_=$name;
		$this->lifetime_=time()+$lifetime;
		$this->value_=md5($salt.microtime()*100000);
	}
	
	/**
	@brief ���Υ����åȤ򥻥å�������Ͽ���ޤ�
	*/
	function setSession() {
		if(isset($_SESSION[TICKET_PREFIX.$this->name_]))
			unset($_SESSION[TICKET_PREFIX.$this->name_]);
		$_SESSION[TICKET_PREFIX.$this->name_]=$this;
	}

	/**
	@brief ���ꤵ�줿���̻ҤΥ����åȤ򥻥å���󤫤���Ф����֤��ޤ���
	���Υ᥽�åɤϡ����å����Ȥ�ľ�ܤΤ��Ȥ���åԥ󥰤������饹�᥽�åɤǤ���
	*/
	function &getSession($name)
	{
		return $_SESSION[TICKET_PREFIX.$name];
	}

	/**
	@brief ���Υ᥽�åɤ� getSession �Υ��ꥢ���Ǥ�
	*/
	function &getInstance($name) {
		return OnetimeTicket::getSession ( $name );
	}

	/**
	@brief ���Υ����åȤμ��̻ҡ�̾�Ρˤ��֤��ޤ�
	@return string
	*/
	function getName() {
		return $this->name_;
	}

	/**
	@brief ���Υ����åȤΥȡ������ͤ��֤��ޤ�
	@return string
	*/
	function getValue() {
		return $this->value_;
	}

	function inquiry($name=null) {
		$value="";
		
		if($name!=null) {
			$instance=&OnetimeTicket::getInstance($name);
			if(is_object($instance))
				return $instance->inquiry();
			else
				return false;
		}
		else {
    		if(time()>$this->lifetime_)
    			return false;
    		
    		if($_SERVER['REQUEST_METHOD']=='GET') {
    			$value=$_GET[$this->name_];
    		}
    		elseif($_SERVER['REQUEST_METHOD']=='POST') {
    			$value=$_POST[$this->name_];
    		}

			unset($_SESSION[TICKET_PREFIX.$this->name_]);

    		if($this->value_==$value)
    			return true;
    		else
    			return false;
		}
	}

	function makeHTMLhidden() {
		$ret = "<input type='hidden' name='". $this->name_."' value='". $this->value_."' />";
		return $ret;
	}
}

/**
@brief ���å����ȤΤ��Ȥ�� exSession ���饹����Ѥ����󥿥�������åȥ��饹
*/
class exOnetimeTicket extends OnetimeTicket {
	var $name_;
	var $value_;
	var $lifetime_;

	/**
	@param $name �����åȤμ��̻�
	@param $lifetime �ȡ������ͭ������
	@param $salt �ȡ������ͤ�ȯ��������ݤ�Ϳ����SALT(��)��
				���ꤷ�ʤ���� EXFRAME_SALT ���Ȥ��ޤ�
	*/
	function exOnetimeTicket($name,$lifetime=300,$salt=EXFRAME_SALT) {
		$this->name_=$name;
		$this->lifetime_=time()+$lifetime;
		$this->value_=md5($salt.microtime()*100000);
	}

	function setSession() {
		if(exSession::is_registered( TICKET_PREFIX . $this->name_ ))
			exSession::unregister( TICKET_PREFIX . $this->name_ );

		exSession::register(TICKET_PREFIX.$this->name_,$this);
	}

	/**
	@brief $name �Υ����åȤ����å�������¸����Ƥ��뤫�ɤ�����Ĵ�٤ޤ�
	@return boolean
	*/
	function isSession($name) {
		return exSession::is_registered(TICKET_PREFIX.$name);
	}

	function unsetSession($name)
	{
		return exSession::unregister(TICKET_PREFIX.$name);
	}

	function &getSession($name)
	{
		return exSession::get(TICKET_PREFIX.$name);
	}

	function &getInstance($name) {
		return exSession::get(TICKET_PREFIX.$name);
	}

	function inquiry($name=null) {
		$value="";
		
		if($name!=null) {
			$instance=&exOnetimeTicket::getInstance($name);
			if(is_object($instance))
				return $instance->inquiry();
			else
				return false;
		}
		else {
    		if(time()>$this->lifetime_)
    			return false;
    		
    		if($_SERVER['REQUEST_METHOD']=='GET') {
    			$value=$_GET[$this->name_];
    		}
    		elseif($_SERVER['REQUEST_METHOD']=='POST') {
    			$value=$_POST[$this->name_];
    		}

			exSession::unregister(TICKET_PREFIX.$this->name_);

    		if($this->value_==$value)
    			return true;
    		else
    			return false;
		}
	}
}


?>