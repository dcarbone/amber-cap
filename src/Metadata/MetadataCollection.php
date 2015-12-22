<?php namespace DCarbone\AmberHat\Metadata;

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
use DCarbone\AmberHat\ExportFieldName\ExportFieldNamesCollection;
use DCarbone\AmberHat\Instrument\InstrumentsCollection;

/**
 * Class MetadataCollection
 * @package PHPRedcap\MetadataCollection
 */
class MetadataCollection extends AbstractItemCollection
{
    /** @var ExportFieldNamesCollection|null */
    private $_exportFieldNames;
    /** @var InstrumentsCollection|null */
    private $_instruments;

    /**
     * Constructor
     *
     * @param ExportFieldNamesCollection|null $exportFieldNames
     * @param InstrumentsCollection|null $instruments
     */
    public function __construct(ExportFieldNamesCollection $exportFieldNames = null,
                                InstrumentsCollection $instruments = null)
    {
        $this->_exportFieldNames = $exportFieldNames;
        $this->_instruments = $instruments;
    }

    /**
     * @param array $itemData
     */
    public function buildAndAppendItem(array $itemData)
    {
        $item = MetadataItem::createFromArray($itemData);

        if (isset($this->_exportFieldNames))
        {
            $exportFieldNames = $this->_exportFieldNames->getExportFieldsForMetadataItem($item);
            if ($exportFieldNames)
            {
                foreach($exportFieldNames as $exportFieldName)
                {
                    $item->addExportFieldNameItem($exportFieldName);
                    $exportFieldName->setMetadataItem($item);
                }
            }
        }

        if (isset($this->_instruments) && isset($this->_instruments[$item['form_name']]))
        {
            /** @var \DCarbone\AmberHat\Instrument\InstrumentItemInterface $instrument */
            $instrument = $this->_instruments[$item['form_name']];
            $item->setInstrumentItem($instrument);
            $instrument->addMetadataItem($item);
        }

        $this[sprintf('%s:%s', $item['form_name'], $item['field_name'])] = $item;
    }
}