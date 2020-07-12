<?php
/**
@author minahito
@version $Id$
@note BSD Licence

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

require_once "exForm/Form.php";

define ( "COMPONENT_INIT_FAIL", "__error__component_init_fail" );
define ( "COMPONENT_INIT_SUCCESS", "__component_init_success__" );

/**
@brief 初期化と表示の２つのメソッドを中心的に用いるコンポーネントモデル
*/
class exComponent extends exAbstractForm {
	var $name_;
	var $msg_;
	var $processor_=null;
	var $render_=null;
	var $form_=null;
	
	function exComponent($processor=null,$render=null,$name=null,$form=null) {
		$this->msg_=array();
		if(is_object($processor))
			$this->processor_=$processor;

		if(is_object($render))
			$this->render_=$render;

		if($name!==null)
			$this->name_=$name;

		if($form!==null)
			$this->form_=&$form;
	}

	function doProcess() {
		if(is_object($this->processor_)) {
			return $this->processor_->process($this);
		}
		else {
			return $this->_process($this);
		}
	}

	function fetchHtml() {
		return $this->processor_->fethHtml($this);
	}

	function display() {
		return $this->processor_->display($this);
	}

}

class exForwardComponent extends exComponent {
	var $forwards_=array();
	
	function setForwards($forwards) {
		if($forwards!==null) {
    		if(is_array($forwards)) {
    			foreach($forwards as $f) {
    				$this->forwards_[$f->name_]=$f;
    			}
    		}    			
    		else
    			$this->forwards_[$forwards->name_]=$forwards;
		}
	}
}

/**
@brief コンポーネントの動作内容を決めるビジネスロジックを書き込む class
@note すべてのメソッドに component の引き渡しがあります。
*/
class exComponentProcessor {
	function process (&$component) {}

	function fethHtml(&$component) {
		$component->render_->init($component);
		return $component->render_->render();
	}
	
	function display(&$component) {
		$component->render_->init($component);
		$ret = $component->render_->render();
		print $ret;
		return $ret;
	}
}

/**
@brief コンポーネントの display 処理を書き込む class
@note 主に、プログラムで HTML コードを吐き出す場合に使用します。
*/
class exComponentRender {
	var $component_;
	
	function init(&$component) {
		$this->component_=&$component;
	}
	
	function render() { }
}


define ( "COMPONENT_MODEL_INIT_FAIL", 0 );
define ( "COMPONENT_MODEL_INIT_SUCCESS", 1 );

/**
@brief コンポーネントのためのモデル（主にデータモデル）
*/
class exComponentModel extends exAbstractForm {
	var $component_;

	function exComponentModel($component=null) {
		$this->component_=$component;
	}

	function doPost($data) {
		$this->actionPerform($data);
	}

	function doGet($data) {
		$this->actionPerform($data);
	}
	
	function actionPerform($data) {
	}

}


?>