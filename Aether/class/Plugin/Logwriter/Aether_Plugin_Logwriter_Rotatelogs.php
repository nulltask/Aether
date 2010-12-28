<?php

/**
 * 
 * @package Aether
 * @author Seiya Konno <seiya@uniba.jp>
 * @copyright 2010 Uniba Inc.
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @link http://rd.uniba.jp/
 * 
 * 
 * 'log' => array(
 *      'rotatelogs'  => array(
 *      	'cmd'			=> '/usr/sbin/rotatelogs',
 *      	'cmd_option'	=> '86400',
 *          'level'         => 'debug',
 *          'file'          => '/var/log/appid/app.log.%Y%m%d',
 *          'mode'          => 0666,
 *      	'option'		=> 'pid,function,pos',
 *      	'filter_ignore'	=> '^plugin file is found in search|^default action|^default view',
 *      ),
 *  ),
 */

require_once 'Ethna/class/Plugin/Logwriter/Ethna_Plugin_Logwriter_File.php';

class Aether_Plugin_Logwriter_Rotatelogs extends Ethna_Plugin_Logwriter_File
{
	var $pp = null;
	
	function begin()
	{
		$this->pp = popen($this->option['cmd'] . ' -l ' . $this->option['file'] . ' ' . $this->option['cmd_option'], 'w');
		$this->fp = fopen('/dev/null', 'w');
	}
	
    function log($level, $message)
    {
    	$args = func_get_args();
        $message = call_user_func_array(array($this, 'parent::log'), $args);
        fwrite($this->pp, $message . "\n");
        return $message;
    }
	
	function end()
	{
		if ($this->fp)
		{
			fclose($this->fp);
			$this->fp = null;
		}
		if ($this->pp)
		{
			pclose($this->pp);
			$this->pp = null;
		}
	}
}