<?php
/**
@note Ʊ���ѥ��Ǥ�����ˤ�äư㤦��������ǽ�ʤ褦�ˡ��ʴ����Ԥξ����̤ν�����Ԥ��ʤɡ�
\par �ѥ��������ФǤϤʤ���Chain of Responsibility ����Ѥ���
@version $Id$
*/

require_once "include/common.php";

class ChainActionHandler
{
	var $path_="";
	var $next_=null;

	function ChainActionHandler($path)
	{
		$this->path_=$path;
	}

	function doService()
	{
		if($this->path_!=exFacadeCommon::getPath()) {
			if($next_==null)
				return false;
			else
				return $next_->doService();
		}
		else {
			$this->_execute();
		}
	}

	function _execute()
	{
	}
}

class ChainActionChainBuilder
{
	var $instances_=array();
	
	function ChainActionBuilder()
	{
	}

	function add(&$instance)
	{
		$count=count($this->instances_);
		if($count) {
		}
		else{
			$this->instances_[$count-1]->next_=&$instance;
			$this->instances_[]=&$instance;
		}
	}

	function &getFirst()
	{
		return $this->instances_[0];
	}
}

?>