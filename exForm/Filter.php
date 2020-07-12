<?php
/**
@brief 絞り込みのためのフィルタ
@author minahito
@version $Id: Filter.php,v 1.6 2005/04/07 12:05:11 minahito Exp $
*/

require_once "exForm/Form.php";

define ("EXFILTER_FETCH_AUTO",0);
define ("EXFILTER_FETCH_MANUAL",0);

define ("EXFILTER_METHOD_BOTH",0);
define ("EXFILTER_METHOD_GET",1);
define ("EXFILTER_METHOD_POST",2);

/**
@brief 絞り込み
*/
class exAbstractFilterForm extends exAbstractActionForm {
	var $fetch_=EXFILTER_FETCH_AUTO;
	var $sort_=array();
	var $_conf_=EXFILTER_METHOD_BOTH;

	/**
	@brief ここで auto fetch
	*/
	function exAbstractFilterForm() {
		if($this->fetch_==EXFILTER_FETCH_AUTO)
			$this->fetch();
	}
	
	function fetch() { }

	function getCriteria($start=0,$limit=0,$sort=0) { }

	/**
	@brief PageController 系から呼び出されるメソッド
	*/
	function getExtra() {}

	function getPositiveIntger($key) {
		$ret =intval($this->_getRequest($key));
		return ($ret<0) ? 0 : $ret;
	}
	
	function _getRequest($key) {
		switch($this->_conf_) {
			case EXFILTER_METHOD_BOTH:
				return isset($_REQUEST[$key]) ? $_REQUEST[$key] : null;
				break;

			case EXFILTER_METHOD_GET:
				return isset($_GET[$key]) ? $_GET[$key] : null;
				break;

			case EXFILTER_METHOD_POST:
				return isset($_POST[$key]) ? $_POST[$key] : null;
				break;
		}
		return null;
	}
	
	function getDefaultCriteria($start=0,$limit=0)
	{
		$ret=new CriteriaCompo();
		$ret->setStart($start);
		$ret->setLimit($limit);
		return $ret;
	}

	function getSortCriteria($start=0,$limit=0,$sort=0)
	{
		$ret=&$this->getDefaultCriteria($start,$limit);
		if(!$sort) return $ret;

		$sortkey=abs($sort);
		if($sortkey>$this->getCountSortProperty()) {
			return $ret;
		}

		$ret->setSort($this->getSortProperty($sortkey));
		$ret->order = ($sort<0) ? "DESC" : "ASC";

		return $ret; 	
	}

	function getCountSortProperty()
	{
		return count($this->sort_);
	}

	function getSortProperties()
	{
		return $this->sort_;
	}

	function getSortProperty($key)
	{
		return is_numeric($key) ? $this->sort_[intval($key-1)] : null;
	}
}

?>