<?php
/**
@version $Id: RSS2Handler.php,v 1.2 2004/08/29 10:14:21 minahito Exp $

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

require_once "exTra/RSS2Handler.php";

class RSS1Handler extends RSS2Handler
{
	function addItem($title=null,$link=null,$desc=null,$pubdate=null,$guid=null)
	{
		$this->items_[]=new RSS1ItemHandler($title,$link,$desc,$pubdate,$guid);
	}
	
	function setLastBuildDate($ldate)
	{
		if(is_numeric($ldate))
			$ldate=date("Y-m-d",$ldate)."T".date("H:i:s+09:00",$ldate);
		$this->lastBuildDate_=$ldate;
	}

	/**
	@ToDo テンプレート使え..と
	*/
	function render()
	{
		$ret=array();
		$ret[]='<?xml version="'.$this->version_.'" encoding="'.$this->encode_.'"?>';
		$ret[]='<rdf:RDF xmlns:dc="http://purl.org/dc/elements/1.1/"';
		$ret[]='    xmlns="http://purl.org/rss/1.0/"';
		$ret[]='    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"';
		$ret[]='    xml:lang="ja">';

		$ret[]='<channel rdf:about="'.$this->channel_link_.'">';
		if($this->channel_title_)
			$ret[]="\t".'<title>'.$this->channel_title_.'</title>';

		if($this->channel_link_)
			$ret[]="\t".'<link>'.$this->channel_link_.'</link>';

		if($this->lastBuildDate_)
			$ret[]="\t".'<dc:date>'.$this->lastBuildDate_.'</dc:date>';

		if(count($this->items_)>0) {
			$ret[]="\t<items>";
			$ret[]="\t\t<rdf:Seq>";
    		foreach($this->items_ as $c) {
    			$tmp=$c->renderSeq();
        		foreach($tmp as $t) {
        			$ret[]="\t".$t;
        		}
        		unset($tmp);
    		}
			$ret[]="\t\t</rdf:Seq>";
			$ret[]="\t</items>";
		}
		
		$ret[]="</channel>";


		if(count($this->items_)>0) {
    		foreach($this->items_ as $c) {
    			$tmp=$c->render();
        		foreach($tmp as $t) {
        			$ret[]="\t".$t;
        		}
        		unset($tmp);
    		}
		}

		$ret[]='</rdf:RDF>';

		return implode("\n",$ret);
	}
}

class RSS1ItemHandler extends RSS2ItemHandler
{
	function renderSeq() {
		$ret=array();
		$ret[] = '<rdf:li rdf:resource="'.$this->link_.'" />';
		return $ret;
	}

	function setPubdate($pubdate)
	{
		if(is_numeric($pubdate))
			$pubdate=date("Y-m-d",$pubdate)."T".date("H:i:s+09:00",$pubdate);
		$this->pubDate_=$pubdate;
	}

	function render()
	{
		$ret=array();
		$ret[]="<item>";

		if($this->title_)
			$ret[]="\t<title>".$this->title_."</title>";

		if($this->link_)
			$ret[]="\t<link>".$this->link_."</link>";

		if($this->pubDate_)
			$ret[]="\t<dc:date>".$this->pubDate_."</dc:date>";

		$ret[]="</item>";

		return $ret;
	}
}
?>
