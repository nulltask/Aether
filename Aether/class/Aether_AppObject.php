<?php

/**
 * @package Aether
 * @author Seiya Konno <seiya@uniba.jp>
 * @copyright 2010 Uniba Inc.,
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @link http://rd.uniba.jp/
 */

class Aether_AppObject extends Ethna_AppObject
{
	/** @var array リレーションを定義 */
	var $relation = array();

	/** @var array insert, update 日時自動挿入用定義 */
	var $column_name = array(
		'add'	=> array('created', 'created_at', 'registered_date', 'registered', 'entry_date',),    // レコード登録日時
		'update'	=> array('updated', 'updated_at', 'modified_date',  'modified', 'update_date',),        // レコード更新日時
	);

	/**
	 * (non-PHPdoc)
	 * @see class/Ethna_AppObject#set()
	 */
	function set($key, $value, $update = false)
	{
		if ($update)
		{
			$this->set($key, $value);
			return $this->update();
		}
		return parent::set($key, $value);
	}

	/**
	 * (non-PHPdoc)
	 * @see class/Ethna_AppObject#add()
	 */
	function add()
	{
		$prop_name = $this->_searchColumnName('add');

		if ($prop_name)
		{
			$this->set($prop_name, date('Y-m-d H:i:s'));    // レコードの作成日をセット
		}

		return parent::add();
	}

	/**
	 * (non-PHPdoc)
	 * @see class/Ethna_AppObject#update()
	 */
	function update()
	{
		$prop_name = $this->_searchColumnName('update');

		if ($prop_name)
		{
			$this->set($prop_name, date('Y-m-d H:i:s'));    // レコードの更新日をアップデート
		}

		return parent::update();
	}

	/**
	 * 自動設定のカラム名を取得します。
	 * 
	 * @author Seiya Konno <seiya@uniba.jp>
	 * @access private
	 * @param string $type
	 * @return string
	 */
	function _searchColumnName($type)
	{
		foreach ($this->column_name[$type] as $col)
		{
			if (array_key_exists($col, $this->prop_def))
			{
				return $col;
			}
		}

		return false;
	}
	
	/**
	 * 連想配列をもとにプロパティに値を設定します。
	 * 
	 * @author Seiya Konno <seiya@uniba.jp>
	 * @access public
	 * @param $array
	 * @param $update
	 * @return mixed
	 */
	function setArray($array, $update = false)
	{
		foreach ($array as $key => $value)
		{
			$this->set($key, $value);
		}

		if ($update)
		{
			return $this->update();
		}
	}

	/**
	 * リレーション定義をもとに関連する AppObject を取得します。
	 * 
	 * @author Seiya Konno <seiya@uniba.jp>
	 * @access protected
	 * @param string $model_name
	 * @param string $prop_name
	 * @return Ethna_AppObject
	 */
	function getRelatedObject($model_name, $prop_name = null)
	{
		if (!array_key_exists($model_name, $this->relation))
		{
			return null;
		}

		if (is_null($prop_name))
		{
			// 省略時先頭
			$pair = array_slice($this->relation, 0, 1);
			list ($prop_name, $referecnce) = $pair;
			//foreach ($pair as $key => $value) { $prop_name = $key; $reference = $value; }
		}
		else if (isset($this->relation[$model_name][$prop_name]))
		{
			$reference = $this->relation[$model_name][$prop_name];
		}
		else
		{
			trigger_error('missing property name.');
			return;
		}

		return $this->backend->getObject($model_name, $reference, $this->get($prop_name));
	}
	
	/**
	 * @link http://www.bpsinc.jp/blog/archives/223
	 */
    function _getSQL_Update()
    {
        $tables = implode(',',
            $this->my_db_rw->quoteIdentifier(array_keys($this->table_def)));

        // SET句構築
        $set_list = "";
        $prop_arg_list = $this->prop;
        Ethna_AppSQL::escapeSQL($prop_arg_list, $this->my_db_type);
        foreach ($this->prop_def as $k => $v) {
        	if (isset($prop_arg_list[$k]) && $prop_arg_list[$k] !== null && $prop_arg_list[$k] !== '') {
	            if ($set_list != "") {
	                $set_list .= ",";
	            }
	            $set_list .= sprintf("%s=%s",
	                                 $this->my_db_rw->quoteIdentifier($k),
	                                 $prop_arg_list[$k]);
        	}
        }

        // 検索条件(primary key)
        $condition = null;
        foreach (to_array($this->id_def) as $k) {
            if (is_null($condition)) {
                $condition = "WHERE ";
            } else {
                $condition .= " AND ";
            }
            $v = $this->prop_backup[$k];    // equals to $this->id
            Ethna_AppSQL::escapeSQL($v, $this->my_db_type);
            $condition .= Ethna_AppSQL::getCondition(
                $this->my_db_rw->quoteIdentifier($k), $v);
        }

        $sql = "UPDATE $tables SET $set_list $condition";

        return $sql;
    }
}
