<?php
/**
@file
@author
@version $Id$
*/

/**
@brief mojavi の Action class とインターフェイスをあわせる抽象化クラス
*/
class mojaLE_AbstractAction
{
	/**
	@brief このアクションのビジネスロジックを実行する際にコールされるメソッドです
		このメソッドは主に GET/POST リクエストを切り分ける際に使用します。
	@remark このメソッドを Controller がコールするようにするためには getRequestMethods メソッドを
		調整しなくてはいけません。
	@note mojavi と異なり、デフォルトでは VIEW_NONE を返します
	*/
	function getDefaultView(&$controller,&$request,&$user)
	{
		return VIEW_NONE;
	}

	/**
	@brief このアクションのビジネスロジックを実行する際にコールされるメソッドです
	@note mojavi と異なり、デフォルトでは VIEW_NONE を返します
	*/
	function execute(&$controller,&$request,&$user)
	{
		return VIEW_NONE;
	}
	
	/**
	@brief GET/POST どちらのリクエストメソッドで execute メソッドをコールするか、その情報を返します
	return REQ_GET で  GET時 execute POST 時 getDefaultView
	return REQ_POST で POST時 execute GET 時 getDefaultView がコールされるようになります。
	デフォルトでは return ( REQ_GET | REQ_POST ) になっていますので、
	リクエストメソッドにかかわらず execute メソッドがコールされます
	@return int 定数 REQ_GET,REQ_POST を組み合わせて実装を
	@note この動作の実装はコントローラクラスで行われている
	*/
	function getRequestMethods()
	{
		return (REQ_GET | REQ_POST);
	}
	
	/**
	@brief このアクションを実行できるユーザーはログイン済みである必要があるかどうかの情報を返します
	@return bool true 時、ログイン済みのユーザーでなければこのアクションを実行できなくなります
	@note この動作の実装はコントローラクラスで行われている
	*/
	function isSecure()
	{
		return false;
	}
	
	/// original
	function isAdmin()
	{
		return false;
	}
}

?>