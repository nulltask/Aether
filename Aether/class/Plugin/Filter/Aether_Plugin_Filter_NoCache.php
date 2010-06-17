<?php

/**
 * @package Aether
 * @author Uniba Inc. <rd@uniba.jp>
 * @copyright Uniba Inc.,
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @link http://rd.uniba.jp/
 */

class Aether_Plugin_Filter_NoCache extends Ethna_Plugin_Filter
{
    function preFilter()
    {
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s \G\M\T"));
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }
}
