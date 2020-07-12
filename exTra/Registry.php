<?php
/**
@version $Id: Registry.php,v 1.1 2004/07/21 11:20:32 minahito Exp $
*/

require_once "xoops/class.object.php";

class exRegistryObject extends exXoopsObject {
	function bxPartyPartyObject($id=null)
	{
		$this->initVar('major', XOBJ_DTYPE_TXTBOX, null, true, 255);
		$this->initVar('minor', XOBJ_DTYPE_TXTBOX, null, true, 255);
		$this->initVar('value', XOBJ_DTYPE_OTHER );

		if ( is_array ( $id ) )
			$this->assignVars ( $id );
	}

	function &getTableInfo()
	{
		$tinfo = new exTableInfomation('bxparty',array('major','minor'));
		return ($tinfo);
	}
}

class exRegistryObjectHandler extends exXoopsObjectHandler {
}

?>