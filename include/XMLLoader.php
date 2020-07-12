<?php
/**
@brief XML をロード、解析するための簡易 class
@version $Id: XMLLoader.php,v 1.4 2004/08/20 08:10:35 minahito Exp $
*/

/**
@brief XML をロード、解析するため
の簡易的な class
*/
class AbstractXMLLoader {
	var $parser_;
	var $msg_;
	var $processor_;

	function AbstractXMLLoader($processor=null) {
		$this->msg_=array();
		if($processor!==null) {
			$this->processor_=$processor;
		}
	}

	/**
	@brief ロード
	*/
	function load($file,$charset='iso-8859-1') {
		if(!file_exists($file)) {
			$this->msg_[]="File does not exist";
			return false;
		}
		$this->parser_ = xml_parser_create($charset);

		xml_set_object($this->parser_,$this);
		xml_parser_set_option($this->parser_,XML_OPTION_CASE_FOLDING,0);
		xml_set_element_handler($this->parser_,"startElement","endElement");

		$fp=fopen($file,"r");
		if(!$fp) {
			$this->msg_[]="Can't open file";
			return false;
		}	
		
		while($data=fread($fp,4096)) {
			if(!xml_parse($this->parser_,$data,feof($fp))) {
				$err_string = xml_error_string(xml_get_error_code($this->parser_));
				$line = xml_get_current_line_number($this->parser_);
			}
		}
		
		xml_parser_free($this->parser_);
		
		fclose($fp);

        return true;
	}

	function startElement($parser,$name,$attrs=null) {
		$method=strtolower($name)."OpenTagHandler";
		$method=str_replace("-","_",$method);
		if(method_exists($this,$method))
			$this->$method($attrs);
		else {
    		// Typo 期間への対処
    		$method=strtolower($name)."OepnTagHandler";
    		$method=str_replace("-","_",$method);
    		if(method_exists($this,$method))
    			$this->$method($attrs);
		}
	}

	function endElement($parser,$name,$attrs=null) {
		$method=strtolower($name)."CloseTagHandler";
		$method=str_replace("-","_",$method);
		if(method_exists($this,$method))
			$this->$method($attrs);
	}
}

?>
