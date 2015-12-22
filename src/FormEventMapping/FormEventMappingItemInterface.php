<?php namespace DCarbone\AmberHat\FormEventMapping;

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

use DCarbone\AmberHat\Arm\ArmItemInterface;
use DCarbone\AmberHat\Event\EventItemInterface;
use DCarbone\AmberHat\Instrument\InstrumentItemInterface;
use DCarbone\AmberHat\ItemInterface;

/**
 * Interface FormEventMappingItemInterface
 * @package DCarbone\AmberHat\FormEventMapping
 */
interface FormEventMappingItemInterface extends ItemInterface
{
    /**
     * @return string
     */
    public function getArmNum();

    /**
     * @return string
     */
    public function getUniqueEventName();

    /**
     * @return string
     */
    public function getForm();

    /**
     * @param ArmItemInterface $arm
     */
    public function setArmItem(ArmItemInterface $arm);

    /**
     * @return ArmItemInterface
     */
    public function getArmItem();

    /**
     * @param EventItemInterface $event
     */
    public function setEventItem(EventItemInterface $event);

    /**
     * @return EventItemInterface
     */
    public function getEventItem();

    /**
     * @param InstrumentItemInterface $instrument
     */
    public function setInstrumentItem(InstrumentItemInterface $instrument);

    /**
     * @return InstrumentItemInterface
     */
    public function getInstrumentItem();

    /**
     * @see getInstrumentItem()
     *
     * @return InstrumentItemInterface
     */
    public function getFormItem();
}