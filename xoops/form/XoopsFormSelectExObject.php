<?php

require_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

/**
@brief exObject ��������Ϥ����Ȥǡ��ץ�������˥塼����˥å����Ӥʥ��饹�Ǥ���
@note ���󥹥ȥ饯���˥��֥������Ȥ����󡢥���¦����Ф��뤿���ź������value ¦����Ф��뤿���ź����
\par �򤽤줾���Ϥ��Ʋ�������
\par ex) new exXoopsFormSelectExObject("TEAM","team,"$tid,1,false, $obj_array, "t_id", "name");
*/
class exXoopsFormSelectExObject extends XoopsFormSelect
{
	function exXoopsFormSelectExObject($caption, $name, $value=null, $size=1, $multiple=false,$objs,$id_key,$value_key){
		parent::XoopsFormSelect($caption,$name,$value,$size,$multiple);
		foreach($objs as $obj) {
			$this->addOption($obj->getVar($id_key),$obj->getVar($value_key));
		}
	}
}


?>