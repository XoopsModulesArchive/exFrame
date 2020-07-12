<?php
/**
@version $Id: PermForm.php,v 1.3 2004/08/04 16:36:18 minahito Exp $
*/

require_once "exForm/Form.php";
require_once "xoops/perm.php";

require_once "include/XMLLoader.php";

class exPermXMLLoader extends AbstractXMLLoader
{
	var $perms_=array();

	function permissionOepnTagHandler($arr)
	{
		$p = array ();
		$p['name']=$arr['name'];
		if(defined($arr['desc']))
			$p['desc']=constant($arr['desc']);
		else
			$p['desc']=$arr['desc'];

		$this->perms_[]=$p;
	}
	
	function &getPerms()
	{
		return $this->perms_;
	}
}

class exPermEditForm extends exAbstractActionForm
{
	var $item_id_=0;
	var $groups_=array();	/**< グループオブジェクト */
	var $perms_=array();	/**< 権限の名前と説明の連想配列 */ 
	var $group_perms_=array();	/**< [権限名][グループ] の添字を持つ bool */

	function exPermEditForm($item_id=0)
	{
		$this->item_id_=$item_id;
		
		// groups の初期化
		$gHandler=&xoops_gethandler('group');
		$this->groups_ =& $gHandler->getObjects();

		parent::exAbstractActionForm($item_id);
	}

	function doGet($data)
	{
		global $xoopsModule;

		// 権限の読み込み
		$handler=&exXoopsGroupPermHandler::getInstance();
		$criteria=new CriteriaCompo();
		$criteria->add(new Criteria('gperm_modid',$xoopsModule->mid()));
		$criteria->add(new Criteria('gperm_itemid',$this->item_id_));
		$objs=&$handler->getObjects($criteria);

		foreach($objs as $obj) {
			$this->group_perms_[$obj->getVar('gperm_name')][$obj->getVar('gperm_groupid')]=true;
		}
	}
	
	function doPost($data)
	{
		// 飛んできた権限のチェック
		foreach($this->perms_ as $perm) {
			$name=$perm['name'];
			if(isset($_POST[$name])) {
				foreach($this->groups_ as $g) {
    				if($_POST[$name][$g->getVar('groupid')]==='1') {
    					$this->group_perms_[$name][$g->getVar('groupid')]=true;
    				}
				}
			}
		}
	}
}

class exPermXMLEditForm extends exPermEditForm {
	function exPermXMLEditForm($file,$item_id=0)
	{
		$loader = new exPermXMLLoader();
		$loader->load($file);
		$this->perms_=&$loader->getPerms();

		parent::exPermEditForm($item_id);
	}
}

class exPermItemEditForm extends exPermEditForm
{
	function doGet($data)
	{
		$this->item_id_=$this->getPositive($_GET['item_id']);
		parent::doGet($data);
	}
	
	function doPost($data)
	{
		$this->item_id_=$this->getPositive($_POST['item_id']);
		parent::doPost($data);
	}
}

class exPermItemXMLEditForm extends exPermItemEditForm
{
	function exPermItemXMLEditForm($file,$item_id=0)
	{
		$loader = new exPermXMLLoader();
		$loader->load($file);
		$this->perms_=&$loader->getPerms();

		parent::exPermItemEditForm($item_id);
	}
}

?>