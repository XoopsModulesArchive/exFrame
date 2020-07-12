<?php
/**
@brief ��������������륯�饹�������ԤäƤ���ե�����
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
	@brief title, time, uid �˳�����Ƥ�ե������̾��������ޤ�
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
	@brief �ɲä��������ե������̾������
	@param $field �ե������̾�ʤ⤷���Ϥ��������
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
	@brief ���Υ᥽�åɤ� addExtra �Υ��ꥢ���Ǥ�
	@param $field �ե������̾�ʤ⤷���Ϥ��������
	*/
	function setExtra ( $field ) {
		$this->addExtra($field);
	}


	/**
	@brief SQL ʸ�� SELECT �ѥ�᡼���˽Ф�����Υե������̾��������ᤷ�ޤ�
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
	@brief ʸ���󸡺��оݤȤʤ�ե������̾���ɲ�
	@param $field �ե������̾�ʤ⤷���Ϥ��������
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
	@brief ���Υ᥽�åɤ� addQueryField �Υ��ꥢ���Ǥ�
	@param $field �ե������̾
	*/
	function setQueryField ( $field ) {
		$this->addQueryField ( $field );
	}

	/**
	@brief ʸ���󸡺��оݤȤʤ�ե������̾���֤��ޤ�
	@return array
	*/
	function getQueryField() {
		return $this->query_fields_;
	}

	/**
	@brief uid �����оݤȤʤ�ե������̾���ɲ�
	@param $field �ե������̾
	*/
	function setUidField ( $field ) {
		$this->uid_field_ = $field;
	}

	/**
	@brief uid �����оݤȤʤ�ե������̾���֤��ޤ�
	@return string
	*/
	function getUidField() {
		return $this->uid_field_;
	}
	
	/**
	@brief ORDER BY �˻��Ѥ���ե������̾�Υ��å�
	*/
	function setOrder ( $order ) {
		$order = strtoupper ( $order );
		if($order=='DESC' || $order='ASC') {
			$this->order_=$order;
		}
	}

	/**
	@brief ORDER BY ���Ѥ���ե������̾���֤��ޤ�
	@return string
	*/
	function getOrder() {
		return $this->order_;
	}

	/**
	@brief �����Ƚ�Υ��å�
	@param $sort 'ASC' �⤷���� 'DESC'
	*/
	function setSort ( $sort ) {
		$this->sort_ = $sort;
	}
	
	/**
	@brief ������������֤��ޤ�
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
	@param $info XoopsSearchFieldInfo �Υ��󥹥���
	@return object Criteria class �Υ��󥹥���
	*/
	function getCriteria(&$info)
	{
		$criteria=$this->getDefaultCriteria($info);
		return $criteria;
	}

	/**
	@param $info XoopsSearchFieldInfo �Υ��󥹥���
	@return object Criteria class �Υ��󥹥���
	*/
	function getDefaultCriteria(&$info)
	{
		$criteria=new CriteriaCompo();

		$querys = $info->getQueryField();

		// �ƥ����ȥե������ʸ���������ȡ�����������Ϥ�ξ��������� Criteria ���ɲ�
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

		// uid ��Ʊ��������
		$uid_field = $info->getUidField();
		if( $uid_field && $this->uid_ ) {
			$criteria->add ( new Criteria($uid_field,$this->uid_), $this->andor_ );
		}

		// ���ꤵ��Ƥ���Х����������������
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
	var $info_;	///< XoopsSearchFieldInfo �Υ��󥹥���
	var $filter_;	///< XoopsSearchFieldInfo �Υ��󥹥���
	var $table_;	///< �ơ��֥�̾

	var $db_;		///< xoopsDB �Υ��󥹥��󥹡ʥ����Х뤫�������

	var $info_class_ = "XoopsSearchFieldInfo";	///< info �Υ��饹̾
	var $filter_class_ = "XoopsSearchFilter";	///< �ե��륿�Υ��饹̾

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
	@brief ���󥹥ȥ饯���������뤹�������ѥ᥽�åɤǤ�
	*/
	function startup() {
	}
	
	/**
	@brief �����оݤȤʤ�ơ��֥�̾
	@param $table string
	*/
	function setTable ( $table ) {
		$this->table_ = $table;
	}

	/**
	@brief ���Υ᥽�åɤǥ��� 'link' ���ɲ���������֤��ޤ�
	@param $row SQL �¹Է�̤�Ϣ������
	*/
	function getResultArray(&$row) {
		$row['link'] = "";	// �������ѹ����ޤ�
		return $row;
	}

	/**
	@return ������̤��Ѥ��Ϣ�����������
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