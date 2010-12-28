<?php

/**
 * @package Aether
 * @author Seiya Konno <seiya@uniba.jp>
 * @copyright 2010 Uniba Inc.,
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @link http://rd.uniba.jp/
 */

class Aether_Plugin_Filter_MemoryUsageLogger extends Ethna_Plugin_Filter
{
	var $action_name;
	var $forward_name;
	
    function preFilter()
    {
    	$this->logger->log(LOG_DEBUG, 'Memory usage (preFilter) : '
    		. $this->_convertByteToKiloByte(memory_get_usage()));
    }

    function preActionFilter($action_name)
    {
    	$this->action_name = $action_name;
    	
    	$this->logger->log(LOG_DEBUG, 'Memory usage (preActionFilter) : '
    		. $this->_convertByteToKiloByte(memory_get_usage()));
    	
        return null;
    }

    function postActionFilter($action_name, $forward_name)
    {
    	$this->action_name = $action_name;
    	$this->forward_name = strlen($forward_name > 0) ? $forward_name : '-';
    	
    	$this->logger->log(LOG_DEBUG, 'Memory usage (postActonFilter) : '
    		. $this->_convertByteToKiloByte(memory_get_usage()));
    	
        return null;
    }

    function postFilter()
    {
    	$this->logger->log(LOG_INFO,
    		sprintf('[%s] -> [%s]', $this->action_name, $this->forward_name)
    		.  ' Memory usage (postFilter) : '
    		. $this->_convertByteToKiloByte(memory_get_usage()));
    	
    	if (function_exists('memory_get_peak_usage'))
    	{
    		// only for PHP 5.3.x
    		$this->logger->log(LOG_INFO, 'Memory usage (peak) : '
    			. $this->_convertByteToKiloByte(memory_get_peak_usage()));
    	}
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
