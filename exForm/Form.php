<?php
/**
@file
@version $Id: Form.php,v 1.12 2005/03/19 06:18:11 minahito Exp $
*/

define ( "ACTIONFORM_INIT_FAIL", '__error__actionform_init_fail__' );
define ( "ACTIONFORM_INIT_SUCCESS", '__actionform_init_success__' );
define ( "ACTIONFORM_POST_SUCCESS", '__actionform_post_success__' );

/**
@brief ����������
@note exAbstractForm �Ⱥ����礹���ǽ�����礤�ˤ���ΤǤ����������ĥ��ʤ��ǲ�����
*/
class exAbstractFormObject {
	var $msg_;				///< ���顼��å������Хåե�
	var $err_render_=null;	///< ���顼��å������������󥰤��� exFormErrorRender ���饹�Υ��󥹥��󥹤��ݻ�����

	function exAbstractFormObject() {
		$this->msg_=array();
		$this->err_render_=new exFormErrorRender();
	}

	/**
	@brief $msg �򥨥顼��å������Хåե����ɲä���
	@param $msg ���顼��å�����ʸ����
	@return �ʤ�
	*/
	function addError($msg) {
		$this->msg_[]=$msg;
	}

	/**
	@brief ���顼��å������Хåե��˥�å���������Ͽ�����뤫�ɤ�����Ĵ�٤ޤ���
	@return ���顼��å������������ true �ʤ���� false ���֤��ޤ�
	*/
	function isError() {
		return count($this->msg_);
	}

	/**
	@brief ���顼��å�������ʸ������֤��ޤ���
	@return string
	*/
	function getHtmlErrors() {
		$this->err_render_->init($this);
		return $this->err_render_->render();
	}
}

/**
@brief ����(�� AbstractBase)
@deprecated ���� XoopsObject �Ȥ�Ϣ�Ȥ�����˥ǥ����󤵤줿���������ե��������侩�Ǥ���
�ۤ�Ʊ�ͤ�ư���Ԥ� exAbstractActionFormEx �����إ��饹�Ȥ��ƻ��ѤǤ��ޤ���
���Υ��饹�ϲ��Υ���ݡ��ͥ�ȤΰܹԤ������ޤǤδ֤ϻĤ���ޤ���������Ū�ˤϺ������ޤ���
*/
class exAbstractForm extends exAbstractFormObject {
	var $data_;	///< ������᥽�åɤ��Ϥ��줿���󥹥��󥹤򥭡��פ���
	
	function init($data=null) {
		if($data) {
			$this->data_=&$data;
		}
		if($_SERVER['REQUEST_METHOD']=='POST') {
			$this->doPost($data);
			return count($this->msg_) ? ACTIONFORM_INIT_FAIL : ACTIONFORM_POST_SUCCESS;
		}
		elseif($_SERVER['REQUEST_METHOD']=='GET') {
			$this->doGet($data);
			return count($this->msg_) ? ACTIONFORM_INIT_FAIL : ACTIONFORM_INIT_SUCCESS;
		}
		else {
			return ACTIONFORM_INIT_FAIL;
		}
		
	}

	function doGet($data) { }

	function doPost($data) { }

	function initSelf() {
		$dmy=null;
		return $this->init($dmy);
	}
}

class exAbstractActionForm extends exAbstractForm {
	/**
	@biref ���� numValue �� min �� max ���ϰϤ˼��ޤäƤ��뤫�򸡺����ޤ�
	@param $numValue �����оݤȤʤ����
	@param $min �����ϰϤκǾ���
	@param $max �����ϰϤκ�����
	@return bool min �ʲ� max �ʾ�Ǥ���� true �����Ǥʤ���� false ���ᤷ�ޤ�
	*/
	function validateInRange($numValue,$min,$max) {
		return ($numValue>=$min && $numValue<=$max);
	}

	/**
	@brief ʸ���� string ��ʸ���� max ʸ���ʲ����������ޤ�
	@param $string �����оݤ�ʸ����
	@param $max ���ꤹ��ʸ�����κ�����
	@return bool ����ʸ������˼��ޤäƤ���� true �����Ǥʤ���� false �����ޤ�
	*/
	function validateMaxLength($string,$max) {
		return (strlen($string)<=$max);
	}

	/**
	@brief ʸ���� string ��ʸ���� min ʸ���ʾ夫�������ޤ�
	@param $string �����оݤ�ʸ����
	@param $max ���ꤹ��ʸ�����κǾ���
	@return bool ����ʸ�����ʾ夢��� true �����Ǥʤ���� false �����ޤ�
	*/
	function validateMinLength($string,$min) {
		return (strlen($string)>=$min);
	}

	function validatePositive($value) {
		return (intval($value)>0);
	}

	function validateHttpUrl($value) {
		return (strpos($value,"http://")===0 or strpos($value,"https://")===0);
	}

	function getPositive($value) {
		$ret=intval($value);
		if($ret<=0)
			return 0;
		else
			return $ret;
	}
}

