<?php

require_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

/**
@brief exObject の配列を渡すことで、プルダウンメニューを作るニッチ用途なクラスです。
@note コンストラクタにオブジェクトの配列、キー側を抽出するための添え字、value 側を抽出するための添え字
\par をそれぞれ渡して下さい。
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