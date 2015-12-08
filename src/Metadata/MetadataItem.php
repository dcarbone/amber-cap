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

use DCarbone\AmberHat\AbstractItem;
use DCarbone\AmberHat\Utilities\ValueUtility;

/**
 * Class MetadataItem
 * @package PHPRedcap\MetadataCollection\MetadataCollection
 */
class MetadataItem extends AbstractItem implements MetadataItemInterface
{
    /** @var array */
    protected $properties = array(
        'field_name' => null,
        'form_name' => null,
        'section_header' => null,
        'field_type' => null,
        'field_label' => null,
        'select_choices_or_calculations' => null,
        'field_note' => null,
        'text_validation_type_or_show_slider_number' => null,
        'text_validation_min' => null,
        'text_validation_max' => null,
        'identifier' => null,
        'branching_logic' => null,
        'required_field' => null,
        'custom_alignment' => null,
        'question_number' => null,
        'matrix_group_name' => null,
        'matrix_ranking' => null,
        'field_annotation' => null,
    );

    /** @var null|array */
    private $_fieldChoiceArray;

    /** @var null|array */
    private $_exportFieldNames;

    /** @var string|false */
    private $_dateTimeFormatString;

    /** @var bool */
    private $_isDateTimeField;

    /** @var bool */
    private $_dateFormatSought = false;

    /**
     * @return null|string
     */
    public function getFieldName()
    {
        return $this->properties['field_name'];
    }

    /**
     * @return null|string
     */
    public function getFormName()
    {
        return $this->properties['form_name'];
    }

    /**
     * @return null|string
     */
    public function getSectionHeader()
    {
        return $this->properties['section_header'];
    }

    /**
     * @return null|string
     */
    public function getFieldType()
    {
        return $this->properties['field_type'];
    }

    /**
     * @return null|string
     */
    public function getFieldLabel()
    {
        return $this->properties['field_label'];
    }

    /**
     * @return null|string
     */
    public function getSelectChoicesOrCalculations()
    {
        return $this->properties['select_choices_or_calculations'];
    }

    /**
     * @return null|string
     */
    public function getFieldNote()
    {
        return $this->properties['field_note'];
    }

    /**
     * @return null|string
     */
    public function getTextValidationTypeOrShowSliderNumber()
    {
        return $this->properties['text_validation_type_or_show_slider_number'];
    }

    /**
     * @return null|string
     */
    public function getTextValidationMin()
    {
        return $this->properties['text_validation_min'];
    }

    /**
     * @return null|string
     */
    public function getTextValidationMax()
    {
        return $this->properties['text_validation_max'];
    }

    /**
     * @return bool
     */
    public function isIdentifier()
    {
        return $this->properties['identifier'] === 'y';
    }

    /**
     * @return null|string
     */
    public function getBranchingLogic()
    {
        return $this->properties['branching_logic'];
    }

    /**
     * @return bool
     */
    public function isRequiredField()
    {
        return $this->properties['required_field'] === 'y';
    }

    /**
     * @return null|string
     */
    public function getCustomAlignment()
    {
        return $this->properties['custom_alignment'];
    }

    /**
     * @return null|string
     */
    public function getQuestionNumber()
    {
        return $this->properties['question_number'];
    }

    /**
     * @return null|string
     */
    public function getMatrixGroupName()
    {
        return $this->properties['matrix_group_name'];
    }

    /**
     * @return null|string
     */
    public function getMatrixRanking()
    {
        return $this->properties['matrix_ranking'];
    }

    /**
     * @return null|string
     */
    public function getFieldAnnotation()
    {
        return $this->properties['field_annotation'];
    }

    /**
     * @return bool
     */
    public function isDateTimeField()
    {
        if (!isset($this->_isDateTimeField))
        {
            $this->_isDateTimeField = (
                $this->properties['field_type'] === 'text'
                && is_string($this->properties['text_validation_type_or_show_slider_number'])
                && strpos($this->properties['text_validation_type_or_show_slider_number'], 'date') === 0
            );
        }

        return $this->_isDateTimeField;
    }

    /**
     * Returns string usable by PHP's date functions, null if the format is not matched,
     * or FALSE if this is not a date field.
     *
     * @param string $fieldValue
     * @return false|null|string
     */
    public function getDateTimeFormatString($fieldValue)
    {
        if (!is_string($fieldValue) || '' === $fieldValue)
            return null;

        if (false === $this->_dateFormatSought)
        {
            if ($this->isDateTimeField())
                $this->_dateTimeFormatString = ValueUtility::getMetadataItemDateTimeFormatString($fieldValue);
            else
                $this->_dateTimeFormatString = false;

            $this->_dateFormatSought = true;
        }

        return $this->_dateTimeFormatString;
    }

    /**
     * @return array
     */
    public function getExportFieldNames()
    {
        if(!isset($this->_exportFieldNames))
        {
            switch($this->properties['field_type'])
            {
                case 'checkbox':
                    $this->_createCheckboxExportNameArray();
                    break;

                default:
                    $this->_exportFieldNames = array($this->properties['field_name']);
            }
        }

        return $this->_exportFieldNames;
    }

    /**
     * @return array
     */
    public function getFieldChoiceArray()
    {
        if (isset($this->_fieldChoiceArray))
            return $this->_fieldChoiceArray;

        switch($this->properties['field_type'])
        {
            case 'checkbox':
            case 'dropdown':
            case 'radio':
                $this->_parseMultiChoiceFieldData();
                break;

            case 'yesno':
                $this->_fieldChoiceArray = array('0' => 'No', '1' => 'Yes');
                break;

            case 'truefalse':
                $this->_fieldChoiceArray = array('0' => 'False', '1' => 'True');
                break;

            default:
                $this->_fieldChoiceArray = array();
        }

        return $this->_fieldChoiceArray;
    }

    /**
     * @param string $exportFieldName
     * @return string|null
     */
    public function getChoiceValueByExportFieldName($exportFieldName)
    {
        $choices = $this->getFieldChoiceArray();
        if (count($choices) === 0)
            return null;

        $exportNames = $this->getExportFieldNames();
        $idx = array_search($exportFieldName, $exportNames, true);
        if (-1 === $idx)
            return null;

        $i = 0;
        while ($i !== $idx)
        {
            next($choices);
            $i++;
        }

        return current($choices);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->properties['field_name'];
    }

    private function _createCheckboxExportNameArray()
    {
        $choices = $this->getFieldChoiceArray();
        $this->_exportFieldNames = array();
        foreach($choices as $k=>$v)
        {
            $this->_exportFieldNames[] = sprintf('%s___%s', $this->properties['field_name'], $k);
        }
    }

    private function _parseMultiChoiceFieldData()
    {
        $this->_fieldChoiceArray = array();
        if (isset($this->properties['select_choices_or_calculations']))
        {
            foreach(preg_split("{[|\n]}S", $this->properties['select_choices_or_calculations']) as $k=>$v)
            {
                $choice = array_map(function($value) { return trim($value); }, explode(',', $v));

                $key = array_shift($choice);
                $this->_fieldChoiceArray[$key] = implode(', ', $choice);
            }
        }
    }
}