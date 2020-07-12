<?php
/**
@brief XoopsObject & Handler の継承/互換 class を定義しているファイル(元 class.object.php)
@version $Id: object.php,v 1.8 2005/05/19 08:19:20 minahito Exp $
*/

class exTableInfomation {
	var $tablenames_;
	var $primarykeys_;
	var $fields_;
	var $extras_;
	var $pk_num_;
	var $merge_;	/**< 表結合条件 */

	function exTableInfomation ( $tbl, $pk, $merge=null, $exts=null, $fields="*" ) {
		if(is_array($tbl))
			$this->tablenames_=&$tbl;
		else {
			$this->tablenames_=array();
			$this->tablenames_[]=&$tbl;
		}

		if(is_array($pk))
			$this->primarykeys_=&$pk;
		else {
			$this->primarykeys_=array();
			$this->primarykeys_[]=&$pk;
		}

		if($merge!==null)
			$this->merge_=$merge;

		if($fields!==null) {
			if(is_array($fields)) {
				$this->fields_=&$fields;
			}
			else {
				$this->fields_=array();
				$this->fields_[]=&$fields;
			}
		}

		if($exts!==null) {
			if(is_array($exts)) {
				$this->extras_=&$exts;
			}
			else {
				$this->extras_=array();
				$this->extras_[]=&$exts;
			}
		}

		$this->pk_num_=count($this->primarykeys_);
	}
}


/**
@brief サニタイズ・妥当性チェックのクラスメソッドを持った XoopsObject 継承クラス
*/
class exXoopsObject extends XoopsObject {
	/**
	@brief プロパティを連想配列に格納して返す
	@return array
	*/
	function &getArray($type='s') {
		$ret=array();
		foreach (array_keys($this->vars) as $key ) {
			if($type)
				$ret[$key]=$this->getVar($key,$type);
			else
				$ret[$key]=$this->getVar($key,$type);
		}
		return $ret;
	}

	/**
	@brief データを構造的に返すことを期待されるメソッド
	@return array
	@note 継承オブジェクトでは、外部キーの入っているフィールドを、そのデータの連想配列
	\par で置き換える動作を実装しなければいけない。
	\par つまり、ここで取得した配列でリレーショナルデーターベースの１レコードが把握でき
	\par なければいけない。・・・というように実装する。
	*/
	function &getStructure($type='s') {
		$ret =& $this->getArray($type);
		return $ret;
	}

	/**
	@brief 連想配列を渡して値をセットするメソッド。XoopsObject に比べ stripSlashesGPC 相当の処理が追加されている。
	@note コンストラクタで cleanVars() を実行してもよいのかもしれない。 $strip を false にすればオリジナルと同じ挙動になるので使い分けを。
	@note 8/6 オリジナルと同じ動作になるよう $strip = false を default に
	@param $var_arr
	@param $strip bool
	*/
	function assignVars($var_arr,$strip=false)
	{
		foreach($var_arr as $key=>$value) {
			if(get_magic_quotes_gpc() && $strip) {
				$value=&stripslashes($value);
				$this->assignVar($key,$value);
			}
			else {
				$this->assignVar($key,$value);
			}
		}
		
		// $this->cleanVars();
	}
	
	/**
	@brief 内部的に stripSlashesGPC 相当の処理を行うための暫定的なヘルパ（ただし参照による書換ではなく変換）
	@param $value
	@return magic_quote が ON の場合 stripslashes した値を返す
	*/
	function _stripSlashesGPC($value)
	{
		return get_magic_quotes_gpc() ? stripslashes($value) : $value;
	}

	/**
	@brief オリジナルと異なり stripSlashesGPC 処理をほぼ強制で行う挙動を行う setVar (thx for ryuji!)
	*/
	function setVar($key,$value,$not_gpc=false)
	{
		if(!empty($key) && isset($value) && isset($this->vars[$key])) {
			$this->vars[$key]['value'] = $not_gpc ? $value : $this->_stripSlashesGPC($value);
			$this->vars[$key]['not_gpc'] = true;	// 常に true
			$this->vars[$key]['changed'] = true;
			$this->setDirty();
		}
	}
}

