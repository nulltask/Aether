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
			return Ethna::raiseError('AppManager の model_name が設定されていません。');
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
		foreach ($object_list as $object)
		{
			$ret = call_user_func_array(array($object, $method_name), $args);
				
			if (Ethna::isError($ret))
			{
				return $ret;
			}
		}
	
		return $object_list;
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
}
