<?php
/**
@file
@brief mojaLE �δ���Ū����������饹�η�����ʤɤ�ԤäƤ���ե�����
@author minahito
@version $Id$
@todo �ӥ塼���饹����ݲ����饹�����ΤȤ����������Ƥ��ʤ�
@todo ��������󥯥饹�� return �ͤ� mojavi �Υ�������󥯥饹�Ȥ��碌�����饹���ߤ����Ȥ���

\section �Ȥ���
\par ����Ū�ʻȤ���
��mojaLE �� mojavi �Ȱۤʤ�ڡ�������ȥ���˽�ʬ���ʥ����ǥ��󥰥�������ˤΤ����
�ե��ȥ���ȥ���ιͤ�������������ΤǤ��Τǡ�����ȥ���ϼ����ǽ񤫤ʤ��ƤϤ���
�ޤ���
������ե�����⤢��ޤ��󤷡� Action ������ fetch ���륳���ɤ⺣�ΤȤ�����ޤ���
����ʬ�Τ���������Υ���ȥ�����뤫�� SimpleFrontController ���ͤ�Ϳ����ư�����Ƥ�
��������

\par SimpleFrontController ��Ȥ�
$controller = new SimpleFrontController(�⥸�塼��̾,���������̾,�١����ǥ��쥯�ȥ�ѥ�);
$controller->dispatch();
����ư��ޤ���
��mojavi �ʤ�⥸�塼��̾�ȥ��������̾�ϼ�ưŪ�˲��Ϥ��Ƥ����Ǥ��礦��
��SimpleFrontController �Ϥ����Ԥ�ʤ��Τǡ������ǲ��Ϥ����ͤ��Ϥ�ɬ�פ�����ޤ���

*/

define ( "MOJALE_DEFAULT_ACTION_NAME", "DefaultIndex" );

/// VIEW �� enum
define ( "VIEW_SUCCESS", "success" );
define ( "VIEW_INPUT", "input" );
define ( "VIEW_ERROR", "error" );
define ( "VIEW_NONE", "" );
define ( "VIEW_REDIRECT", "redirect" );	// original

define ("REQ_NONE",0);
define ("REQ_GET",1);
define ("REQ_POST",2);

define ("RENDER_TYPE_NONE",1);
define ("RENDER_TYPE_DIRECT_WITH_HEADER",1);
define ("RENDER_TYPE_DIRECT_WITH_CPHEADER",2);

define ( "RENDER_CLIENT", 0 );	///< ľ�����褹��
define ( "RENDER_VAR", 1 );		///< �ѿ������褹��


// ��ݲ����饹�ȥ���������Ϥ��٤��ɤ�
define ( "MOJALE_BASE", XOOPS_ROOT_PATH."/modules/exFrame/mojaLE" );
require_once MOJALE_BASE."/Action/AbstractAction.class.php";
require_once MOJALE_BASE."/Action/ActionChain.class.php";
require_once MOJALE_BASE."/View/AbstractView.class.php";
require_once MOJALE_BASE."/Render/AbstractRenderer.class.php";
require_once MOJALE_BASE."/Render/NoneRenderer.class.php";
require_once MOJALE_BASE."/Render/Renderer.class.php";
require_once MOJALE_BASE."/Render/SmartyRenderer.class.php";
require_once MOJALE_BASE."/Render/XoopsTplRenderer.class.php";

function mojaTesting_warn($message)
{
	nl2br($message."\n");
}


/**
@brief �⥸�塼��̾�� 'mojaLE' �˷���Ǥ��ˤ���Ƥ���ե��ȥ���ȥ��饯�饹
�⥸�塼����Υڡ�������ȥ���Ǥϥ⥸�塼�����Ĥ��ȤϤۤȤ�ɤ���ޤ���Τǡ������Ƥ���
���Υ��饹��Ȥä��������᤯�����Ǥ���Ǥ��礦��
*/
class mojaLE_FrontController extends SimpleFrontController
{
	function mojaLE_FrontController($action_name,$base_dir) {
		parent::SimpleFrontController("mojaLE",$action_name,$base_dir);
	}
}

