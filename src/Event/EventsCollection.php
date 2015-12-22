<?php namespace DCarbone\AmberHat\Event;

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

use DCarbone\AmberHat\AbstractItemCollection;
use DCarbone\AmberHat\Arm\ArmsCollection;

/**
 * Class EventsCollection
 * @package DCarbone\AmberHat\Event
 */
class EventsCollection extends AbstractItemCollection
{
    /** @var ArmsCollection|null */
    private $_armsCollection;

    /**
     * Constructor
     *
     * @param ArmsCollection|null $armsCollection
     */
    public function __construct(ArmsCollection $armsCollection = null)
    {
        $this->_armsCollection = $armsCollection;
    }

    /**
     * @param array $itemData
     */
    public function buildAndAppendItem(array $itemData)
    {
        $item = EventItem::createFromArray($itemData);
        if (isset($this->_armsCollection) && isset($this->_armsCollection[$item['arm_num']]))
        {
            /** @var \DCarbone\AmberHat\Arm\ArmItemInterface $arm */
            $arm = $this->_armsCollection[$item['arm_num']];
            $arm->addEventItem($item);
            $item->setArmItem($arm);
        }
        $this[$item['unique_event_name']] = $item;
    }
}