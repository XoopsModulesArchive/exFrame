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

class RSS2Handler
{
	var $version_="1.0";
	var $encode_="utf-8";

	var $channel_title_=null;
	var $channel_link_=null;
	var $lastBuildDate_=0;
	var $items_=array();

	function RSS2Handler($encode="utf-8",$version="1.0")
	{
		$this->version_=$version;
		$this->encode_=$encode;
		$this->setLastBuildDate(time());
	}

	function setChannelTitle($title)
	{
		$this->channel_title_=$title;
	}

	function setChannelLink($link)
	{
		$this->channel_link_=$link;
	}

	function setLastBuildDate($ldate)
	{
		if(is_numeric($ldate))
			$ldate=date("r",$ldate);
		$this->lastBuildDate_=$ldate;
	}
	
	function addItem($title=null,$link=null,$desc=null,$pubdate=null,$guid=null)
	{
		$this->items_[]=new RSS2ItemHandler($title,$link,$desc,$pubdate,$guid);
	}
	
	function render()
	{
		$ret=array();
		$ret[]='<?xml version="'.$this->version_.'" encoding="'.$this->encode_.'"?>';
		$ret[]='<rss version="2.0">';
		$ret[]='<channel>';

		if($this->channel_title_)
			$ret[]="\t".'<title>'.$this->channel_title_.'</title>';

		if($this->channel_link_)
			$ret[]="\t".'<link>'.$this->channel_link_.'</link>';

		if($this->lastBuildDate_)
			$ret[]="\t".'<lastBuildDate>'.$this->lastBuildDate_.'</lastBuildDate>';

		if(count($this->items_)>0) {
    		foreach($this->items_ as $c) {
    			$tmp=$c->render();
        		foreach($tmp as $t) {
        			$ret[]="\t".$t;
        		}
        		unset($tmp);
    		}
		}

		$ret[]='</channel>';
		$ret[]='</rss>';

		return implode("\n",$ret);
	}
}

class RSS2ItemHandler
{
	var $title_;
	var $link_;
	var $description_;
	var $pubDate_;
	var $guid_;

	function RSS2ItemHandler($title=null,$link=null,$desc=null,$pubdate=null,$guid=null)
	{
		$this->setTitle($title);
		$this->setLink($link);
		$this->setDesc($desc);
		$this->setPubdate($pubdate);
		$this->setGuid($guid);
		$this->setTitle($title);
	}
	
	function setTitle($title)
	{
		$this->title_=$title;
	}

	function setLink($link)
	{
		$this->link_=$link;
	}

	function setDesc($desc)
	{
		$this->description_=$desc;
	}

	function setPubdate($pubdate)
	{
		if(is_numeric($pubdate))
			$pubdate=date("r",$pubdate);
		$this->pubDate_=$pubdate;
	}
	function setGuid($guid)
	{
		$this->guid_=$guid;
	}

	function render()
	{
		$ret=array();
		$ret[]="<item>";

		if($this->title_)
			$ret[]="\t<title>".$this->title_."</title>";

		if($this->link_)
			$ret[]="\t<link>".$this->link_."</link>";

		if($this->description_)
			$ret[]="\t<description>".$this->description_."</description>";

		if($this->pubDate_)
			$ret[]="\t<pubDate>".$this->pubDate_."</pubDate>";

		if($this->guid_)
			$ret[]="\t<guid>".$this->guid_."</guid>";

		$ret[]="</item>";

		return $ret;
	}
}
?>
