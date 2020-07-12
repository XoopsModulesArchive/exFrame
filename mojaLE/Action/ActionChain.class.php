<?php
/**
@file
@brief ���������������󥯥饹��������Ƥ���ե�����
@version $Id$
*/

/**
@brief ����������������Υ��饹
@remark ��Τ��������󤬤���Τǥ���ȥ��饯�饹���ľ�����˻��Ѥ��ʤ��Ǥ�������
*/
class mojaLE_ActionChain {
	var $actions_;
	var $result_;
	
	function mojaLE_ActionChain()
	{
		$this->actions_ = array();
		$this->result_="";
	}
	
	function execute ( &$controller, &$request, &$user )
	{
		$keys = array_keys($this->actions_);	// ���٤Ƥ� reg name ��ȴ��
		$action_max = count($keys);		// �롼�פΤ���˿��������

		$orig_render_mode = $controller->getRenderMode();

		for($i=0;$i<$action_max;$i++) {
			$local_controller = new SimpleFrontController(
									$this->actions_[$keys[$i]]['module_name'],
									$this->actions_[$keys[$i]]['action_name'],
									$controller->getBaseDir());

			$local_controller->setRenderMode(RENDER_VAR);
            $local_controller->dispatch();

			$this->result_ .= $local_controller->render_->fetchResult();
			unset($local_controller);
		}
	}

	function register ( $name, $module_name, $action_name )
	{
		$this->actions_[$name]['module_name'] = $module_name;
		$this->actions_[$name]['action_name'] = $action_name;
	}
	
	function fetchResult()
	{
		return $this->result_;
	}
}


?>