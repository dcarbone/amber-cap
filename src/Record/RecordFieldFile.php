<?php namespace DCarbone\AmberHat\Record;

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