/**
@brief exXoopsObjectHandler を別の切り口で実装するためのクラス
@note 移植が終り次第格上げ。
*/
class exXoopsObjectHandler extends XoopsObjectHandler {
	var $db_;
	var $_classname_=null;
	var $_tableinfo_=null;

	function exXoopsObjectHandler($db,$classname=null) {
		$this->db_=&$db;

		if($this->_classname_==null)
			$this->_classname_=$classname;
		if($this->_tableinfo_==null)
			$this->_tableinfo_=&call_user_func(array($classname,'getTableInfo'));
	}

	/**
	@brief インスタンスをキャッシュする
	FIXME:: null がとんできたときの担保（対 getObjects としてはこんなもん）
	*/
	function _cacheInstance($obj) {
		global $__exobject__cache__;
		if(is_object($obj)){
			$classname=get_class($obj);
			$pk_count=count($this->_tableinfo_->primarykeys_);
			if($pk_count==1)
				$__exobject__cache__[$classname][$obj->getVar($this->_tableinfo_->primarykeys_[0])]=&$obj;
			else {
				// プライマリが複数の場合の処理
				// FIXME: 厳密なデバグを
				if(!isset($__exobject_cache__[$classname]))
					$__exobject_cache__[$classname]=array();
				elseif(!is_array($__exobject_cache__[$classname]))
					$__exobject_cache__[$classname]=array();

				$p =& $__exobject__cache__[$classname];
				for($i=0;$i<$pk_count;$i++) {
					$keyname=$obj->getVar($this->_tableinfo_->primarykeys_[$i]);
					if($i==($pk_count-1)) {	// 最後の要素か？
						$p[$keyname]=&$obj;
					}
					else {
						if(!isset($p[$keyname]))
							$p[$keyname]=array();
						$p=&$p[$keyname];
					}
				}
			}
		}
/*		exFrame::debug("クラス名:".$classname);
		exFrame::debug("プライマリキーカウント:".$pk_count);
		exFrame::debug("渡されたプライマリキー:".$this->_tableinfo_->primarykeys_[0]);
		exFrame::debug($obj->getVar($this->_tableinfo_->primarykeys_[0]));
		exFrame::debug($__exobject__cache__);*/
	}

	function _resetCacheAll() {
		global $__exobject__cache__;
		$__exobject__cache__=array();
	}
	
	function &create($isNew=true) {
		$obj=null;
		if(class_exists($this->_classname_)) {
			$obj=new $this->_classname_();
			if($isNew)
				$obj->setNew();
		}
		return $obj;
	}
	
	function &get() {
		global $__exobject__cache__;

		// エラー処理
		if($this->_tableinfo_->pk_num_!=func_num_args())
			return null;

		// プライマリーキーがひとつのときの処理
		if($this->_tableinfo_->pk_num_==1) {
			$value=func_get_arg(0);
//			exFrame::debug( "与えられた".$value);
			if(isset($__exobject__cache__[$this->_classname_][$value])) {
				return $__exobject__cache__[$this->_classname_][$value];
			}
			else {
				$criteria=new Criteria($this->_tableinfo_->primarykeys_[0],
					$value, "=");
				$obj=&$this->_get($this->_tableinfo_,$criteria);
				$this->_cacheInstance($obj);
				return $obj;
			}
		}
		else {	// ふたつ以上の場合
			if(isset($__exobject__cache__[$this->_classname_])) {
				if(is_array($__exobject__cache__[$this->_classname_])) {
					$p=&$__exobject__cache__[$this->_classname_];
					for($i=0;$i<$this->_tableinfo_->pk_num_;$i++) {
        				if($i==($this->_tableinfo_->pk_num_ - 1)) {
        					if(isset($p[func_get_arg($i)])) {
        						return $p[func_get_arg($i)];
        					}
        				}
        				else {
        					if(is_array($p[func_get_arg($i)]))
        						$p=&$p[func_get_arg($i)];
        					else
        						break;
        				}
        			}
				}
			}

			$criteria = new CriteriaCompo();
			for($i=0;$i<$this->_tableinfo_->pk_num_;$i++) {
				$val=func_get_arg($i);
				$criteria->add(new Criteria($this->_tableinfo_->primarykeys_[$i],$val));
			}
			$obj=&$this->_get($this->_tableinfo_,$criteria);
			$this->_cacheInstance($obj);
			return $obj;
		}
	}

