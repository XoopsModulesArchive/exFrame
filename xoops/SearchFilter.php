<?php
/**
@brief Xoops2 の search 仕様に合わせた Filter です
*/

require_once "exForm/Filter.php";

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
		$this->start_=$start;
		$this->limit_=$limit;
	}

	function getCriteria()
	{
		$criteria=$this->getDefaultCriteria();
		return $criteria;
	}

	function getDefaultCriteria()
	{
		$criteria=new CriteriaCompo();
		$criteria->setStart($this->start_);
		$criteria->setLimit($this->limit_);
		return $criteria;
	}
}

?>