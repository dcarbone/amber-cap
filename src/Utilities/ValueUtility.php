<?php namespace DCarbone\AmberHat\Utilities;

/*
    AmberHat: A REDCap Client library written in PHP
    Copyright (C) 2015  Daniel Paul Carbone (daniel.p.carbone@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

/**
 * Class ValueUtility
 * @package DCarbone\AmberHat\Utilities
 */
class ValueUtility
{
    /**
     * Thanks to http://stackoverflow.com/a/1401716/1141357 for the below regex
     *
     * @var string
     */
    private static $_invalidByteRemovalRegex = '/((?:[\x00-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3}){1,100})|./Sx';

    /**
     * @param $fieldValue
     * @return string|null
     */
    public static function getFieldDateTimeFormatString($fieldValue)
    {
        static $testRegex = array(
            '/(^[0-9]{4}-[0-9]{2}-[0-9]{2}$)/' => 'Y-m-d',
            '/(^[0-9]{2}\/[0-9]{2}\/[0-9]{4}\s[0-9]{1,2}:[0-9]{2}$)/' => 'm/d/Y G:i',
            '/(^[0-9]{2}-[0-9]{2}-[0-9]{4}\s[0-9]{2}:[0-9]{2}$)/' => 'm-d-Y H:i',
            '/(^[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}$)/' => 'Y-m-d H:i',
        );

        foreach($testRegex as $regex=>$format)
        {
            if ((bool)preg_match($regex, $fieldValue))
                return $format;
        }

        return null;
    }

    /**
     * @param string $input
     * @return string
     */
    public static function utf8ByteFix($input)
    {
        return preg_replace(self::$_invalidByteRemovalRegex, '$1', $input);
    }
}