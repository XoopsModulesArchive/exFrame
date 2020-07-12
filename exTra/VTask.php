<?php
/**
@brief ���������ȥꥬ�������Ѥ�������Ū�ʥ����������Τ�������
@version $Id: VTask.php,v 1.1 2004/07/21 11:20:32 minahito Exp $
*/

require_once "xoops/class.object.php";

class exFrameTaskObject extends exXoopsObject {
	function exFrameTaskObject($id=null)
	{
		$this->initVar('tid', XOBJ_DTYPE_INT, 0, true);
		$this->initVar('time', XOBJ_DTYPE_INT, 0, true);
		$this->initVar('serial', XOBJ_DTYPE_OTHER );

		if ( is_array ( $id ) )
			$this->assignVars ( $id );
	}

	function &getTableInfo()
	{
		$tinfo = new exTableInfomation('exframe_task','tid');
		return ($tinfo);
	}
}

class exVirtualTask {
	var $classname_="exVirtualTask";
	var $include_;
	var $map_;
	
	/**
	@brief ���󥯥롼�ɥե�������ɲä���
	*/
	function addInclude($file) {
		if(is_array($file)) {
			foreach($file as $f) {
				$this->addInclude($f);
			}
		}
		else {
			if(strpos($f,XOOPS_ROOT_PATH)==0) {
				$this->include_[] = $f;
				return true;
			}
			else
				return false;
		}
	}

	/**
	@brief require_once �μ¹�
	*/
	function processInclude() {
		foreach($this->include_ as $f) {
			if(strpos($f,XOOPS_ROOT_PATH)==0)
				require_once($f);
		}
	}

	/**
	@brief ���󥹥��󥹤�����
	*/
	function create() {
		$ret = unserialize($this->getVar('serial','e'));
		return $ret;
	}

	/**
	@brief Ϣ������ץ�ѥƥ����ͤ���Ͽ 
	*/
	function setAttribute($key,$value) {
		$this->map_[$key]=$value;
	}

	/**
	@brief Ϣ������ץ�ѥƥ������ͤ���Ф� 
	*/
	function getAttribute($key) {
		return ($this->map_[$key]);
	}
}

?>