/**
@brief �ե��ȥ���ȥ���Ū�ʼ���������륷��ץ�ʥ���ȥ��饯�饹
*/
class SimpleFrontController
{
	var $module_name_;
	var $action_name_;
	var $base_dir_;
	var $request_;
	var $user_;
	var $aid_;			///< �¹���� Action �Υ���ȥ�����ˤ����� ID�ʤĤޤ� forward ��� processView ���ʤ�������)

	var $render_;
	
	var $render_mode_;	///< �������⡼��

	var $varbose_;		///< �С��ܡ����⡼�ɤΥե饰

	function SimpleFrontController($module_name,$action_name,$base_dir)
	{
		global $xoopsUser;
		$this->module_name_=$module_name;
		if($action_name && $action_name!=MOJALE_DEFAULT_ACTION_NAME)
			$this->action_name_=strtolower($action_name);
		else
			$this->action_name_=MOJALE_DEFAULT_ACTION_NAME;

		$this->base_dir_ = $base_dir;
		
		$this->request_=new mojaLE_VR_Request();
		$this->user_=$xoopsUser;
		
		$this->render_=null;
		$this->render_mode_ = RENDER_CLIENT;
		
		$this->aid_=1;

		$this->varbose_ = false;
	}
	
	/**
	@brief �⥸�塼��̾���֤��ޤ�
	@return string
	*/
	function getModuleName()
	{
		return $this->module_name_;
	}
	
	function getBaseDir()
	{
		return $this->base_dir_;
	}
	
	function getActionName()
	{
		return $this->action_name_;
	}

	function getModuleDir()
	{
		$dir = $this->base_dir_;
		if($this->module_name_)
			$dir .= "/" . $this->module_name_;
		return $dir;
	}

	function setVarbose($flag=true)
	{
		$this->varbose_ = $flag;
	}

	function setRenderMode($mode)
	{
		$this->render_mode_ = $mode;
	}
	
	function getRenderMode()
	{
		return $this->render_mode_;
	}
	
	function _getViewNamingMap()
	{
		// ���󥸥���������к������ݤʤΤǤ�����
        $_VIEW_NAMING_MAP_ = array(
        	1=>"success",
        	2=>"input",
        	3=>"error"
        );

        return $_VIEW_NAMING_MAP_;
	}
	
	function _getViewName($action_return_value)
	{
		$map = $this->_getViewNamingMap();
		return isset($map[$action_return_value]) ? $map[$action_return_value] : "";
	}

	function validate()
	{
		return (strstr($this->action_name_,"..")===false);
	}

	function dispatch()
	{
		if(!$this->validate()) {
			print "�Х�ǡ�����󥨥顼";
			die();
		}
			
		// FIXME::ActionName ����������Ĵ�٤�٤�

		$now_aid = $this->aid_;
		$action_result = $this->_processAction($this->module_name_,$this->action_name_);
		
		if($action_result!=VIEW_NONE && $now_aid==$this->aid_) {
			$this->render_ = $this->_processView($this->module_name_,$this->action_name_,$action_result);
			if(is_object($this->render_))
				$this->render_->execute($this,$this->request_,$this->user_);
		}
	}

	/// dispatch �μ¹Ԥ򥹥ȥåפ��륬���ɤ�ɬ��?
	function forward($module_name,$action_name)
	{
		$this->aid_++;
		$now_aid = $this->aid_;
		$action_result = $this->_processAction($module_name,$action_name);
		
		if($action_result!=VIEW_NONE) {
			$this->render_ = $this->_processView($module_name,$action_name,$action_result);
			if(is_object($this->render_))
				$this->render_->execute($this,$this->request_,$this->user_);
		}
	}
	
