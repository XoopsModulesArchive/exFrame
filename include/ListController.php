<?php
/**
@brief 設計が終わっていない ListController （ページナビゲータ）
@verison $Id:$
*/

class ListController {
	// ---- 整理の必要がある ----
	var $start_;
	var $perpage_=20;
	var $total_=null;	/**< トータルの件数 */
	var $url_="";

	var $sort_=null;

	var $render_=null;
	var $filter_=null;
	var $navi_="";
	var $extra_=array();

	var $_configured_=false;
	var $_current_page_=null;
	
	var $offset_=5;
	var $sub_=true;

	var $maxpage_=null;

	function ListController($render=null) {
		if($render!==null)
			$this->render_=$render;
		else
			$this->render_=new ListControllerRender();
	}

	function fetch($total,$perpage) {
		$this->total_=$total;
		$this->perpage_=$perpage;
		
		$this->sort_=isset($_REQUEST['sort']) ? intval($_REQUEST['sort']) : null;

		$this->start_=isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
		if(!$this->perpage_) {
			$this->perpage_=isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 5;
			if(!$this->perpage_) $this->perpage_=1;
		}
		else 
			if(!$this->perpage_) $this->perpage_=1;
	}

	function getCriteria($start=null,$limit=null) {
		if($start) $this->start_=$start;
		if($limit) $this->perpage_=$limit;

		if($this->filter_)
			return $this->filter_->getCriteria($this->start_,$this->perpage_,$this->sort_);
	}

	function freeze() {
		if(!$this->_configured_) {
    
    		$this->_configured_=true;
    
			if($this->perpage_) {
    			$this->maxpage_=ceil($this->total_ / $this->perpage_);
    		}

			$this->current_page_=intval(floor(($this->start_+$this->perpage_)/$this->perpage_));
    
    		$this->navi_ = $this->renderNavi($this->offset_,$this->sub_);
		}
	}

	function renderUrl($start)
	{
		$url = $this->url_."?"."start=".($start)
			."&amp;perpage=" . $this->perpage_;
		if(count($this->extra_)) $url.="&amp;".$this->renderExtraUrl($this->extra_);
		if($this->filter_!==null) {
			$extra=$this->filter_->getExtra();
			if($extra)
				$url.="&amp;".$this->renderExtraUrl($extra);
		}
		return $url;
	}

	function renderUrl4Sort() {
		$start=$this->start_;

		$url = $this->url_."?"."start=".intval($start)
			."&amp;perpage=" . $this->perpage_;
		if(count($this->extra_)) $url.="&amp;".$this->renderExtraUrl($this->extra_);
		if($this->filter_!==null) {
			$extra=$this->filter_->getExtra();
			if($extra)
				$url.="&amp;".$this->renderExtraUrl($extra);
		}
		return $url;
	}

	function renderExtraUrl($extra) {
		$ret=array();
		if(is_array($extra)) {
    		foreach ( $extra as $key=>$value ) {
    			if(is_array($value)) {
    				foreach ( $value as $key2=>$value2 ) {
    					$ret[] =$key."[".$key2."]=".rawurlencode($value2);
    				}
    			}
    			else {
    				$ret[] = $key."=".rawurlencode($value);
    			}
    		}
    		return implode("&amp;",$ret);
		}
	}

	/**
	@brief ページナビを生成して返す
	@param $offset オフセット値。指定するとドラム式となる
	@param $sub true 時 先頭ページと最後尾ページを表示する
	*/
	function renderNavi ( $offset = 5, $sub=false ) {
		$ret ='';
		if ( $this->total_ <= $this->perpage_ ) {
			return $ret;
		}

		$this->_configured_=true;

		if ( $this->maxpage_ > 1 ) {
			$prev = $this->start_ - $this->perpage_;
			if ( $prev >= 0 ) {
				$ret.= $this->render_->renderPrePage($this->renderUrl($prev));
			}

			$ret_prefix ='';
			$ret_center ='';
			$ret_suffix ='';

			for($pageCounter=1;$pageCounter <= $this->maxpage_ ; $pageCounter++) {
				if($pageCounter == $this->current_page_) {
					$ret_center.= $this->render_->renderSpace();
					$ret_center.= $this->render_->renderSelectPage($pageCounter,$this->renderUrl($pageCounter));
				}
				else {
					if ( !$offset ) {	// ドラム指定がない場合
						$ret_center.= $this->render_->renderSpace();
						$ret_center.=$this->render_->renderNormalPage($this->renderUrl(($pageCounter-1)*($this->perpage_)), $pageCounter );
					}
					else { // ドラム方式
						if ( abs ( $pageCounter-$this->current_page_ ) < $offset ) {
							$ret_center.= $this->render_->renderSpace();
							$ret_center .= $this->render_->renderNormalPage ($this->renderUrl(($pageCounter-1)*$this->perpage_), $pageCounter );
						}
						elseif ( $sub ) {
							if ( $pageCounter == 1 ) {
								$ret_prefix .= $this->render_->renderNormalPage ($this->renderUrl(($pageCounter-1)*$this->perpage_),$pageCounter);
								$ret_prefix .= $this->render_->renderPart();
							}
							elseif ($pageCounter == $this->maxpage_) {
								$ret_suffix .= $this->render_->renderPart();
								$ret_suffix .= $this->render_->renderNormalPage ($this->renderUrl(($pageCounter-1)*$this->perpage_),$pageCounter);
							}
						}
					}
				}
			}

			$ret .= implode($this->render_->renderSpace(),
				array($ret_prefix,$ret_center,$ret_suffix));

			$next = $this->start_ + $this->perpage_;
			if ( $this->total_ > $next ) {
				$ret.= $this->render_->renderSpace();
				$ret.= $this->render_->renderNextPage($this->renderUrl($next));
			}
		}
		return $ret;
	}

	/**
	@brief フィルター用セッタ
	*/
	function setFilter(&$filter)
	{
		$this->filter_=&$filter;
	}

	/**
	@brief フィルター用ゲッタ
	*/
	function &getFilter()
	{
		return $this->filter_;
	}
	
	/**
	@brief メンバを連想配列で返す（試験的）
	@param $type dummy
	*/
	function getArray($type='s')
	{
		$ret=array();
		$ret['start']=$this->start_;
		$ret['perpage']=$this->perpage_;
		$ret['total']=$this->total_;
		$ret['navi']=$this->navi_;
		return $ret;
	}
	
	/**
	@brief メンバを連想配列で返す（試験的） getArray のエイリアス
	@param $type dummy
	*/
	function getStructure($type='s')
	{
		return $this->getArray($type);
	}
}

class ListControllerRender {
	var $style_normal_ = "<a href='%s'>%u</a>";
	var $style_select_ = "<b>(%u)</b>";
	var $style_pre_ = "<a href='%s'>&laquo;</a>";
	var $style_next_ = "<a href='%s'>&raquo;</a>";
	var $style_part_ = "...";
	var $style_space_ = "&nbsp;";

	function renderSelectPage($url,$page) {
		return @sprintf($this->style_select_,$url,$page);
	}

	function renderNormalPage($url,$page) {
		return @sprintf($this->style_normal_,$url,$page);
	}

	function renderPrePage($url) {
		return @sprintf($this->style_pre_,$url);
	}

	function renderNextPage($url) {
		return @sprintf($this->style_next_,$url);
	}

	function renderPart() {
		return $this->style_part_;
	}
	
	function renderSpace()
	{
		return $this->style_space_;
	}
}


?>