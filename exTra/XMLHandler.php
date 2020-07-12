<?php
/**
@version $Id: XMLHandler.php,v 1.3 2004/08/29 10:15:51 minahito Exp $

\section copyright Copyright and license
 Copyright (c) 2003-2004, minahito
 All rights reserved.

 Redistribution and use in source and binary forms, with or without
 modification, are permitted provided that the following conditions are met:

1 Redistributions of source code must retain the above copyright notice, 
  this list of conditions and the following disclaimer.

2 Redistributions in binary form must reproduce the above copyright notice, 
  this list of conditions and the following disclaimer in the documentation
  and/or other materials provided with the distribution.

3 Neither the name of the nor the names of its contributors may be used to 
  endorse or promote products derived from this software without specific 
  prior written permission.

 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS
 BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, 
 OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
 SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
 INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
 WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF 
 SUCH DAMAGE.
*/

/**
@brief ノード内のエレメントをあらわすクラス
*/
class exXMLNodeElement {
	var $name_;
	var $value_;
	
	function exXMLNodeElement($name,$value)
	{
		$this->name_;
		$this->value_;
	}

	function render()
	{
		return ' '.$this->name_.'="'.$this->value_.'" ';
	}
}

class exXMLHandler
{
	var $version_;
	var $encode_;
	var $child_;
	
	function exXMLHandler($encode='iso-8859-1', $version='1.0')
	{
		$this->encode_=$encode;
		$this->version_=$version;
		$this->child_=array();
	}

	function add($node)
	{
		$this->child_[]=$node;
	}

	function &render()
	{
		$ret=array();
		$ret[]='<?xml version="'.$this->version_.'" encoding="'.$this->encode_.'"?>';

		if(count($this->child_)>0) {
			$tmp=array();
    		foreach($this->child_ as $c) {
    			$tmp=$c->render();
        		foreach($tmp as $t) {
        			$ret[]="\t".$t;
        		}
    		}
		}

		return implode("\n",$ret);
	}
}

class exXMLNode
{
	var $name_;
	var $elements_=array();
	var $child_=array();
	var $value_;
	
	function exXMLNode($name,$elements=null,$value=null)
	{
		$this->name_=$name;
		$this->value_=$value;
		if($elements)
			$this->addElements($elements);
	}
	
	/**
	@brief エレメントを追加する。配列で渡すこともできます
	@param $elements exXMLNodeElement or exXMLNodeElement の array
	*/
	function addElements($elements)
	{
		if(is_array($elements)) {
			foreach($elements as $ele)
				$this->elements_[]=$ele;
		}
		else
			$this->elements_[]=$elements;
	}

	function add($node)
	{
		$this->child_[]=$node;
	}

	function &render()
	{
		$ret=array();

		// 子ノードがあれば処理する
		if(count($this->child_)>0) {
			if($this->elements_) {
				$tmp="<".$this->name_;
				foreach($this->elements_ as $ele)
					$tmp.=$ele->render();
				$tmp.=">";
				$ret[]=$tmp;
			}
			else
				$ret[]="<".$this->name_.">";

			$tmp=array();
    		foreach($this->child_ as $c) {
    			$tmp=$c->render();
        		foreach($tmp as $t) {
        			$ret[]="\t".$t;
        		}
    		}
			$ret[]="</".$this->name_.">";
		}
		elseif($this->value_!==null) {	// なければ value を埋める
			$ret[]="<".$this->name_.">".$this->value_."</".$this->name_.">";
		}
		else {
			$ret[]="<".$this->name_." />";
		}


		return $ret;
	}
}

?>