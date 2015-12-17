<?php namespace DCarbone\AmberHat\Record;

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

/**
 * Class RecordFieldFile
 * @package DCarbone\AmberHat\Record
 */
class RecordFieldFile implements RecordFieldFileInterface
{
    /** @var string */
    public $basename = null;
    /** @var string */
    public $filePath = null;
    /** @var string */
    public $fileType = null;
    /** @var int */
    public $fileSize = null;

    /**
     * Constructor
     *
     * @param string $filePath
     * @param string $fileType
     */
    public function __construct($filePath, $fileType)
    {
        $this->basename = basename($filePath);
        $this->filePath = $filePath;
        $this->fileType = $fileType;
        $this->fileSize = filesize($filePath);
    }

    /**
     * @return string
     */
    public function getBasename()
    {
        return $this->basename;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * WARNING: Files could be huge!
     *
     * @return string
     */
    public function getFileContents()
    {
        return file_get_contents($this->filePath);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->basename;
    }
}