<?php

/**
 * @package Aether
 * @author Seiya Konno <seiya@uniba.jp>
 * @copyright 2010 Uniba Inc.,
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @link http://rd.uniba.jp/
 */

class Aether_Controller extends Ethna_Controller
{
	/**
	 * ActionForm/Action/View を単一のファイルに収めるためのオーバーライド
	 * @author Seiya Konno <seiya@uniba.jp>
	 * @param $forward_name
	 */
	function getViewClassName($forward_name)
	{
		$this->_includeActionScript(null, $forward_name);
		$class_name = parent::getViewClassName($forward_name);
		
		return $class_name;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see class/Ethna_Controller#_getActionName_Form()
	 */
	function _getActionName_Form()
	{
		if (isset($_SERVER['REQUEST_METHOD']) == false) {
			return null;
		}

		if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') == 0) {
			$http_vars =& $_POST;
		} else {
			$http_vars =& $_GET;
		}

		foreach ($http_vars as $name => $value)
		{
			if ($value == "" || strncmp($name, 'action_', 7) != 0) {
				continue;
			}

			// オリジナル方式 http://hostname/?action_action_name
			return parent::_getActionName_Form();
		}

		// かっこいい http://hostname/action/name/ 方式
		if (!empty($_SERVER['REDIRECT_URL']))
		{
			$redirect_url = $_SERVER['REDIRECT_URL'];
			$action_name = str_replace('/', '_', $redirect_url);
			return trim($action_name, '_');
		}

		// まあ悪くはない http://hostname/?action=action_name 方式
		if (array_key_exists('action', $http_vars))
		{
			return $http_vars['action'];
		}
	}
	
	/*
	function _createFilterChain()
	{
		$this->filter_chain = array();
		foreach ($this->filter as $filter => $gateway)
		{
			if (isset($gateway[$this->getGateway()]))
			{
				$this->_isAcceptableActionName();
			}
			
			$filter_plugin =& $this->plugin->getPlugin('Filter', $filter);
			if (Ethna::isError($filter_plugin))
			{
				continue;
			}
			
			$this->filter_chain[] =& $filter_plugin;
		}
	}
	*/
}