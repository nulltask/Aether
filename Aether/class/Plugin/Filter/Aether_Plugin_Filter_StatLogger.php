<?php

/**
 * @package Aether
 * @author Seiya Konno <seiya@uniba.jp>
 * @copyright 2010 Uniba Inc.
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @link http://rd.uniba.jp/
 */

class Aether_Plugin_Filter_StatLogger extends Ethna_Plugin_Filter
{
	var $stime;
	var $action_name;
	var $forward_name;

	function preFilter()
	{
		$stime = explode(' ', microtime());
		$stime = $stime[1] + $stime[0];
		$this->stime = $stime;
		
		$this->logger->log(LOG_DEBUG, '**** stat preFilter: '
		. $this->_convertByteToKiloByte(memory_get_usage()));
	}

	function preActionFilter($action_name)
	{
		$this->action_name = $action_name;
		 
		$this->logger->log(LOG_DEBUG, '**** stat preActionFilter: '
		. $this->_convertByteToKiloByte(memory_get_usage()));
		 
		return null;
	}

	function postActionFilter($action_name, $forward_name)
	{
		$this->action_name = $action_name;
		$this->forward_name = strlen($forward_name > 0) ? $forward_name : '-';
		 
		$this->logger->log(LOG_DEBUG, '**** stat postActonFilter: '
		. $this->_convertByteToKiloByte(memory_get_usage()));
		 
		return null;
	}

	function postFilter()
	{
		$etime = explode(' ', microtime());
		$etime = $etime[1] + $etime[0];
		$time   = round(($etime - $this->stime), 4);
		
		$mem_peak = null;
		
		if (function_exists('memory_get_peak_usage'))
		{
			// only for PHP 5.3.x
			$mem_peak = $this->_convertByteToKiloByte(memory_get_peak_usage());
		}
		
		$this->logger->log(LOG_INFO,
		sprintf('**** stat postFilter: [%s] -> [%s]', $this->action_name, $this->forward_name)
		.  ' '
		. $this->_convertByteToKiloByte(memory_get_usage())
		// . (isset($mem_peak)) ? ', peak ' . $mem_peak : ''
		. ' (' . $time . 'sec)');
	}

	/**
	 * バイトをキロバイトに変換します。しかも単位付きで！
	 *
	 * @param integer $byte
	 * @return string
	 */
	private function _convertByteToKiloByte($byte)
	{
		return number_format(round($byte / 1024)) . 'KB';
	}
}
