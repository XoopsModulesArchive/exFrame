<?php
/**
@brief PermForm 用のレンダー
@version $Id: PermConfirmModelRender.php,v 1.3 2005/03/31 10:25:22 minahito Exp $
*/

require_once "exComponent/confirm/render/ConfirmModelRender.php";

class exPermConfirmComponentModelRender extends exConfirmComponentModelRender {
	function render()
	{
		$ret="";

		$permform =& $this->component_->form_->data_;

		$ret .="<form method='post'><table class='outer'>";

		if(defined("_MD_EXFRAME_LANGUAGE_TERM_LOADED"))
			$ret .="<th>"._MD_EXFRAME_LANG_NAME."</th><th>"._MD_EXFRAME_LANG_DESCRIPTION."</th>";
		else
			$ret .="<th>NAME</th><th>DESC</th>";

		// チケットの埋め込み（あれば）
		if(isset($this->component_->form_->ticket_)) {
			$ret.="<input type='hidden' name='".$this->component_->form_->ticket_->name_."' ".
				"value='".$this->component_->form_->ticket_->value_."' />";
		}

		$groups=&$permform->groups_;
		foreach($groups as $g) {
			$ret .= "<th>".$g->getVar('name')."</th>";
		}

		$perms=&$permform->perms_;
		foreach($perms as $perm) {
			$ret .="<tr class='odd'><td>".$perm['name']."</td><td>".$perm['desc']."</td>";
			foreach($groups as $g) {
				$ret.="<td>";

				if(defined("_MD_EXFRAME_LANGUAGE_TERM_LOADED"))
					$ret .= ($permform->group_perms_[$perm['name']][$g->getVar('groupid')]) ?
						_MD_EXFRAME_LANG_PERM_AFFIRMATION : _MD_EXFRAME_LANG_PERM_DENY;
				else
					$ret .= ($permform->group_perms_[$perm['name']][$g->getVar('groupid')]) ?
						"Affirmation" : "Deny";

				$ret.="</td>";
			}
			$ret .="</tr>";
		}
		
		$ret.="<tr class='odd'><td>ACTION</td><td colspan='".(count($groups)+1)."'>".
			"<input type='submit' value='Submit' />".
			"<input type='button' value='Back' onclick=\"javascript:history.go(-1);\" />";

		$ret .= "</table></form>";

		print $ret;
	}
}
?>
