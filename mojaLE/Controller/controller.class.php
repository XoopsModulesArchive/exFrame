<?php
/**
@file
@brief mojaLE の基本的な定義、クラスの型宣言などを行っているファイル
@author minahito
@version $Id$
@todo ビュークラスの抽象化クラスが今のところ定義されていない
@todo アクションクラスの return 値を mojavi のアクションクラスとあわせたクラスが欲しいところ

\section 使い方
\par 基本的な使い方
　mojaLE は mojavi と異なりページコントローラに書き分け（コーディングスタイル）のための
フロントコントローラの考え方を持ち込むものですので、コントローラは自前で書かなくてはいけ
ません。
　設定ファイルもありませんし、 Action を自前で fetch するコードも今のところありません。
　自分のお気に入りのコントローラを作るか、 SimpleFrontController に値を与えて動かしてく
ださい。

\par SimpleFrontController を使う
$controller = new SimpleFrontController(モジュール名,アクション名,ベースディレクトリパス);
$controller->dispatch();
　で動作します。
　mojavi ならモジュール名とアクション名は自動的に解析してくれるでしょう。
　SimpleFrontController はそれを行わないので、自前で解析し、値を渡す必要があります。

*/

define ( "MOJALE_DEFAULT_ACTION_NAME", "DefaultIndex" );

/// VIEW の enum
define ( "VIEW_SUCCESS", "success" );
define ( "VIEW_INPUT", "input" );
define ( "VIEW_ERROR", "error" );
define ( "VIEW_NONE", "" );
define ( "VIEW_REDIRECT", "redirect" );	// original

define ("REQ_NONE",0);
define ("REQ_GET",1);
define ("REQ_POST",2);

define ("RENDER_TYPE_NONE",1);
define ("RENDER_TYPE_DIRECT_WITH_HEADER",1);
define ("RENDER_TYPE_DIRECT_WITH_CPHEADER",2);

define ( "RENDER_CLIENT", 0 );	///< 直接描画する
define ( "RENDER_VAR", 1 );		///< 変数へ描画する


// 抽象化クラスとレンダラだけはすべて読む
define ( "MOJALE_BASE", XOOPS_ROOT_PATH."/modules/exFrame/mojaLE" );
require_once MOJALE_BASE."/Action/AbstractAction.class.php";
require_once MOJALE_BASE."/Action/ActionChain.class.php";
require_once MOJALE_BASE."/View/AbstractView.class.php";
require_once MOJALE_BASE."/Render/AbstractRenderer.class.php";
require_once MOJALE_BASE."/Render/NoneRenderer.class.php";
require_once MOJALE_BASE."/Render/Renderer.class.php";
require_once MOJALE_BASE."/Render/SmartyRenderer.class.php";
require_once MOJALE_BASE."/Render/XoopsTplRenderer.class.php";

function mojaTesting_warn($message)
{
	nl2br($message."\n");
}


/**
@brief モジュール名が 'mojaLE' に決め打ちにされているフロントコントローラクラス
モジュール内のページコントローラではモジュールを持つことはほとんどありませんので、たいていは
このクラスを使った方が手早く実装できるでしょう。
*/
class mojaLE_FrontController extends SimpleFrontController
{
	function mojaLE_FrontController($action_name,$base_dir) {
		parent::SimpleFrontController("mojaLE",$action_name,$base_dir);
	}
}

/**
@brief フロントコントローラ的な実装を助けるシンプルなコントローラクラス
*/
class SimpleFrontController
{
	var $module_name_;
	var $action_name_;
	var $base_dir_;
	var $request_;
	var $user_;
	var $aid_;			///< 実行中の Action のコントローラ内における ID（つまり forward 後に processView しないガード)

	var $render_;
	
	var $render_mode_;	///< レンダーモード

	var $varbose_;		///< バーボーズモードのフラグ

	function SimpleFrontController($module_name,$action_name,$base_dir)
	{
		global $xoopsUser;
		$this->module_name_=$module_name;
		if($action_name && $action_name!=MOJALE_DEFAULT_ACTION_NAME)
			$this->action_name_=strtolower($action_name);
		else
			$this->action_name_=MOJALE_DEFAULT_ACTION_NAME;

		$this->base_dir_ = $base_dir;
		
		$this->request_=new mojaLE_VR_Request();
		$this->user_=$xoopsUser;
		
		$this->render_=null;
		$this->render_mode_ = RENDER_CLIENT;
		
		$this->aid_=1;

		$this->varbose_ = false;
	}
	
	/**
	@brief モジュール名を返します
	@return string
	*/
	function getModuleName()
	{
		return $this->module_name_;
	}
	
	function getBaseDir()
	{
		return $this->base_dir_;
	}
	
	function getActionName()
	{
		return $this->action_name_;
	}

	function getModuleDir()
	{
		$dir = $this->base_dir_;
		if($this->module_name_)
			$dir .= "/" . $this->module_name_;
		return $dir;
	}

	function setVarbose($flag=true)
	{
		$this->varbose_ = $flag;
	}

	function setRenderMode($mode)
	{
		$this->render_mode_ = $mode;
	}
	
	function getRenderMode()
	{
		return $this->render_mode_;
	}
	
	function _getViewNamingMap()
	{
		// インジェクション対策が面倒なのでここで
        $_VIEW_NAMING_MAP_ = array(
        	1=>"success",
        	2=>"input",
        	3=>"error"
        );

        return $_VIEW_NAMING_MAP_;
	}
	
	function _getViewName($action_return_value)
	{
		$map = $this->_getViewNamingMap();
		return isset($map[$action_return_value]) ? $map[$action_return_value] : "";
	}

