<?php
/**
@brief ワンタイムチケット付のアクションフォームを定義しているファイル
@todo init をストラテジで切り替え
*/

require_once "exForm/Form.php";
require_once "include/OnetimeTicket.php";

/**
@brief チケット機能付きのアクションフォームです
@note チケットは内部で自動生成。検査合格にはチケットの照会が必要で、照会に失敗した場合は
	チケットを作り直します。継承クラスでは getTicketErrorMessage をオーバーライドして適
	切なエラーメッセージを定義しておくことをお勧めします
*/
class exTicketActionFormEx extends exActionForm
{
	var $ticket_=null;	///< ワンタイムチケットのインスタンス
	
	function init(&$master) {
		if($_SERVER['REQUEST_METHOD']=='POST') {
			$this->fetch($master);
			if(count($this->msg_))
				return ACTIONFORM_INIT_FAIL;
			else {
				// チケットをセッションから消しておく
				exOnetimeTicket::unsetSession($this->getTicketName());
				return ACTIONFORM_POST_SUCCESS;
			}
		}
		else {
			$this->load($master);
			$this->createTicket();
			return count($this->msg_) ? ACTIONFORM_INIT_FAIL : ACTIONFORM_INIT_SUCCESS;
		}
	}

	/**
	@brief ワンタイムチケットの問い合わせを行っている fetch
	@note 継承クラスでオーバーライドする際、必ず parent::fetch($master) してください
	*/
	function fetch(&$master)
	{
		parent::fetch($master);
		if(!exOnetimeTicket::inquiry($this->getTicketName())) {
			$this->addError($this->getTicketErrorMessage());
			// 新しいチケットの発行
			$this->createTicket();
		}
	}

	/**
	@brief ワンタイムチケットクラスを返します
	*/
	function &getOnetimeTicket()
	{
		return $this->ticket_;
	}

	/**
	@brief ワンタイムチケットの発行
	*/
	function createTicket()
	{
		$this->ticket_ = new exOnetimeTicket($this->getTicketName(),$this->getTicketLifetime(),EXFRAME_SALT);
		$this->ticket_->setSession();
	}

	/**
	@brief チケットの認識名を返します
	@note 必要に応じて継承クラスでオーバーライドを行ってください
	@return string
	*/
	function getTicketName()
	{
		return get_class($this);
	}

	/**
	@brief チケットのライフタイムを返します（秒）
	@note 必要に応じて継承クラスでオーバーライドを行ってください
	@return int
	*/
	function getTicketLifetime()
	{
		// 60分を返す
		return 3600;
	}

	/**
	@brief チケットエラー時のエラーメッセージを返します。
	@return string
	*/
	function getTicketErrorMessage()
	{
		return "TICKET ERROR";
	}
}


?>