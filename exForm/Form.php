<?php
/**
@file
@version $Id: Form.php,v 1.12 2005/03/19 06:18:11 minahito Exp $
*/

define ( "ACTIONFORM_INIT_FAIL", '__error__actionform_init_fail__' );
define ( "ACTIONFORM_INIT_SUCCESS", '__actionform_init_success__' );
define ( "ACTIONFORM_POST_SUCCESS", '__actionform_post_success__' );

/**
@brief 新しい基底
@note exAbstractForm と再統合する可能性が大いにあるのでここから引っ張らないで下さい
*/
class exAbstractFormObject {
	var $msg_;				///< エラーメッセージバッファ
	var $err_render_=null;	///< エラーメッセージをレンダリングする exFormErrorRender クラスのインスタンスを保持する

	function exAbstractFormObject() {
		$this->msg_=array();
		$this->err_render_=new exFormErrorRender();
	}

	/**
	@brief $msg をエラーメッセージバッファに追加する
	@param $msg エラーメッセージ文字列
	@return なし
	*/
	function addError($msg) {
		$this->msg_[]=$msg;
	}

	/**
	@brief エラーメッセージバッファにメッセージの登録があるかどうかを調べます。
	@return エラーメッセージがあれば true なければ false を返します
	*/
	function isError() {
		return count($this->msg_);
	}

	/**
	@brief エラーメッセージを文字列で返します。
	@return string
	*/
	function getHtmlErrors() {
		$this->err_render_->init($this);
		return $this->err_render_->render();
	}
}

/**
@brief 基底(元 AbstractBase)
@deprecated この XoopsObject との連携を前提にデザインされたアクションフォームは非推奨です。
ほぼ同様の動作を行う exAbstractActionFormEx が代替クラスとして使用できます。
このクラスは過去のコンポーネントの移行が終わるまでの間は残されますが、将来的には削除されます。
*/
class exAbstractForm extends exAbstractFormObject {
	var $data_;	///< 初期化メソッドで渡されたインスタンスをキープする
	
	function init($data=null) {
		if($data) {
			$this->data_=&$data;
		}
		if($_SERVER['REQUEST_METHOD']=='POST') {
			$this->doPost($data);
			return count($this->msg_) ? ACTIONFORM_INIT_FAIL : ACTIONFORM_POST_SUCCESS;
		}
		elseif($_SERVER['REQUEST_METHOD']=='GET') {
			$this->doGet($data);
			return count($this->msg_) ? ACTIONFORM_INIT_FAIL : ACTIONFORM_INIT_SUCCESS;
		}
		else {
			return ACTIONFORM_INIT_FAIL;
		}
		
	}

	function doGet($data) { }

	function doPost($data) { }

	function initSelf() {
		$dmy=null;
		return $this->init($dmy);
	}
}

class exAbstractActionForm extends exAbstractForm {
	/**
	@biref 数値 numValue が min と max の範囲に収まっているかを検査します
	@param $numValue 検査対象となる数値
	@param $min 検査範囲の最小値
	@param $max 検査範囲の最大値
	@return bool min 以下 max 以上であれば true そうでなければ false を戻します
	*/
	function validateInRange($numValue,$min,$max) {
		return ($numValue>=$min && $numValue<=$max);
	}

	/**
	@brief 文字列 string の文字列が max 文字以下か検査します
	@param $string 検査対象の文字列
	@param $max 指定する文字数の最大値
	@return bool 指定文字数内に収まっていれば true そうでなければ false が戻ります
	*/
	function validateMaxLength($string,$max) {
		return (strlen($string)<=$max);
	}

	/**
	@brief 文字列 string の文字列が min 文字以上か検査します
	@param $string 検査対象の文字列
	@param $max 指定する文字数の最小値
	@return bool 指定文字数以上あれば true そうでなければ false が戻ります
	*/
	function validateMinLength($string,$min) {
		return (strlen($string)>=$min);
	}

	function validatePositive($value) {
		return (intval($value)>0);
	}

	function validateHttpUrl($value) {
		return (strpos($value,"http://")===0 or strpos($value,"https://")===0);
	}

	function getPositive($value) {
		$ret=intval($value);
		if($ret<=0)
			return 0;
		else
			return $ret;
	}
}