	function validate()
	{
		return (strstr($this->action_name_,"..")===false);
	}

	function dispatch()
	{
		if(!$this->validate()) {
			print "バリデーションエラー";
			die();
		}
			
		// FIXME::ActionName の妥当性を調べるべき

		$now_aid = $this->aid_;
		$action_result = $this->_processAction($this->module_name_,$this->action_name_);
		
		if($action_result!=VIEW_NONE && $now_aid==$this->aid_) {
			$this->render_ = $this->_processView($this->module_name_,$this->action_name_,$action_result);
			if(is_object($this->render_))
				$this->render_->execute($this,$this->request_,$this->user_);
		}
	}

	/// dispatch の実行をストップするガードが必要?
	function forward($module_name,$action_name)
	{
		$this->aid_++;
		$now_aid = $this->aid_;
		$action_result = $this->_processAction($module_name,$action_name);
		
		if($action_result!=VIEW_NONE) {
			$this->render_ = $this->_processView($module_name,$action_name,$action_result);
			if(is_object($this->render_))
				$this->render_->execute($this,$this->request_,$this->user_);
		}
	}
	
	function &_processView($module_name,$action_name,$action_result)
	{
		$action_name=strtolower($action_name);
//		if(!($view_name=$this->_getViewName($action_result)))
//			die();

		$view_name = $action_result;
		
		// 一応チェック
		if(preg_match("#(\.\.|\\|/)#",$view_name))
			die();

		// ビュースタブクラス定義ファイルの取り込み
		$view_file = $this->base_dir_ ."/". $module_name ."/views/". $action_name . "_" . $view_name . ".class.php";

		if($this->varbose_)
			mojaTesting_warn ( "Read View File ... ".$view_file."<br/>" );

		if(file_exists($view_file)) {
			require_once ($view_file);
		}
		else	// view の場合は一応ここで死
			die();

		// クラスチェック
		$class_name = $module_name . "_" . ucfirst($action_name) . "View_" . $view_name;

		if($this->varbose_)
			mojaTesting_warn ( "check exsist class ... ".$class_name."<br/>" );

		if(!class_exists($class_name))
			die();

		// 実行可能
		$view_instance = new $class_name;
		$render = $view_instance->execute($this,$this->request_,$this->user_);

		if(!is_object($render))
			die();	// FIXME::do エラーハンドリング

		return $render;
	}

	/// $action_class_name からファイルの読み込み、クラスの確保を行い実行。
	function _processAction($module_name,$action_name)
	{
		$action_class_name = strtolower($action_name);

		//FIXME:: .. だけでいいような...あとで確認
		if(preg_match("#(\.\.|\\|/)#",$action_class_name))
			die();

		// ファイルを読む
		$action_file = $this->base_dir_ ."/". $module_name ."/actions/". $action_name . ".class.php";

		if($this->varbose_)
			mojaTesting_warn ( "Read Action file ".$action_file."<br/>" );

		if(file_exists($action_file)) {
			require_once ($action_file);
		}
		
		$class_name = $module_name . "_" . ucfirst($action_name) . "Action";
		if(!class_exists($class_name)) {
			trigger_error ( $class_name." is not defined." );
			die();
		}
			
		$action_instance = new $class_name;
		if($this->varbose_)
			mojaTesting_warn ( "Generate action instance<br/>" );
		
		// ユーザーチェック
		if($action_instance->isSecure() && !is_object($this->user_)) {
			header ( "location: ".XOOPS_URL."/user.php" );
			die();
		}

		// 管理者チェック
		if($action_instance->isAdmin()) {
			$flag = false;
			if(is_object($this->user_)) {
				// https 対応
				if(preg_match("/(http|https)::(.+)/",XOOPS_URL,$match)) {
					$xoops_naked_url = $match[2];
					$pattern = "#^(http|https)::".$xoops_naked_url."/modules/#";
					// FIXME:: 未検証です
					if(preg_match($pattern,$_SERVER['REQUEST_URI'])) {
						global $xoopsModule;
						$flag = $this->user_->isAdmin($xoopsModule->mid());
					}
					else
						$flag = $this->user_->isAdmin();
				}
			}
			if(!$flag) {
				$this->_errorIsAdmin();
			}
		}

		// 呼び出すメソッド名の検査
		$req_flag = $action_instance->getRequestMethods();

		if($req_flag==REQ_NONE) {
			return $action_instance->getDefaultView($this,$this->request_,$this->user_);
		}
		elseif($_SERVER['REQUEST_METHOD']=='POST') {
			return ($req_flag & REQ_POST ) ? 
				$action_instance->execute($this,$this->request_,$this->user_) :
				$action_instance->getDefaultView($this,$this->request_,$this->user_);
		}
		else {
			return ($req_flag & REQ_GET ) ? 
				$action_instance->execute($this,$this->request_,$this->user_) :
				$action_instance->getDefaultView($this,$this->request_,$this->user_);
		}

		return $action_instance->execute($this,$this->request_,$this->user_);
	}

	function _errorIsAdmin()
	{
		header ( "location: ".XOOPS_URL."/user.php" );
		die();
	}
}

/**
@biref HashMap ラッパ
@note Java 合わせのために、まあ、一応...
*/
class mojaLE_VR_Request
{
	var $attribute_;
	
	function mojaLE_VR_Request()
	{
		$this->attribute_ = array();
	}

	function setAttribute($key,$value)
	{
		$this->attribute_[$key] = $value;
	}
	
	function setAttributeByRef($key, &$value)
	{
		$this->attribute_[$key] =& $value;
	}
	
	function &getAttribute($key)
	{
		return isset($this->attribute_[$key]) ? $this->attribute_[$key] : null;
	}
}

?>