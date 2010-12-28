<?php

require_once 'Ethna/class/Plugin/Generator/Ethna_Plugin_Generator_Action.php';

class Aether_Plugin_Generator_Action extends Ethna_Plugin_Generator_Action
{
	var $action_name;
	var $skelton;
	var $gateway;
	
	function generate($action_name, $skelton = null, $gateway = GATEWAY_WWW)
	{
		$this->action_name = $action_name;
		$this->skelton = $skelton;
		$this->gateway = $gateway;
		
		return parent::generate($action_name, $skelton = null, $gateway = GATEWAY_WWW);
	}
	
	function _getUserMacro()
	{
		$view_class = $this->ctl->getDefaultViewClass($this->action_name, $this->gateway);
		$appid = $this->ctl->getAppId();
		return array('appid' => $appid, 'view_class' => $view_class);
	}
}
