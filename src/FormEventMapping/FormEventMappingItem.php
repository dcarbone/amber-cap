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

use DCarbone\AmberHat\AbstractItem;
use DCarbone\AmberHat\Arm\ArmItemInterface;
use DCarbone\AmberHat\Event\EventItemInterface;
use DCarbone\AmberHat\Instrument\InstrumentItemInterface;

/**
 * Class FormEventMappingItem
 * @package DCarbone\AmberHat\FormEventMapping
 */
class FormEventMappingItem extends AbstractItem implements FormEventMappingItemInterface
{
    /** @var array */
    protected $properties = array(
        'arm_num' => null,
        'unique_event_name' => null,
        'form' => null
    );

    /** @var ArmItemInterface */
    private $_armItem = null;
    /** @var EventItemInterface */
    private $_eventItem = null;
    /** @var InstrumentItemInterface */
    private $_instrumentItem = null;

    /**
     * @return string
     */
    public function getArmNum()
    {
        return $this->properties['arm_num'];
    }

    /**
     * @return string
     */
    public function getUniqueEventName()
    {
        return $this->properties['unique_event_name'];
    }

    /**
     * @return string
     */
    public function getForm()
    {
        return $this->properties['form'];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s:%s', $this->properties['form'], $this->properties['unique_event_name']);
    }

    /**
     * @param ArmItemInterface $arm
     */
    public function setArmItem(ArmItemInterface $arm)
    {
        $this->_armItem = $arm;
    }

    /**
     * @return ArmItemInterface
     */
    public function getArmItem()
    {
        return $this->_armItem;
    }

    /**
     * @param EventItemInterface $event
     */
    public function setEventItem(EventItemInterface $event)
    {
        $this->_eventItem = $event;
    }

    /**
     * @return EventItemInterface
     */
    public function getEventItem()
    {
        return $this->_eventItem;
    }

    /**
     * @param InstrumentItemInterface $instrument
     */
    public function setInstrumentItem(InstrumentItemInterface $instrument)
    {
        $this->_instrumentItem = $instrument;
    }

    /**
     * @return InstrumentItemInterface
     */
    public function getInstrumentItem()
    {
        return $this->_instrumentItem;
    }

    /**
     * @see getInstrumentItem()
     *
     * @return InstrumentItemInterface
     */
    public function getFormItem()
    {
        return $this->getInstrumentItem();
    }
}