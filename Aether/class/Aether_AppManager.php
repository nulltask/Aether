<?php

/**
 * @package Aether
 * @author Seiya Konno <seiya@uniba.jp>
 * @copyright 2010 Uniba Inc.,
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @link http://rd.uniba.jp/
 */

class Aether_AppManager extends Ethna_AppManager
{
	/** @var	string	モデルの名前 */
	var $name = null;
	var $object_cache = array();

	/**
	 * クラス名を基にモデル名をメンバに設定します。
	 * 
	 * @param Ethna_Backend $backend
	 */
	function __construct($backend)
	{
		parent::__construct($backend);
	
		$class_name = explode('_', get_class($this));
		$this->name = substr($class_name[1], 0, -(strlen('Manager')));	// HogeManager -> Hoge
	}
	
	/**
	 * 連想配列をもとに AppObject を生成し add() します。
	 * 
	 * @author Seiya Konno <seiya@uniba.jp>
	 * @access protected
	 * @param array $array
	 * @param bool $add
	 */
	function createObject($array = array(), $add = true)
	{
		if (is_null($this->name))
		{
			return Ethna::raiseError('AppManager::$name が設定されていません。');
		}
	
		$this->backend->log(LOG_DEBUG, var_export($array, true));
	
		$appObject = $this->backend->getObject($this->name);
		foreach ($array as $key => $value)
		{
			$appObject->set($key, $value);
		}
		if ($add)
		{
			$result = $appObject->add();
			return (Ethna::isError($result)) ? $result : $appObject;
		}
	
		return $appObject;
	}

	/**
	 * 連想配列をもとに AppObject を生成します。 (add() はしない)
	 * 
	 * @author Seiya Konno <seiya@uniba.jp>
	 * @access protected
	 * @param array $array
	 * @param bool $add
	 */
	function create($array = array(), $add = false)
	{
		return $this->createObject($array, $add);
	}
	
	/**
	 * トランザクションを開始します。
	 * 
	 * @author Seiya Konno <seiya@uniba.jp>
	 * @access public
	 * @return void
	 */
	function begin()
	{
		$this->backend->log(LOG_INFO, '<<<<<<<<<< begin transaction >>>>>>>>>>');
		$this->db->db->autoCommit(false);
		$this->db->begin();
	}
	
	/**
	 * トランザクションをコミットします。
	 * 
	 * @author Seiya Konno <seiya@uniba.jp>
	 * @access public
	 * @return void
	 */
	function commit()
	{
		$this->db->commit();
		$this->db->db->autoCommit(true);
		$this->backend->log(LOG_INFO, '<<<<<<<<<< commit transaction >>>>>>>>>>');
	}
	
	/**
	 * トランザクションをロールバックします。
	 * 
	 * @author Seiya Konno <seiya@uniba.jp>
	 * @access public
	 * @return void
	 */
	function rollback()
	{
		$this->db->rollback();
		$this->db->db->autoCommit(true);
		$this->backend->log(LOG_INFO, '<<<<<<<<<< rollback transaction >>>>>>>>>>');
	}
	
	/**
	 * AppObject の配列に対して、一律処理を行ないます。
	 * 
	 * @author Seiya Konno <seiya@uniba.jp>
	 * @access protected
	 * @param string $mehod_name
	 * @param array $object_list
	 * @param array $args
	 * @return array
	 */
	function map($object_list, $method_name, $args = array())
	{
		$result_list = array();
		
		foreach ($object_list as $object)
		{
			$ret = call_user_func_array(array($object, $method_name), $args);

			if (Ethna::isError($ret))
			{
				return $ret;
			}
			
			$result_list[] = $ret;
		}

		return $result_list;
	}
	
	function get($key, $value)
	{
		if (!isset($this->object_cache[$key][$value]))
		{
			$this->object_cache[$key][$value] = $this->backend->getObject($this->name, $key, $value);
		}
		
		return $this->object_cache[$key][$value];
	}
	
	/**
	 * AppObject の配列に対して getNameObject() を実行した結果リストを返します。
	 * 
	 * @author Seiya Konno <seiya@uniba.jp>
	 * @access public
	 * @param array $object_list
	 * @param string $get_name_object_method
	 * @return array
	 */
	function getNameObjectList($object_list, $get_name_object_method = 'getNameObject')
	{
		return $this->map($object_list, $get_name_object_method);
	}
	
	/**
	 * AppObject の配列に対して remove() を実行します。
	 * 
	 * @author Seiya Konno <seiya@uniba.jp>
	 * @access public
	 * @param array $object_list
	 * @return array
	 */
	function remove($object_list)
	{
		return $this->map($object_list, 'remove');
	}
	
	/**
	 * AppObject を確実にメモリから開放します。
	 * シンボルテーブルからも確実に開放したい場合は呼び出し元で unset() してください。
	 * 
	 * @author Seiya Konno <seiya@uniba.jp>
	 * @access public
	 * @param Ethna_AppObject $object
	 * @return void
	 */
	function free(Ethna_AppObject $object)
	{
		$object->_clearPropCache();
		$object = null;
	}
	
	function clearObjectCache()
	{
		foreach ($this->object_cache as $object)
		{
			$this->clear($object);
		}
		
		$this->object_cache = array();
	}
	
	function exists($filter)
	{
		$result = $this->getObjectPropList($this->name, null, $filter, null, 0, 0);
		if (Ethna::isError($result))
		{
			return $result;
		}
		return ($result[0] > 0);
	}
}
