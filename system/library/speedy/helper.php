<?php
namespace Speedy;

class Helper
{
    static function get($arr, $key, $return = '')
    {
        if (isset($arr[$key]) && $arr[$key] !== 0) {
            return $arr[$key];
        } else {
            return $return;
        }
    }
}