<?php
/**
@brief header.php を後読みした際に使用するローダー
@version $Id$
*/

//define("__EXFRAME__DEBUG__",1);

if(!defined("__EXFRAME_PATH__")) {
	define ("__EXFRAME__PATH__", dirname(__FILE__));

    define('INCLUDE_PRIORITY','');
    
    if(!defined('PATH_SEPARATOR')) {
		if ( "/" == DIRECTORY_SEPARATOR )
    		define('PATH_SEPARATOR',':');
    	else // Windows
    		define('PATH_SEPARATOR',';');
    }
    
    if (defined('INCLUDE_PRIORITY')) {
        ini_set('include_path', 
        		__EXFRAME__PATH__ . PATH_SEPARATOR .
        		ini_get('include_path'));
    }
    else {
        ini_set('include_path',ini_get('include_path') . PATH_SEPARATOR . 
        		__EXFRAME__PATH__ );
    }

	// xoops salt
	if(defined('XOOPS_SALT'))
		define('TICKET_PREFIX',XOOPS_SALT);
	else
		define('TICKET_PREFIX',md5(XOOPS_ROOT_PATH.XOOPS_DB_NAME.XOOPS_DB_USER.XOOPS_DB_PASS));
	define('EXFRAME_SALT',TICKET_PREFIX);

	// xoops common include files
	include_once XOOPS_ROOT_PATH.'/class/errorhandler.php';
	include_once XOOPS_ROOT_PATH.'/class/logger.php';
	include_once XOOPS_ROOT_PATH.'/include/functions.php';
	require_once XOOPS_ROOT_PATH.'/class/database/databasefactory.php';
	require_once XOOPS_ROOT_PATH.'/kernel/object.php';
	require_once XOOPS_ROOT_PATH.'/class/criteria.php';
	include_once XOOPS_ROOT_PATH."/class/module.textsanitizer.php";

	require_once __EXFRAME__PATH__."/include/class.exFrame.php";
	require_once __EXFRAME__PATH__."/include/Session.php";
	require_once __EXFRAME__PATH__."/xoops/cache.php";
}

// 最低限これだけは調べる
if(exFrame::InjectionCheck('xoopsConfig')) die;

?>
