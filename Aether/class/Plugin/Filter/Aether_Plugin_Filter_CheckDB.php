<?php

/**
 * @package Aether
 * @author Seiya Konno <seiya@uniba.jp>
 * @copyright 2010 Uniba Inc.,
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @link http://rd.uniba.jp/
 */

require_once 'DB.php';

class Aether_Plugin_Filter_CheckDB extends Ethna_Plugin_Filter
{
	function preFilter()
	{
		$db_list = $this->_getDBList();
		
		if (Ethna::isError($db_list) || is_null($db_list))
		{
			$this->logger->log(LOG_INFO, 'WARNING: DB connection failed.');
			// return 'maintenance';
		}
	}
}