	function &_get($tinfo,$criteria) {
		$sql="SELECT ".implode(",",$tinfo->fields_)." FROM ".implode(",",array_map(array($this->db_,'prefix'),$tinfo->tablenames_));
		if($tinfo->merge_)
			$sql.=" WHERE ".$tinfo->merge_." AND ".$criteria->render();
		else
			$sql.=" ".$criteria->renderWhere();

//		exFrame::debug($sql);

		if(!$result=$this->db_->query($sql))
			return false;

		if($this->db_->getRowsNum($result)==1) {
			if($row=$this->db_->fetchArray($result)) {
				$obj = new $this->_classname_();
				$obj->assignVars($row);
				$obj->unsetNew();
				return $obj;
			}
		}

		return null;
	}

	function &getObjects($criteria=null,$id_as_key='',$order=null) {
		global $__exobject__cache__;
		
		$objects =& $this->_getObjects($this->_tableinfo_,$criteria,$order);
		foreach($objects as $o) {
			$this->_cacheInstance($o);
		}
		
		// キャッシュを渡せば id_as_key となる?
		if($id_as_key)
			return $__exobject__cache__[$this->classname_];
		else
			return $objects;
	}

	/**
	@param $force true のとき、取得できるものが１つでなくても先頭を返す
	*/
	function &getOne($criteria,$force=false) {
		$objs=&$this->getObjects($criteria);
		if(count($objs)==1 or ($force && count($objs)))
			if(is_object($objs[0])) return $objs[0];

		return false;
	}

	function &getCount($criteria=null) {
		global $__exobject__cache__;
		
		return $this->_getCount($this->_tableinfo_,$criteria);
	}

	function &_getObjects($tinfo,$criteria,$order) {
		$ret=array();
		$whereflag=false;

		$limit = $start = 0;

		$sql="SELECT ".implode(",",$tinfo->fields_)." FROM ".implode(",",array_map(array($this->db_,'prefix'),$tinfo->tablenames_));
		if($tinfo->merge_) {
			$sql.=" WHERE ".$tinfo->merge_;
			$whereflag=true;
		}

		if(isset($criteria) && is_subclass_of($criteria,'criteriaelement')) {
			if($whereflag)
				$sql.=" AND ".$criteria->render();
			else
				$sql.=' '.$criteria->renderWhere();
			
			if($criteria->getSort()!='')
				$sql.=' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
			elseif($order!=null)
				$sql.=' ORDER BY '.$order;

			$limit=$criteria->getLimit();
			$start=$criteria->getStart();
		}
		elseif($order!=null)
			$sql.=' ORDER BY '.$order;

		$result=$this->db_->query($sql,$limit,$start);
		if(!$result){ return $ret; }

		while($row=$this->db_->fetchArray($result)) {
			$obj = new $this->_classname_();
			$obj->assignVars($row);
			$obj->unsetNew();
			$ret[]=&$obj;
			unset($obj);
		}

		return $ret;
	}

	function &_getCount($tinfo,$criteria=null) {
		$whereflag=false;

		$sql="SELECT COUNT(*) c FROM ".implode(",",array_map(array($this->db_,'prefix'),$tinfo->tablenames_));
		if($tinfo->merge_) {
			$sql.=" WHERE ".$tinfo->merge_;
			$whereflag=true;
		}

		if(isset($criteria) && is_subclass_of($criteria,'criteriaelement')) {
			if($whereflag)
				$sql.=" AND ".$criteria->render();
			else
				$sql.=" ".$criteria->renderWhere();
		}

		$result=$this->db_->query($sql);
		if(!$result){ return false; }

		$ret=$this->db_->fetchArray($result);
		
		return $ret['c'];
	}

	function insert(&$obj,$force=false) {
		return $this->_insert($this->_tableinfo_,$obj,$force);
	}
	
