<?php namespace DCarbone\AmberHat\Arm;

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
use DCarbone\AmberHat\Event\EventItemInterface;

/**
 * Class ArmItem
 * @package DCarbone\AmberHat\Arm
 */
class ArmItem extends AbstractItem implements ArmItemInterface
{
    /** @var array */
    protected $properties = array(
        'arm_num' => null,
        'name' => null,
    );

    /** @var EventItemInterface[] */
    private $_events = array();

    /**
     * @return null|number
     */
    public function getArmNum()
    {
        return $this->properties['arm_num'];
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->properties['name'];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->properties['name'];
    }

    /**
     * @param EventItemInterface $eventItem
     */
    public function addEvent(EventItemInterface $eventItem)
    {
        $this->_events[$eventItem['unique_event_name']] = $eventItem;
    }

    /**
     * @return \DCarbone\AmberHat\Event\EventItemInterface[]
     */
    public function getEvents()
    {
        return $this->_events;
    }
}