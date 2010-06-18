<?php

require_once 'Ethna/class/DB/Ethna_DB_PEAR.php';

class Aether_DB_PEAR extends Ethna_DB_PEAR
{
	/**
	 * テーブル名が SQL の予約語だった場合、
	 * AppObject の prop_def 自動取得などが失敗する仕様を変更。
	 * 
	 * @author	Seiya Konno <seiya@uniba.jp>
	 * @access public
	 * @see class/DB/Ethna_DB_PEAR#getMetaData()
	 */
	function getMetaData($table)
	{
		return parent::getMetaData($this->db->quoteIdentifier($table));
	}
}
