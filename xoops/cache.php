<?php
/**
@brief smarty キャッシュコントロールのためのクラスメソッドなどを提供するファイル
@version $Id: cache.php,v 1.2 2004/08/03 13:09:32 minahito Exp $
*/

class exXoopsCache {
	function moduleCacheClear($dirname="") {
		global $xoopsModule;
		global $xoopsUser;
		if($dirname=="" && is_object($xoopsModule)) {
			exXoopsCache::moduleCacheClear($xoopsModule->dirname());
		}
		else {
			// キャッシュファイルを削除する
			if($handler=opendir(XOOPS_CACHE_PATH."/")) {
				while(($file=readdir($handler)) !== false) {
					if(strpos($file,"mod_".$dirname."^")!==false) {
						@unlink (XOOPS_CACHE_PATH."/".$file);
					}
				}
			}
		}
	}

}


?>