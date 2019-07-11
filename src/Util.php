<?php
namespace HeadCouch;

class Util
{
    public static function normalize($value)
    {
        if ($value === true)
        {
            $value = "true";
        } elseif ($value === false) {
            $value = "false";
        }

        return $value;
    }
}