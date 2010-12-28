<?php

require_once ETHNA_BASE . '/class/Ethna_Config.php';

class Aether_Config extends Ethna_Config
{
	var $environment = null;
	
	function __construct($controller)
	{
		parent::__construct($controller);
		
		$this->_setEnvironment();
	}
	
	function get($key = null)
	{
		$env = $this->environment;
		
		if (is_null($key))
		{
			$r = $this->config;
			
			if (isset($this->config['environment'][$env]))
			{
				$r = $this->array_merge_recursive_overwrite($r, $this->config['environment'][$env]);
			}
			
			return $r;
        }
        
		if (isset($this->config['environment'][$env][$key]))
		{
			if (is_array($this->config[$key]))
			{
				return $this->array_merge_recursive_overwrite($this->config[$key], $this->config['environment'][$env][$key]);
			}
			
			return $this->config['environment'][$env][$key];
		}
		
		return parent::get($key);
	}
	
	function set($key, $value)
	{
		$this->config['environment'][$this->environment][$key] = $value;
	}
	
	function _setEnvironment()
	{
		$this->environment = AETHER_ENVIRONMENT_DEVELOPMENT;
	}
	
	function array_merge_recursive_overwrite($a, $b)
	{
		if (!$a)
		{
			return $b;
		}
		else if (!$b)
		{
			return $a;
		}
		
		foreach($a as $ak => $av)
		{
			
			foreach($b as $bk => $bv)
			{
				
				if	($ak == $bk)
				{
				
					if(!empty($av) && is_array($av))
					{
						$a[$ak] = $this->array_merge_recursive_overwrite($a[$ak], $b[$bk]);
				
					}
					else
					{
						$a[$ak] = $b[$bk];
					}
				
				}
				else
				{
					$a += $b;
				}
			}
		}
		
		return $a;
	
	}
}