/**
@brief 抽象的なアクションフォーム
@todo exAbstractFormObject からの基底に切り替える
*/
class exActionForm extends exAbstractActionForm {
	/**
	@brief このアクションフォームをリクエストに応じて実行する
	ActionForm は POST リクエスト時のみに fetch を行う
	mojaLE から使いやすくするために load/doGet との連動は行わない
	@return ACTIONFORM_XXXX
	@note 戻り値について
		POST リクエスト時、fetch で問題が発生すれば ACTIONFORM_INIT_FAIL を
		問題が発生しなければ ACTIONFORM_POST_SUCCESS を返します
		POST リクエスト以外の場合は ACTIONFORM_INIT_SUCCESS を返します
	*/
	function init($master) {
		if($_SERVER['REQUEST_METHOD']=='POST') {
			$this->fetch();
			return count($this->msg_) ? ACTIONFORM_INIT_FAIL : ACTIONFORM_POST_SUCCESS;
		}
		else {
			return ACTIONFORM_INIT_SUCCESS;
		}
	}

	/**
	@brief リクエストなどからデータを抽出し、アクションフォームにデータを流し込む
	*/
	function fetch() {
	}

	/**
	@brief 渡されたマスターデータからこのアクションフォームのメンバを構築する
	@param $master データ
	*/
	function load(&$master) {
	}

	/**
	@brief 渡されたマスターデータにこのアクションフォームの情報を渡す
	@param $master データ
	*/
	function update(&$master) {
	}
}

/**
@brief GET/POST の動作に応じて挙動を変更するアクションフォーム
このアクションフォームは、 GET リクエストの際は load メソッドを、POST リクエストの際は fetch メソッドを
実行します。
\par このアクションフォームは mojaLE 側で GET/POST の切り分けを行わない場合、mojaLE を使わない場合に、簡
易フレームワーク的な挙動をしますので便利です。
*/
class exActionFormEx extends exAbstractActionForm {
	/**
	@brief このアクションフォームをリクエストに応じて実行する。
	\par ActionForm は POST リクエスト時のみに fetch を行う。
	exAbstractActionForm と異なり GET リクエストの際には load を行う。
	\par mojaLE など GET/POST を使い分ける Action と併用する場合は exAbstractActionForm を使用するべきです。
	@param $master fetch および load への引数
	@return POST リクエスト時、fetch で問題が発生すれば ACTIONFORM_INIT_FAIL を
			問題が発生しなければ ACTIONFORM_POST_SUCCESS を返します
			\par GET リクエスト時、load で問題が発生すれば ACTIONFORM_INIT_FAIL を
			問題が発生しなければ ACTIONFORM_INIT_FAIL を返します
	*/
	function init(&$master) {
		if($_SERVER['REQUEST_METHOD']=='POST') {
			$this->fetch($master);
			return count($this->msg_) ? ACTIONFORM_INIT_FAIL : ACTIONFORM_POST_SUCCESS;
		}
		else {
			$this->load($master);
			return count($this->msg_) ? ACTIONFORM_INIT_FAIL : ACTIONFORM_INIT_SUCCESS;
		}
	}

	function fetch(&$master)
	{
		$this->load($master);
	}

}


/**
@brief AbstractForm 系のインスタンスが内部プロパティ msg に蓄積したメッセージを画面表示する
手段を提供する class
\par exAbstractFormObject の getHtmlErrors メソッドがコールされたとき、このレンダークラスの
render メソッドが呼び出されます。
\par このクラスは標準の exAbstractFormObject がレンダラとして用い、エラーコードを <li> タグで
赤い文字を使ってレンダリングします。
\par エラーメッセージの表示の動きを変えたいとき、レンダラの動作を変更することがひとつの方法に
なります。詳しくは exAbstractFormObject の $err_render_ メンバを見てください。
*/
class exFormErrorRender {
	var $form_=null;

	function init($form) {
		$this->form_=$form;
	}

	function render() {
		$ret ="<ul>";
		foreach($this->form_->msg_ as $m) {
			$ret.=@sprintf("<li><font color='red'>%s</font></li>\n",$m);
		}
		$ret .="</ul>";
		return $ret;
	}
}

?>