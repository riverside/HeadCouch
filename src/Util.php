<?php

namespace HeadCouch;
/**
 * Util
 *
 * @author Dimitar Ivanov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 1.0.0
 * @package HeadCouch
 */
class Util
{
    /**
     * Normalize a given value to use in http_build_query()
     *
     * @param $value
     * @return string
     */
    public static function normalize($value)
    {
        if ($value === true) {
            $value = "true";
        } elseif ($value === false) {
            $value = "false";
        }

        return $value;
    }
}