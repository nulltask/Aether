<?php

/**
 * @package Aether
 * @author Seiya Konno <seiya@uniba.jp>
 * @copyright 2010 Uniba Inc.,
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @link http://rd.uniba.jp/
 */

class Aether_ActionForm extends Ethna_ActionForm
{
	/**
	 * 未設定のフォーム値にデフォルト値を埋める
	 * 
	 * @author Seiya Konno <seiya@uniba.jp>
	 * @access public
	 * @return void
	 */
	function fillDefault()
	{
		foreach ($this->form as $name => $def)
		{
			$val = $this->get($name);

			if (isset($def['default']) && is_null($val))
			{
				$this->set($name, $def['default']);
			}
		}
	}

	/**
	 * AppObject の表示名をアプリケーション値に設定し、
	 * プロパティをエスケープなしのアプリケーション値に設定します
	 * 
	 * @author Seiya Konno <seiya@uniba.jp>
	 * @access public
	 * @param string $prop
	 * @param Ethna_AppObject $obj
	 * @return void
	 */
	function setAppObject($prop, Ethna_AppObject $obj)
	{
		$this->setApp($prop, $obj->getNameObject());
		$this->setAppNE($prop, $obj->prop);
	}
}