/**
@brief ���Ū�ʥ��������ե�����
@todo exAbstractFormObject ����δ�����ڤ��ؤ���
*/
class exActionForm extends exAbstractActionForm {
	/**
	@brief ���Υ��������ե������ꥯ�����Ȥ˱����Ƽ¹Ԥ���
	ActionForm �� POST �ꥯ�����Ȼ��Τߤ� fetch ��Ԥ�
	mojaLE ����Ȥ��䤹�����뤿��� load/doGet �Ȥ�Ϣư�ϹԤ�ʤ�
	@return ACTIONFORM_XXXX
	@note ����ͤˤĤ���
		POST �ꥯ�����Ȼ���fetch �����꤬ȯ������� ACTIONFORM_INIT_FAIL ��
		���꤬ȯ�����ʤ���� ACTIONFORM_POST_SUCCESS ���֤��ޤ�
		POST �ꥯ�����Ȱʳ��ξ��� ACTIONFORM_INIT_SUCCESS ���֤��ޤ�
	*/
	function init($master) {
		if($_SERVER['REQUEST_METHOD']=='POST') {
			$this->fetch();
			return count($this->msg_) ? ACTIONFORM_INIT_FAIL : ACTIONFORM_POST_SUCCESS;
		}
		else {
			return ACTIONFORM_INIT_SUCCESS;
		}
	}

	/**
	@brief �ꥯ�����Ȥʤɤ���ǡ�������Ф������������ե�����˥ǡ�����ή������
	*/
	function fetch() {
	}

	/**
	@brief �Ϥ��줿�ޥ������ǡ������餳�Υ��������ե�����Υ��Ф��ۤ���
	@param $master �ǡ���
	*/
	function load(&$master) {
	}

	/**
	@brief �Ϥ��줿�ޥ������ǡ����ˤ��Υ��������ե�����ξ�����Ϥ�
	@param $master �ǡ���
	*/
	function update(&$master) {
	}
}

/**
@brief GET/POST ��ư��˱����Ƶ�ư���ѹ����륢�������ե�����
���Υ��������ե�����ϡ� GET �ꥯ�����Ȥκݤ� load �᥽�åɤ�POST �ꥯ�����Ȥκݤ� fetch �᥽�åɤ�
�¹Ԥ��ޤ���
\par ���Υ��������ե������ mojaLE ¦�� GET/POST ���ڤ�ʬ����Ԥ�ʤ���硢mojaLE ��Ȥ�ʤ����ˡ���
�ץե졼����Ū�ʵ�ư�򤷤ޤ��Τ������Ǥ���
*/
class exActionFormEx extends exAbstractActionForm {
	/**
	@brief ���Υ��������ե������ꥯ�����Ȥ˱����Ƽ¹Ԥ��롣
	\par ActionForm �� POST �ꥯ�����Ȼ��Τߤ� fetch ��Ԥ���
	exAbstractActionForm �Ȱۤʤ� GET �ꥯ�����Ȥκݤˤ� load ��Ԥ���
	\par mojaLE �ʤ� GET/POST ��Ȥ�ʬ���� Action ��ʻ�Ѥ������ exAbstractActionForm ����Ѥ���٤��Ǥ���
	@param $master fetch ����� load �ؤΰ���
	@return POST �ꥯ�����Ȼ���fetch �����꤬ȯ������� ACTIONFORM_INIT_FAIL ��
			���꤬ȯ�����ʤ���� ACTIONFORM_POST_SUCCESS ���֤��ޤ�
			\par GET �ꥯ�����Ȼ���load �����꤬ȯ������� ACTIONFORM_INIT_FAIL ��
			���꤬ȯ�����ʤ���� ACTIONFORM_INIT_FAIL ���֤��ޤ�
	*/
	function init(&$master) {
		if($_SERVER['REQUEST_METHOD']=='POST') {
			$this->fetch($master);
			return count($this->msg_) ? ACTIONFORM_INIT_FAIL : ACTIONFORM_POST_SUCCESS;
		}
		else {
			$this->load($master);
			return count($this->msg_) ? ACTIONFORM_INIT_FAIL : ACTIONFORM_INIT_SUCCESS;
		}
	}

	function fetch(&$master)
	{
		$this->load($master);
	}

}


/**
@brief AbstractForm �ϤΥ��󥹥��󥹤������ץ�ѥƥ� msg �����Ѥ�����å����������ɽ������
���ʤ��󶡤��� class
\par exAbstractFormObject �� getHtmlErrors �᥽�åɤ������뤵�줿�Ȥ������Υ��������饹��
render �᥽�åɤ��ƤӽФ���ޤ���
\par ���Υ��饹��ɸ��� exAbstractFormObject ��������Ȥ����Ѥ������顼�����ɤ� <li> ������
�֤�ʸ����Ȥäƥ�����󥰤��ޤ���
\par ���顼��å�������ɽ����ư�����Ѥ������Ȥ����������ư����ѹ����뤳�Ȥ��ҤȤĤ���ˡ��
�ʤ�ޤ����ܤ����� exAbstractFormObject �� $err_render_ ���Ф򸫤Ƥ���������
*/
class exFormErrorRender {
	var $form_=null;

	function init($form) {
		$this->form_=$form;
	}

	function render() {
		$ret ="<ul>";
		foreach($this->form_->msg_ as $m) {
			$ret.=@sprintf("<li><font color='red'>%s</font></li>\n",$m);
		}
		$ret .="</ul>";
		return $ret;
	}
}

?>