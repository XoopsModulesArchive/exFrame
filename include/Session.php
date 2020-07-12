<?php
/**
@file
@brief ���å����Ͽ�Τ���� class
@note �����ƥ�Υ��å����Ͽ���ˤˤ��碌�뤿��Υ�åѡ��Ȥ���¾���ܥ����ɤ� $_SESSION ľ���Ȥ�����
*/

if(!defined('EXFRAME_SESSION_PREFIX'))
	define('EXFRAME_SESSION_PREFIX','__exf__');

/**
@brief ���å������갷������Υ�åѡ��Ǥ�
@deprecated ���Υ��饹̾�ϥ���եꥯ�Ȥ򵯤����䤹�������ؤȤ��� exSession ���饹����Ѥ��Ƥ�������
���Υ��饹�� 1.00 �ޤǤ˺�������ͽ��Ǥ���
*/
class Session {
	/**
	@brief ���ꤷ��̾���ǥ��å�����ͤ���¸���ޤ��ʥ��饹�᥽�åɡ�
	@param $name ���å����̾
	@param $value ��
	@note �ºݤ���¸̾�ˤ� EXFRAME_SESSION_PREFIX �ȥ���������������Ĥ��û�����ޤ���
	*/
	function register($name,$value) {
		$name=EXFRAME_SESSION_PREFIX."__".$name;
		$_SESSION[$name]=$value;
	}

	/**
	@brief ���ꤷ��̾���ǥ��å�����ͤ��֤��ޤ��ʥ��饹�᥽�åɡ�
	@param $name ���å����̾
	@return �쥸���Ȥ���Ƥ�����
	@note �ºݤ���¸̾�ˤ� EXFRAME_SESSION_PREFIX �ȥ���������������Ĥ��û�����ޤ���
	*/
	function &get($name) {
		$name=EXFRAME_SESSION_PREFIX."__".$name;
		if(isset($_SESSION[$name]))
			return $_SESSION[$name];
		else
			return false;
	}

	/**
	@brief ���ꤷ��̾���ǥ��å�����ͤ��֤������ȡ��ͤ������ޤ��ʥ��饹�᥽�åɡ�
	@param $name ���å����̾
	@return �쥸���Ȥ���Ƥ�����
	@note �ºݤ���¸̾�ˤ� EXFRAME_SESSION_PREFIX �ȥ���������������Ĥ��û�����ޤ���
	*/
	function &pop($name) {
		$name=EXFRAME_SESSION_PREFIX."__".$name;
		if(isset($_SESSION[$name])) {
			$ret=$_SESSION[$name];
			unset($_SESSION[$name]);
			return $ret;
		}
		else
			return false;
	}

	/**
	@brief POST �ꥯ�����Ȥξ�� get, �����Ǥʤ���� pop ��ƤӽФ���åѡ��ʥ��饹�᥽�åɡ�
	@param $name ���å����̾
	@return �쥸���Ȥ���Ƥ�����
	@note �ºݤ���¸̾�ˤ� EXFRAME_SESSION_PREFIX �ȥ���������������Ĥ��û�����ޤ���
	*/
	function &postPop($name) {
		if(exFrame::isPost())
			return Session::pop($name);
		else
			return Session::get($name);
	}
	
	/**
	@brief ���ꤷ��̾���Υ��å�����ͤ����뤫�ɤ���Ĵ�����ޤ��ʥ��饹�᥽�åɡ�
	@param $name ���å����̾
	@return ¸�ߤ��Ƥ���п������Ƥ��ʤ���е����֤��ޤ�
	@note �ºݤ���¸̾�ˤ� EXFRAME_SESSION_PREFIX �ȥ���������������Ĥ��û�����ޤ���
	*/
	function is_registered($name){
		$name=EXFRAME_SESSION_PREFIX."__".$name;

		return isset($_SESSION[$name]);
	}

	/**
	@brief ���ꤷ��̾���Υ��å�����ͤ������ޤ��ʥ��饹�᥽�åɡ�
	@param $name ���å����̾
	@note �ºݤ���¸̾�ˤ� EXFRAME_SESSION_PREFIX �ȥ���������������Ĥ��û�����ޤ���
	*/
	function unregister($name){
		$name=EXFRAME_SESSION_PREFIX."__".$name;
		unset($_SESSION[$name]);
	}

	function addRole($major,$minor,$lifetime=0) {
		$role=array();
		if(Session::is_registered('exRole'))
			$role=&Session::get('exRole');

		if($lifetime) $lifetime+=time();
		$role[$major.":".$minor]=$lifetime;

		Session::register('exRole',$role);
	}

	function clearRole($major,$minor) {
		if(Session::is_registered('exRole')) {
			$role=&Session::get('exRole');
			unset($role[$major.":".$minor]);
			
			Session::register('exRole',$role);
		}
	}

	function isRole($major,$minor) {
		if(Session::is_registered('exRole')) {
			$role=&Session::get('exRole');
			if(!isset($role[$major.":".$minor]))
				return false;

			if(time()>$role[$major.":".$minor] && $role[$major.":".$minor]!==0 ) {
				Session::clearRole($major,$minor);
				return false;
			}

			return true;
		}
		else
			return false;
	}
}

/**
@brief ���å����ȤΤ��Ȥ��Ԥ�����Υ�åѡ����饹
���� exSession �ϥ��å����ؤ���Ͽ�����ͤ򥷥ꥢ�벽���ޤ���
����ˤ�� xoops ��ͭ�� session_start �Υ��ӥ��ʥ����ߥ󥰤򾯤����餹���Ȥ��Ǥ��ޤ�
get �Ǽ�������ޤǤ˥��饹��������Ƥ����� session_start ��Ǥ��äƤ⹽���ޤ���
*/
class exSession extends Session {
	/**
	@brief ���ꤷ��̾���ǥ��å�����ͤ���¸���ޤ��ʥ��饹�᥽�åɡ�
	���ΤȤ������ꥢ�饤������Ѥ�������������ޤ�
	@param $name ���å����̾
	@param $value ��
	@note �ºݤ���¸̾�ˤ� EXFRAME_SESSION_PREFIX �ȥ���������������Ĥ��û�����ޤ���
	*/
	function register($name,&$value) {
		$name=EXFRAME_SESSION_PREFIX."__".$name;
		$_SESSION[$name]=serialize($value);
	}

	/**
	@brief ���ꤷ��̾���ǥ��å�����ͤ��֤��ޤ��ʥ��饹�᥽�åɡ�
	@param $name ���å����̾
	@return �쥸���Ȥ���Ƥ�����
	@note �ºݤ���¸̾�ˤ� EXFRAME_SESSION_PREFIX �ȥ���������������Ĥ��û�����ޤ���
	*/
	function &get($name) {
		$ret=null;
		$name=EXFRAME_SESSION_PREFIX."__".$name;
		if(isset($_SESSION[$name]))
			$ret = unserialize($_SESSION[$name]);

		return $ret;
	}
}

?>