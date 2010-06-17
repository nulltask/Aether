<?php

/**
 * @package Aether
 * @author Seiya Konno <seiya@uniba.jp>
 * @copyright 2010 Uniba Inc.,
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @link http://rd.uniba.jp/
 */

require_once 'DB.php';

class Aether_Plugin_Filter_CheckMySQL extends Ethna_Plugin_Filter
{
    function preFilter()
    {
    	$db = DB::connect($this->config->get('dsn'));
    	
    	if (DB::isError($db))
    	{
    		// データベースが落ちている時の処理
			$this->logger->log(LOG_INFO, 'WARNING: DB connection failed.');
    		// return 'maintenance';
    		// return Ethna::raiseError('DB connection failed.');
    	}
    }
}
