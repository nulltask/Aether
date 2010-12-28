<?php

class Aether_ViewClass extends Ethna_ViewClass
{
	/**
     *  HTMLタグを取得する (textarea で値が何もない場合にバグっているのを治す)
     *
     *  @access protected
     */
    function _getFormInput_Html($tag, $attr, $element = null, $escape_element = true)
    {
        // 不要なパラメータは消す
        foreach ($this->helper_parameter_keys as $key) {
            unset($attr[$key]);
        }

        $r = "<$tag";

        foreach ($attr as $key => $value) {
            if ($value === null) {
                $r .= sprintf(' %s', $key);
            } else {
                $r .= sprintf(' %s="%s"', $key, htmlspecialchars($value, ENT_QUOTES));
            }
        }

        if ($element === null && $tag != 'textarea') {
            $r .= ' />';
        } else if ($element === null && $tag == 'textarea') {
        	$r .= "></$tag>";
        } else if ($escape_element) {
            $r .= sprintf('>%s</%s>', htmlspecialchars($element, ENT_QUOTES), $tag);
        } else {
            $r .= sprintf('>%s</%s>', $element, $tag);
        }

        return $r;
    }
}