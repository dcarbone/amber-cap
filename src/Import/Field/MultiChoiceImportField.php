<?php namespace DCarbone\AmberHat\Import\Field;

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

use DCarbone\AmberHat\Metadata\MetadataItemInterface;

/**
 * Class MultiChoiceImportField
 * @package DCarbone\AmberHat\Import\Field
 */
class MultiChoiceImportField extends AbstractImportField
{
    /** @var array */
    protected $choices;

    /**
     * Constructor
     *
     * @param MetadataItemInterface $metadataItem
     */
    public function __construct(MetadataItemInterface $metadataItem)
    {
        parent::__construct($metadataItem);

        $this->choices = $metadataItem->getFieldChoiceArray();
    }

    /**
     * @param mixed $fieldValue
     */
    public function setFieldValue($fieldValue)
    {
        if (isset($this->choices[$fieldValue]))
        {
            parent::setFieldValue($fieldValue);
        }
        else if (($idx = array_search($fieldValue, $this->choices)) > -1)
        {
            parent::setFieldValue($idx);
        }
        else
        {
            throw new \InvalidArgumentException(sprintf(
                '%s::setFieldValue - Choice field "%s" requires value be one of ["%s"] or ["%s"].  "%s" seen.',
                get_class($this),
                implode('", "', array_keys($this->choices)),
                implode('", "', array_values($this->choices))
            ));
        }
    }
}