<?php

/**
 * @package Aether
 * @author Seiya Konno <seiya@uniba.jp>
 * @copyright 2010 Uniba Inc.,
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @link http://rd.uniba.jp/
 */

class Aether_Plugin_Filter_ExecutionTime extends Ethna_Plugin_Filter
{
    var $stime;
    var $action_name = '-';
	var $forward_name = '-';
    
    function preFilter()
    {
        $stime = explode(' ', microtime());
        $stime = $stime[1] + $stime[0];
        $this->stime = $stime;
    }

    function preActionFilter($action_name)
    {
    	$this->action_name = $action_name;
    	
        return null;
    }

    function postActionFilter($action_name, $forward_name)
    {
    	$this->action_name = $action_name;
    	$this->forward_name = $forward_name;
    	
        return null;
    }

    function postFilter()
    {
        $etime = explode(' ', microtime());
        $etime = $etime[1] + $etime[0];
        $time   = round(($etime - $this->stime), 4);

    	$this->logger->log(LOG_INFO,
    		sprintf('{%s, %s}', $this->action_name, $this->forward_name)
    		. " page was processed in $time seconds");
    }
}

