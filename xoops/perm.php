<?php
/**
@brief �ѡ��ߥå���󥬡���
*/

require_once "xoops/object.php";

class exPerm
{
	/**
	@brief ������ : $xoopsUser �� $name �Υѡ��ߥå�������äƤ��뤫�ɤ�����Ĵ�٤ޤ�
	*/
	function Guard($name,$item_id=0)
	{
		global $xoopsUser,$xoopsModule;
		static $__experm_currentUser_cache__;
		
		if (isset($__experm_currentUser_cache__[$item_id][$name]))
			return $__experm_currentUser_cache__[$item_id][$name];

		$handler=&exXoopsGroupPermHandler::getInstance();

		if(is_object($xoopsUser))
			$groups=$xoopsUser->getGroups();
		else {
			$groups=array();
			$groups[]=XOOPS_GROUP_ANONYMOUS;
		}

		$criteria=new CriteriaCompo();
		$gc=new CriteriaCompo();
		foreach($groups as $gid) {
			$gc->add(new Criteria('gperm_groupid',$gid),"OR");
		}
		$criteria->add($gc);
		$criteria->add(new Criteria('gperm_modid',$xoopsModule->mid()));
		if($item_id!==null)
			$criteria->add(new Criteria('gperm_itemid',$item_id));
		$criteria->add(new Criteria('gperm_name',$name));
		
		return $__experm_currentUser_cache__[$item_id][$name]=$handler->getCount($criteria);
	}
	
	/**
	@brief ���¤�����Ȥ� true ���ʤ��Ȥ� false ���ᤷ�ޤ�
	*/
	function isPerm($name,$item_id=0)
	{
		return exPerm::Guard($name,$item_id);
	}

	function getPermNames($item_id=0,$mid=null)
	{
		global $xoopsUser,$xoopsModule;

		$mid = ($mid==null) ? $xoopsModule->mid() : $mid;

		$handler=&exXoopsGroupPermHandler::getInstance();

		if(is_object($xoopsUser))
			$groups=$xoopsUser->getGroups();
		else {
			$groups=array();
			$groups[]=XOOPS_GROUP_ANONYMOUS;
		}

		$criteria=new CriteriaCompo();
		$gc=new CriteriaCompo();
		foreach($groups as $gid) {
			$gc->add(new Criteria('gperm_groupid',$gid),"OR");
		}
		$criteria->add($gc);
		$criteria->add(new Criteria('gperm_modid',$mid));
		if($item_id!==null)
			$criteria->add(new Criteria('gperm_itemid',$item_id));
		
		$objs=$handler->getObjects($criteria);
		
		$ret=array();
		foreach($objs as $obj) {
			$ret[$obj->getVar('gperm_name')]=1;
		}
		return $ret;
	}

	function getPermNames_global($item_id)
	{
		global $xoopsUser,$xoopsModule;

		$handler=&exXoopsGroupPermHandler::getInstance();

		if(is_object($xoopsUser))
			$groups=$xoopsUser->getGroups();
		else {
			$groups=array();
			$groups[]=XOOPS_GROUP_ANONYMOUS;
		}

		$criteria=new CriteriaCompo();
		$gc=new CriteriaCompo();
		foreach($groups as $gid) {
			$gc->add(new Criteria('gperm_groupid',$gid),"OR");
		}
		$criteria->add($gc);
		$criteria->add(new Criteria('gperm_modid',$xoopsModule->mid()));

		$ic=new CriteriaCompo();
		$ic->add(new Criteria('gperm_itemid',0),"OR");
		$ic->add(new Criteria('gperm_itemid',$item_id),"OR");
		$criteria->add($ic);

		$objs=$handler->getObjects($criteria);
		
		$ret=array();
		foreach($objs as $obj) {
			$ret[$obj->getVar('gperm_name')]=1;
		}
		return $ret;
	}

	/**
	@brief �����å������ɤη�̤����ʤ�����å�������ɽ�����ƥ�����쥯�Ȥ��ޤ���
	*/
	function GuardRedirect($name,$url,$message=null,$time=1)
	{
		if(!exPerm::Guard($name)) {
			redirect_header($url,$time,$message);
			exit;
		}
	}
	
	/**
	@brief ���ꤵ�줿 $item_id �� 0 �� OR �ͤ��֤��ޤ��������Х����꤬����ݤ�Ʊ���˥����å��Ǥ��ޤ�
	*/
	function Guard_global($name,$item_id)
	{
		return (exPerm::Guard($name,0) or exPermission::Guard($name,$item_id));
	}

	/**
	@brief exXoopsGroupPermHandler::getInstance() ����Ѥ��Ƥ����������ѻߤ���ޤ���
	*/
	function &getGroupPermHandler()
	{
		global $xoopsDB;
		static $exXGPermHandler;
		if(!$exXGPermHandler)
			$exXGPermHandler=new exXoopsGroupPermHandler($xoopsDB);

		return $exXGPermHandler;
	}
}

// ��碌
class exPermission extends exPerm {
}

class exXoopsGroupPermObject extends exXoopsObject
{
	function exXoopsGroupPermObject($id=null)
	{
		parent::exXoopsObject($id);
		$this->initVar('gperm_id',XOBJ_DTYPE_INT,0,false);
		$this->initVar('gperm_groupid',XOBJ_DTYPE_INT,0,false);
		$this->initVar('gperm_itemid',XOBJ_DTYPE_INT,0,false);
		$this->initVar('gperm_modid',XOBJ_DTYPE_INT,0,false);
		$this->initVar('gperm_name',XOBJ_DTYPE_TXTBOX,'',false,32);
	}

}

class exXoopsGroupPermHandler extends exXoopsObjectHandler
{
	function &getInstance()
	{
		global $xoopsDB;
		static $exXGPermHandler;
		if(!$exXGPermHandler)
			$exXGPermHandler=new exXoopsGroupPermHandler($xoopsDB);

		return $exXGPermHandler;
	}

	function exXoopsGroupPermHandler($db)
	{
		$this->_classname_="exXoopsGroupPermObject";
		$this->_tableinfo_=new exTableInfomation('group_permission','gperm_id');
		parent::exXoopsObjectHandler($db);
	}
}

?>
