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

use DCarbone\AmberHat\ExportFieldName\ExportFieldNameItemInterface;
use DCarbone\AmberHat\Instrument\InstrumentItemInterface;
use DCarbone\AmberHat\ItemInterface;

/**
 * Interface MetadataItemInterface
 * @package DCarbone\AmberHat\MetadataCollection
 */
interface MetadataItemInterface extends ItemInterface
{
    /**
     * @return null|string
     */
    public function getFieldName();

    /**
     * @return null|string
     */
    public function getFormName();

    /**
     * @return null|string
     */
    public function getSectionHeader();

    /**
     * @return null|string
     */
    public function getFieldType();

    /**
     * @return null|string
     */
    public function getFieldLabel();

    /**
     * @return null|string
     */
    public function getSelectChoicesOrCalculations();

    /**
     * @return null|string
     */
    public function getFieldNote();

    /**
     * @return null|string
     */
    public function getTextValidationTypeOrShowSliderNumber();

    /**
     * @return null|string
     */
    public function getTextValidationMin();

    /**
     * @return null|string
     */
    public function getTextValidationMax();

    /**
     * @return bool
     */
    public function isIdentifier();

    /**
     * @return null|string
     */
    public function getBranchingLogic();

    /**
     * @return bool
     */
    public function isRequiredField();

    /**
     * @return null|string
     */
    public function getCustomAlignment();

    /**
     * @return null|string
     */
    public function getQuestionNumber();

    /**
     * @return null|string
     */
    public function getMatrixGroupName();

    /**
     * @return null|string
     */
    public function getMatrixRanking();

    /**
     * @return null|string
     */
    public function getFieldAnnotation();

    /**
     * @return bool
     */
    public function isDateTimeField();

    /**
     * @param string $fieldValue
     * @return string
     */
    public function getDateTimeFormatString($fieldValue);

    /**
     * @return array
     */
    public function getFieldChoiceArray();

    /**
     * @param string $exportFieldName
     * @return null|string
     */
    public function getChoiceValueByExportFieldName($exportFieldName);

    /**
     * @param ExportFieldNameItemInterface $exportFieldName
     */
    public function addExportFieldNameItem(ExportFieldNameItemInterface $exportFieldName);

    /**
     * @return ExportFieldNameItemInterface[]
     */
    public function getExportFieldNameItems();

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