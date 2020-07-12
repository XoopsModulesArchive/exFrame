<?php
/**
@brief 確認画面で Confirm を押し、Form 検査に合格すると、プロセッサーの _process()
\par を呼び出すコンポーネントセット。
*/

require_once "exComponent/Preview.php";
require_once "exForm/PreviewForm.php";

require_once "exConfig/ForwardConfig.php";

require_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

/**
@note レンダーは ConfirmModelRender.php で定義されているものを推奨
*/
class exTypicalConfirmComponent extends exPreviewComponent {
	var $quick_message_=null;
	var $handler_;
	
	function exTypicalConfirmComponent($processor=null,$render=null,$name=null,$form=null,$forwards=null) {
		if(!is_object($processor))
			$processor = new exTypicalConfirmComponentProcessor();
		if(!is_object($render))
			$render = new exComponentRender();
		if(!is_object($form))
			$form = new exConfirmTicketForm();

		parent::exPreviewComponent($processor,$render,$name,$form);

		$this->setForwards($forwards);
	}

	function init($obj,$handler=null,$mes=null) {
		$this->data_=&$obj;
		if($handler!==null)
			$this->handler_=$handler;
		if($mes!==null)
			$this->quick_message_=$mes;
		return $this->doProcess();
	}
}


/**
@brief 一連の処理を典型的コントロールとしておさめた強力で応用の利かないタイプのプロセッサークラス
@return COMPONENT_INIT_SUCCESS か FAIL しか返さない
*/
class exTypicalConfirmComponentProcessor extends exComponentProcessor {
	function process(&$component) {
		// バリデーションを行う
		switch($ret=$component->form_->init($component->data_,$component->name_)) {
			// アクションフォームに失敗した場合は COMPONENT_INIT_FAIL にエラーを変更して戻す
			// _processActionformInitFail() が定義されていればそれをコールする
			// エラーの変換機構もいまはその方式で実装している
			case ACTIONFORM_INIT_FAIL:
				$ret = $this->_processActionformInitFail($component);
				break;

			// アクションフォームの通常初期化が終了した場合は、COMPONENT_INIT_SUCCESS に変更して戻す
			// _processActionformInitSuccess() が定義されていればそれをコールする
			// エラーの変換機構もいまはその方式で実装している
			case ACTIONFORM_INIT_SUCCESS:
				$ret = $this->_processActionformInitSuccess($component);
				break;

			case ACTIONFORM_POST_SUCCESS:
				$ret = $this->_processActionformPostSuccess($component);
				break;
		}

		return ($ret);
	}
	
	function _processActionformInitFail(&$component)
	{
		return COMPONENT_INIT_FAIL;
	}
	
	function _processActionformInitSuccess(&$component)
	{
		return COMPONENT_INIT_SUCCESS;
	}

	function _processActionformPostSuccess(&$component)
	{
		if($this->_processPost($component)) {
			if(isset($component->forwards_['success']))
				$component->forwards_['success']->doForward();
			else
				$ret=COMPONENT_INIT_SUCCESS;
		}
		else {
			if(isset($component->forwards_['fail']))
				$component->forwards_['fail']->doForward();
			else {
				$ret = $this->_processPostFail();
			}
		}

		return $ret;
	}
	
	// 最低限実装すべきロジック
	function _processPost($component)
	{
		return false;
	}

	// FailForward が実装されていなかった場合に呼び出される処理
	function _processPostFail($component)
	{
		return COMPONENT_INIT_FAIL;
	}

}

?>