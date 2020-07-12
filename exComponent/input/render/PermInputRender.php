<?php
/**
@version $Id: PermInputRender.php,v 1.4 2005/03/31 10:25:22 minahito Exp $
*/

require_once "exComponent/Input.php";

class exPermInputComponentRender extends exInputComponentRender {
	function render()
	{
		$ret="";

		$ret .="<form method='post'>";
		$ret .="<input type='hidden' name='item_id' value='".$this->component_->form_->item_id_."' />";
		$ret .="<table class='outer'>";

		if(defined("_MD_EXFRAME_LANGUAGE_TERM_LOADED"))
			$ret .="<th>"._MD_EXFRAME_LANG_NAME."</th><th>"._MD_EXFRAME_LANG_DESCRIPTION."</th>";
		else
			$ret .="<th>NAME</th><th>DESC</th>";

		$groups=&$this->component_->form_->groups_;

		foreach($groups as $g) {
			$ret .= "<th>".$g->getVar('name')."</th>";
		}
		$ret .="</tr>";

		$ret .="<tr><td class='head'>ITEM_ID</td><td class='head' colspan='".(count($groups)+1)."'>".$this->component_->form_->item_id_."</td></tr>";

		$perms=&$this->component_->form_->perms_;
		foreach($perms as $perm) {
			$ret .="<tr class='odd'><td>".$perm['name']."</td><td>".$perm['desc']."</td>";
			foreach($groups as $g) {
				$ret.="<td>";
				$ret.="<select name='".$perm['name']."[".$g->getVar('groupid')."]'>";

				if(isset($this->component_->form_->group_perms_[$perm['name']][$g->getVar('groupid')])) {
					$ret.="<option value='1' selected>";
						$ret.=defined("_MD_EXFRAME_LANGUAGE_TERM_LOADED") ? _MD_EXFRAME_LANG_PERM_AFFIRMATION : "Affirmation";
					$ret.="</option>";

					$ret.="<option value='0'>";
						$ret.=defined("_MD_EXFRAME_LANGUAGE_TERM_LOADED") ? _MD_EXFRAME_LANG_PERM_DENY : "Deny";
					$ret.="</option>";
				}
				else {
					$ret.="<option value='1'>";
						$ret.=defined("_MD_EXFRAME_LANGUAGE_TERM_LOADED") ? _MD_EXFRAME_LANG_PERM_AFFIRMATION : "Affirmation";
					$ret.="</option>";

					$ret.="<option value='0' selected>";
						$ret.=defined("_MD_EXFRAME_LANGUAGE_TERM_LOADED") ? _MD_EXFRAME_LANG_PERM_DENY : "Deny";
					$ret.="</option>";
				}

				$ret.="</select>";
				$ret.="</td>";
			}
			$ret .="</tr>";
		}

		$ret.="<tr class='odd'><td>ACTION</td><td colspan='".(count($groups)+1)."'>".
			"<input type='submit' value='Confirm' /><input type='reset' value='Reset' />";

		$ret .= "</table></form>";

		print $ret;
	}
}

?>