	function _insert(&$tinfo,&$obj,$force=false) {
		if(strtolower(get_class($obj))!=strtolower($this->_classname_))
			return false;

		if(!$obj->isDirty()) return true;
		if(!$obj->cleanVars()) return true;

		foreach($obj->cleanVars as $key => $value) {
			$vars{$key} = $value;
		}
		
		$new_flag=false;
		
		if($obj->isNew()) {
			$new_flag=true;
			$sql = $this->_insert_new($tinfo,$obj,$vars);
		}
		else {
			$sql = $this->_insert_update($tinfo,$obj,$vars);
		}
		
		if($force!=false) {
			$result=$this->db_->queryF($sql);
		}
		else {
			$result=$this->db_->query($sql);
		}

		if(defined("__EXFRAME__DEBUG__"))
			print $sql;

		if(!$result){
			return false;
		}
		
		// PK がひとつの場合に限る
		if($new_flag && $tinfo->pk_num_>0 ) {
			$id = $this->db_->getInsertId();
			$obj->setVar($tinfo->primarykeys_[0],$id);
		}

		return true;
	}

	function _insert_new(&$tinfo,&$obj,&$vars) {
		$fileds=array();
		$values=array();
		$arr = $this->_makeVars4sql($obj,$vars);
		foreach(array_keys($arr) as $name) {
			$fields[]=$name;
			$values[]=$arr[$name];
		}
		$q=sprintf("INSERT INTO %s ( %s ) VALUES ( %s )",
			$this->db_->prefix($tinfo->tablenames_[0]),
			implode(",",$fields),
			implode(",",$values));

		return $q;
	}

	function _insert_update(&$tinfo,&$obj,&$vars) {
		$set_lists=array();
		$pk_lists=array();
		$values=array();
		$arr = $this->_makeVars4sql($obj,$vars);
		foreach(array_keys($arr) as $name) {
			if(in_array($name,$tinfo->primarykeys_)) {
				$pk_lists[]=$name."=".$arr[$name];
			}
			else {
				$set_lists[]=$name."=".$arr{$name};
			}
		}
		$q=sprintf("UPDATE %s SET %s WHERE %s",
			$this->db_->prefix($tinfo->tablenames_[0]),
			implode(",",$set_lists),
			implode(" AND ",$pk_lists));

		return $q;
	}

	/**
	@brief SQL 文生成ヘルパ。クオーテーションが必要なプロパティに対して quoteString() をかけます。
	@param $obj xoopsObject。プロパティの調査のために渡します。
	@param $vars Array プロパティの名前に値を入れた連想配列
	@return Array と同じハッシュで、必要なものに quoteString() をかけた連想配列を返します。
	@note _insert_new() _insert_update() などの SQL 生成メソッドから呼び出されるヘルパ
	*/
	function &_makeVars4sql(&$obj,&$vars) {
		$ret=array();
		foreach(array_keys($obj->vars) as $v) {
			switch($obj->vars[$v]['data_type']) {
				case XOBJ_DTYPE_TXTBOX:
				case XOBJ_DTYPE_TXTAREA:
				case XOBJ_DTYPE_URL:
				case XOBJ_DTYPE_EMAIL:
				case XOBJ_DTYPE_ARRAY:
				case XOBJ_DTYPE_OTHER:
				case XOBJ_DTYPE_SOURCE:
					$ret[$v]=$this->db_->quoteString($vars[$v]);
					break;
				default:
					$ret[$v]=$vars[$v];
			}
		}
		return $ret;
	}

	/**
	@brief 渡されたオブジェクトの削除
	@note キャッシュのピンポイントクリアが本来は必要
	*/
	function delete(&$obj,$force=false) {
		// criteria の生成
		$criteria = new CriteriaCompo();
		foreach($this->_tableinfo_->primarykeys_ as $pk) {
			$criteria->add(new Criteria($pk,$obj->getVar($pk)));
		}
		return $this->deletes($criteria,$force);
	}
	
	/**
	@brief Criteria を使用した削除
	@param $criteria
	*/
	function deletes($criteria,$force=false) {
		$sql = "DELETE FROM ".$this->db_->prefix($this->_tableinfo_->tablenames_[0])." ".$criteria->renderWhere();

		if($force)
			return $this->db_->queryF($sql);
		else
			return $this->db_->query($sql);
	}

} 


?>
