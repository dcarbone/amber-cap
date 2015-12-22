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

use DCarbone\AmberHat\AbstractItemCollection;
use DCarbone\AmberHat\Arm\ArmsCollection;
use DCarbone\AmberHat\Event\EventsCollection;
use DCarbone\AmberHat\Instrument\InstrumentsCollection;
use DCarbone\AmberHat\ItemInterface;

/**
 * Class FormEventMappingsCollection
 * @package DCarbone\AmberHat\FormEventMapping
 */
class FormEventMappingsCollection extends AbstractItemCollection
{
    /** @var ArmsCollection|null */
    private $_armsCollection;
    /** @var EventsCollection|null */
    private $_eventsCollection;
    /** @var InstrumentsCollection|null */
    private $_instrumentsCollection;

    /**
     * Constructor
     *
     * @param ArmsCollection|null $armsCollection
     * @param EventsCollection|null $eventsCollection
     * @param InstrumentsCollection|null $instrumentsCollection
     */
    public function __construct(ArmsCollection $armsCollection = null,
                                EventsCollection $eventsCollection = null,
                                InstrumentsCollection $instrumentsCollection = null)
    {
        $this->_armsCollection = $armsCollection;
        $this->_eventsCollection = $eventsCollection;
        $this->_instrumentsCollection = $instrumentsCollection;
    }

    /**
     * @param array $itemData
     */
    public function buildAndAppendItem(array $itemData)
    {
        $item = FormEventMappingItem::createFromArray($itemData);

        if (isset($this->_armsCollection) && isset($this->_armsCollection[$item['arm_num']]))
        {
            /** @var \DCarbone\AmberHat\Arm\ArmItemInterface $arm */
            $arm = $this->_armsCollection[$item['arm_num']];
            $arm->addFormEventMappingItem($item);
            $item->setArmItem($arm);
        }

        if (isset($this->_eventsCollection) && isset($this->_eventsCollection[$item['unique_event_name']]))
        {
            /** @var \DCarbone\AmberHat\Event\EventItemInterface $event */
            $event = $this->_eventsCollection[$item['unique_event_name']];
            $event->addFormEventMappingItem($item);
            $item->setEventItem($event);
        }

        if (isset($this->_instrumentsCollection) && isset($this->_instrumentsCollection[$item['form']]))
        {
            /** @var \DCarbone\AmberHat\Instrument\InstrumentItemInterface $instrument */
            $instrument = $this->_instrumentsCollection[$item['form']];
            $instrument->addFormEventMappingItem($item);
            $item->setInstrumentItem($instrument);
        }

        $this[sprintf('%s:%s', $item['form'], $item['unique_event_name'])] = $item;
    }
}