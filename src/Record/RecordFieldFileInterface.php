<?php namespace DCarbone\AmberHat\Record;

/**
 * Interface RecordFieldFileInterface
 * @package DCarbone\AmberHat\File
 *
 * @property string basename
 * @property string filePath
 * @property string fileType
 * @property int fileSize
 */
interface RecordFieldFileInterface
{
    /**
     * @return string
     */
    public function getBasename();

    /**
     * @return string
     */
    public function getFilePath();

    /**
     * @return string
     */
    public function getFileType();

    /**
     * @return int
     */
    public function getFileSize();

    /**
     * @return string
     */
    public function getFileContents();

    /**
     * @return string
     */
    public function __toString();
}