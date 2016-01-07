<?php namespace DCarbone\AmberHat\Import;

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

use DCarbone\AmberHat\Event\EventItemInterface;

/**
 * Interface ImportRecordInterface
 * @package DCarbone\AmberHat\Import
 */
interface ImportRecordInterface extends \ArrayAccess, \Countable, \Iterator
{
    /**
     * @return string
     */
    public function getRecordID();

    /**
     * @return EventItemInterface|null
     */
    public function getEvent();

    /**
     * @return string[]
     */
    public function getInstrumentNames();

    /**
     * @param string $instrumentName
     * @return string[]
     */
    public function getInstrumentFieldNames($instrumentName);

    /**
     * @param string $instrumentName
     * @return \DCarbone\AmberHat\Import\Field\ImportFieldInterface[]
     */
    public function getInstrumentFields($instrumentName);

    /**
     * @param string $instrumentName
     * @param string $fieldName
     * @return \DCarbone\AmberHat\Import\Field\ImportFieldInterface
     */
    public function getInstrumentField($instrumentName, $fieldName);

    /**
     * @return string
     */
    public function createEAVJsonEntry();

    /**
     * @return string
     */
    public function createFlatJsonEntry();

    /**
     * @return string
     */
    public function createEAVXMLEntry();

    /**
     * @return string
     */
    public function createFlatXMLEntry();
}