	function &_processView($module_name,$action_name,$action_result)
	{
		$action_name=strtolower($action_name);
//		if(!($view_name=$this->_getViewName($action_result)))
//			die();

		$view_name = $action_result;
		
		// ��������å�
		if(preg_match("#(\.\.|\\|/)#",$view_name))
			die();

		// �ӥ塼�����֥��饹����ե�����μ�����
		$view_file = $this->base_dir_ ."/". $module_name ."/views/". $action_name . "_" . $view_name . ".class.php";

		if($this->varbose_)
			mojaTesting_warn ( "Read View File ... ".$view_file."<br/>" );

		if(file_exists($view_file)) {
			require_once ($view_file);
		}
		else	// view �ξ��ϰ�������ǻ�
			die();

		// ���饹�����å�
		$class_name = $module_name . "_" . ucfirst($action_name) . "View_" . $view_name;

		if($this->varbose_)
			mojaTesting_warn ( "check exsist class ... ".$class_name."<br/>" );

		if(!class_exists($class_name))
			die();

		// �¹Բ�ǽ
		$view_instance = new $class_name;
		$render = $view_instance->execute($this,$this->request_,$this->user_);

		if(!is_object($render))
			die();	// FIXME::do ���顼�ϥ�ɥ��

		return $render;
	}

	/// $action_class_name ����ե�������ɤ߹��ߡ����饹�γ��ݤ�Ԥ��¹ԡ�
	function _processAction($module_name,$action_name)
	{
		$action_class_name = strtolower($action_name);

		//FIXME:: .. �����Ǥ����褦��...���Ȥǳ�ǧ
		if(preg_match("#(\.\.|\\|/)#",$action_class_name))
			die();

		// �ե�������ɤ�
		$action_file = $this->base_dir_ ."/". $module_name ."/actions/". $action_name . ".class.php";

		if($this->varbose_)
			mojaTesting_warn ( "Read Action file ".$action_file."<br/>" );

		if(file_exists($action_file)) {
			require_once ($action_file);
		}
		
		$class_name = $module_name . "_" . ucfirst($action_name) . "Action";
		if(!class_exists($class_name)) {
			trigger_error ( $class_name." is not defined." );
			die();
		}
			
		$action_instance = new $class_name;
		if($this->varbose_)
			mojaTesting_warn ( "Generate action instance<br/>" );
		
		// �桼���������å�
		if($action_instance->isSecure() && !is_object($this->user_)) {
			header ( "location: ".XOOPS_URL."/user.php" );
			die();
		}

		// �����ԥ����å�
		if($action_instance->isAdmin()) {
			$flag = false;
			if(is_object($this->user_)) {
				// https �б�
				if(preg_match("/(http|https)::(.+)/",XOOPS_URL,$match)) {
					$xoops_naked_url = $match[2];
					$pattern = "#^(http|https)::".$xoops_naked_url."/modules/#";
					// FIXME:: ̤���ڤǤ�
					if(preg_match($pattern,$_SERVER['REQUEST_URI'])) {
						global $xoopsModule;
						$flag = $this->user_->isAdmin($xoopsModule->mid());
					}
					else
						$flag = $this->user_->isAdmin();
				}
			}
			if(!$flag) {
				$this->_errorIsAdmin();
			}
		}

		// �ƤӽФ��᥽�å�̾�θ���
		$req_flag = $action_instance->getRequestMethods();

		if($req_flag==REQ_NONE) {
			return $action_instance->getDefaultView($this,$this->request_,$this->user_);
		}
		elseif($_SERVER['REQUEST_METHOD']=='POST') {
			return ($req_flag & REQ_POST ) ? 
				$action_instance->execute($this,$this->request_,$this->user_) :
				$action_instance->getDefaultView($this,$this->request_,$this->user_);
		}
		else {
			return ($req_flag & REQ_GET ) ? 
				$action_instance->execute($this,$this->request_,$this->user_) :
				$action_instance->getDefaultView($this,$this->request_,$this->user_);
		}

		return $action_instance->execute($this,$this->request_,$this->user_);
	}

	function _errorIsAdmin()
	{
		header ( "location: ".XOOPS_URL."/user.php" );
		die();
	}
}

/**
@biref HashMap ��å�
@note Java ��碌�Τ���ˡ��ޤ������...
*/
class mojaLE_VR_Request
{
	var $attribute_;
	
	function mojaLE_VR_Request()
	{
		$this->attribute_ = array();
	}

	function setAttribute($key,$value)
	{
		$this->attribute_[$key] = $value;
	}
	
	function setAttributeByRef($key, &$value)
	{
		$this->attribute_[$key] =& $value;
	}
	
	function &getAttribute($key)
	{
		return isset($this->attribute_[$key]) ? $this->attribute_[$key] : null;
	}
}

?>