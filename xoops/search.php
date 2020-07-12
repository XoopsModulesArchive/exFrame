<?php
/**
@brief 検索実装を助けるクラスの定義を行っているファイル
@author minahito
@version $Id$
*/

require_once "exForm/Filter.php";

class XoopsSearchFieldInfo {
	var $fields_ = array();
	var $extra_ = array();
	var $query_fields_ = array();
	var $uid_field_ ="";
	var $sort_="";
	var $order_="DESC";

	/**
	@brief title, time, uid に割り当てるフィールド名を定義します
	*/
	function setFields ( $title, $time, $uid ) {
		if($title)
			$this->fields_['title'] = $title;

		if($time)
			$this->fields_['time'] = $time;

		if($uid)
			$this->fields_['uid']=$uid;
	}
	
	/**
	@brief 追加で得たいフィールド名の設定
	@param $field フィールド名（もしくはその配列）
	*/
	function addExtra ( $field ) {
		if(is_array($field)) {
			foreach($field as $str)
				$this->extra_[] = $str;
		}
		else
			$this->extra_[] = $field;
	}

	/**
	@brief このメソッドは addExtra のエリアスです
	@param $field フィールド名（もしくはその配列）
	*/
	function setExtra ( $field ) {
		$this->addExtra($field);
	}


	/**
	@brief SQL 文の SELECT パラメータに出せる形のフィールド名を配列で戻します
	@return array
	*/
	function getSelectParams() {
		$ret = array();
		foreach ( $this->fields_ as $key=>$val ) {
			if($key!=$val)
				$ret[] = $val." AS ".$key;
			else
				$ret[] = $key;
		}

		foreach ( $this->extra_ as $str )
			$ret[] = $str;

		return $ret;
	}

	/**
	@brief 文字列検索対象となるフィールド名の追加
	@param $field フィールド名（もしくはその配列）
	*/
	function addQueryField ( $field ) {
		if(is_array($field)) {
			foreach($field as $str)
				$this->query_fields_[] = $str;
		}
		else
			$this->query_fields_[] = $field;
	}
	
	/**
	@brief このメソッドは addQueryField のエリアスです
	@param $field フィールド名
	*/
	function setQueryField ( $field ) {
		$this->addQueryField ( $field );
	}

	/**
	@brief 文字列検索対象となるフィールド名を返します
	@return array
	*/
	function getQueryField() {
		return $this->query_fields_;
	}

	/**
	@brief uid 検索対象となるフィールド名の追加
	@param $field フィールド名
	*/
	function setUidField ( $field ) {
		$this->uid_field_ = $field;
	}

	/**
	@brief uid 検索対象となるフィールド名を返します
	@return string
	*/
	function getUidField() {
		return $this->uid_field_;
	}
	
	/**
	@brief ORDER BY に使用するフィールド名のセット
	*/
	function setOrder ( $order ) {
		$order = strtoupper ( $order );
		if($order=='DESC' || $order='ASC') {
			$this->order_=$order;
		}
	}

	/**
	@brief ORDER BY に用いるフィールド名を返します
	@return string
	*/
	function getOrder() {
		return $this->order_;
	}

	/**
	@brief ソート順のセット
	@param $sort 'ASC' もしくは 'DESC'
	*/
	function setSort ( $sort ) {
		$this->sort_ = $sort;
	}
	
	/**
	@brief ソート設定を返します
	@return string
	*/
	function getSort() {
		return $this->sort_;
	}
}

class XoopsSearchFilter extends exAbstractFilterForm {
	var $querys_=array();
	var $uid_=null;
	var $andor_="AND";
	var $start_;
	var $limit_;
	
	function XoopsSearchFilter($querys,$andor,$limit,$offset,$userid)
	{
		$this->querys_=$querys;
		$this->andor_=$andor;
		$this->uid_=$userid;
		$this->start_=$offset;
		$this->limit_=$limit;
	}
	
	/**
	@param $info XoopsSearchFieldInfo のインスタンス
	@return object Criteria class のインスタンス
	*/
	function getCriteria(&$info)
	{
		$criteria=$this->getDefaultCriteria($info);
		return $criteria;
	}

	/**
	@param $info XoopsSearchFieldInfo のインスタンス
	@return object Criteria class のインスタンス
	*/
	function getDefaultCriteria(&$info)
	{
		$criteria=new CriteriaCompo();

		$querys = $info->getQueryField();

		// テキストフィールド文字列の定義と、クエリの入力の両方があれば Criteria に追加
		if ( count($querys)>0 && is_array($this->querys_) && count($this->querys_) ) {
    		foreach ( $this->querys_ as $str ) {
    			$criteria_text = new CriteriaCompo();
    			foreach ( $querys as $field_name ) {
    				$criteria_text->add(new Criteria($field_name, '%'.$str.'%', 'LIKE'),'OR');
    			}
    			$criteria->add ( $criteria_text, $this->andor_ );
    			unset ( $criteria_text );
    		}
		}

		// uid も同じ理屈で
		$uid_field = $info->getUidField();
		if( $uid_field && $this->uid_ ) {
			$criteria->add ( new Criteria($uid_field,$this->uid_), $this->andor_ );
		}

		// 指定されていればオーダーを定義する
		if($info->getSort()) {
			$criteria->setSort($info->getSort());
			$criteria->setOrder($info->getOrder());
		}

		$criteria->setStart($this->start_);
		$criteria->setLimit($this->limit_);
		return $criteria;
	}
}

class SimpleXoopsSearchService {
	var $info_;	///< XoopsSearchFieldInfo のインスタンス
	var $filter_;	///< XoopsSearchFieldInfo のインスタンス
	var $table_;	///< テーブル名

	var $db_;		///< xoopsDB のインスタンス（グローバルから取得）

	var $info_class_ = "XoopsSearchFieldInfo";	///< info のクラス名
	var $filter_class_ = "XoopsSearchFilter";	///< フィルタのクラス名

	function SimpleXoopsSearchService($queryarray,$andor,$limit,$offset,$userid) {
		global $xoopsDB;

		$classname = $this->info_class_;
		$this->info_ = new $classname();

		$classname = $this->filter_class_;
		$this->filter_ = new $classname ($queryarray,$andor,$limit,$offset,$userid);

		$this->db_=&$xoopsDB;

		$this->startup();
	}
	
	/**
	@brief コンストラクタがコールする初期化用メソッドです
	*/
	function startup() {
	}
	
	/**
	@brief 検索対象となるテーブル名
	@param $table string
	*/
	function setTable ( $table ) {
		$this->table_ = $table;
	}

	/**
	@brief このメソッドでキー 'link' を追加定義して返します
	@param $row SQL 実行結果の連想配列
	*/
	function getResultArray(&$row) {
		$row['link'] = "";	// ここを変更します
		return $row;
	}

	/**
	@return 検索結果を積んだ連想配列の配列
	*/
	function doService() {
		$sql = "SELECT ".implode(",",$this->info_->getSelectParams())." FROM ".
				$this->db_->prefix($this->table_)." ";

		$criteria=$this->filter_->getCriteria($this->info_);
		$sql .= $criteria->renderWhere();
		if($criteria->getOrder())
			$sql .= " ORDER BY ".$criteria->getSort()." ".$criteria->getOrder();

//		print $sql;

		$ret =array();
		$result = $this->db_->query($sql, $criteria->getLimit(), $criteria->getStart());
		
		while($row=&$this->db_->fetchArray($result)) {
			$ret[] = $this->getResultArray($row);
		}

		return $ret;
	}
}

?>