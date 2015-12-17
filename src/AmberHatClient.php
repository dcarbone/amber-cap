<?php namespace DCarbone\AmberHat;

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

use DCarbone\AmberHat\Information\ProjectInformation;
use DCarbone\AmberHat\Metadata\MetadataCollection;
use DCarbone\AmberHat\Record\RecordFieldFile;
use DCarbone\AmberHat\Record\RecordFieldInterface;
use DCarbone\AmberHat\Record\RecordParser;
use DCarbone\AmberHat\Utilities\FileUtility;
use DCarbone\CurlPlus\CurlPlusClient;
use DCarbone\CurlPlus\CurlPlusClientContainerInterface;

/**
 * Class AmberHatClient
 * @package DCarbone\AmberHat
 */
class AmberHatClient implements CurlPlusClientContainerInterface
{
    const MAX_MEMORY_FILESIZE = 10000000;

    /** @var string */
    private $_redcapApiEndpoint;
    /** @var string */
    private $_token;

    /** @var array */
    private $_files = array();

    /** @var string */
    private $_tempDirectory;
    /** @var bool */
    private $_saveTempFiles;

    /** @var CurlPlusClient */
    private $_curlClient;

    /** @var string */
    private $_redcapVersion = null;

    /**
     * Constructor
     *
     * TODO: Implement parameter validation
     *
     * @param string $redcapApiEndpoint
     * @param string $token
     * @param string $tempDirectory
     * @param bool $saveTempFiles
     */
    public function __construct($redcapApiEndpoint, $token, $tempDirectory, $saveTempFiles = false)
    {
        $this->_redcapApiEndpoint = $redcapApiEndpoint;
        $this->_token = $token;
        $this->_tempDirectory = rtrim($tempDirectory, "/\\");
        $this->_saveTempFiles = (bool)$saveTempFiles;

        $this->_curlClient = new CurlPlusClient();
    }

    /**
     * Cleanup any temp files if we are not supposed to keep them.
     */
    public function __destruct()
    {
        if (false === $this->_saveTempFiles)
        {
            foreach($this->_files as $file)
            {
                @unlink($file);
            }
        }
    }

    /**
     * @param array $armNumbers
     * @return \DCarbone\AmberHat\Arm\ArmsCollection
     */
    public function getArms(array $armNumbers = array())
    {
        return $this->_createCollection(
            '\\DCarbone\\AmberHat\\Arm\\ArmsCollection',
            (string)$this->_executeRequest('arm', array('format' => 'xml', 'arms' => $armNumbers))
        );
    }

    /**
     * @param array $armNumbers
     * @return \DCarbone\AmberHat\Event\EventsCollection
     */
    public function getEvents(array $armNumbers = array())
    {
        return $this->_createCollection(
            '\\DCarbone\\AmberHat\\Event\\EventsCollection',
            (string)$this->_executeRequest('event', array('format' => 'xml', 'arms' => $armNumbers))
        );
    }

    /**
     * @param array $forms
     * @param array $fields
     * @return MetadataCollection
     */
    public function getMetadata(array $forms = array(), array $fields = array())
    {
        return $this->_createCollection(
            '\\DCarbone\\AmberHat\\Metadata\\MetadataCollection',
            (string)$this->_executeRequest('metadata', array('format' => 'xml', 'forms' => $forms, 'fields' => $fields))
        );
    }

    /**
     * @return \DCarbone\AmberHat\ExportFieldName\ExportFieldNamesCollection
     */
    public function getExportFieldNames()
    {
        return $this->_createCollection(
            '\\DCarbone\\AmberHat\\ExportFieldName\\ExportFieldNamesCollection',
            (string)$this->_executeRequest('exportFieldNames', array('format' => 'xml'))
        );
    }

    /**
     * @return \DCarbone\AmberHat\Instrument\InstrumentsCollection
     */
    public function getInstruments()
    {
        return $this->_createCollection(
            '\\DCarbone\\AmberHat\\Instrument\\InstrumentsCollection',
            (string)$this->_executeRequest('instrument', array('format' => 'xml'))
        );
    }

    /**
     * @return \DCarbone\AmberHat\FormEventMapping\FormEventMappingsCollection
     */
    public function getFormEventMappings()
    {
        return $this->_createCollection(
            '\\DCarbone\\AmberHat\\FormEventMapping\\FormEventMappingsCollection',
            (string)$this->_executeRequest('formEventMapping', array('format' => 'xml'))
        );
    }

    /**
     * @return \DCarbone\AmberHat\User\UsersCollection
     */
    public function getUsers()
    {
        return $this->_createCollection(
            '\\DCarbone\\AmberHat\\User\\UsersCollection',
            (string)$this->_executeRequest('user', array('format' => 'xml'))
        );
    }

    /**
     * @return \DCarbone\AmberHat\Information\ProjectInformationInterface
     */
    public function getProjectInformation()
    {
        return ProjectInformation::createWithXMLString(
            (string)$this->_executeRequest('project', array('format' => 'xml'), false)
        );
    }

    /**
     * @param string $formName
     * @param array $fields
     * @param array $events
     * @param MetadataCollection|null $metadataCollection
     * @return RecordParser
     */
    public function getRecords($formName,
                               array $fields = array(),
                               array $events = array(),
                               MetadataCollection $metadataCollection = null)
    {
        $filename = (string)$this->_executeRequest(
            'record',
            array('format' => 'xml', 'type' => 'eav', 'forms' => $formName, 'events' => $events, 'fields' => $fields)
        );

        $parser = RecordParser::createWithXMLFile($filename, $formName, $metadataCollection);

        return $parser;
    }

    /**
     * @param RecordFieldInterface $recordField
     * @param null $outputDir
     * @return \DCarbone\AmberHat\Record\RecordFieldFileInterface|string
     */
    public function getFile(RecordFieldInterface $recordField, $outputDir = null)
    {
        if (null === $outputDir)
            $outputDir = $this->_tempDirectory;

        if (!is_dir($outputDir))
        {
            throw new \InvalidArgumentException(sprintf(
                '%s::getFile - "%s" does not appear to be a directory.',
                get_class($this),
                $outputDir
            ));
        }

        if (!is_readable($outputDir))
        {
            throw new \RuntimeException(sprintf(
                '%s::getFile - "%s" is not readable by this process, please check permissions.',
                get_class($this),
                $outputDir
            ));
        }

        if (!is_writable($outputDir))
        {
            throw new \RuntimeException(sprintf(
                '%s::getFile - "%s" is not writable by this process, please check permissions.',
                get_class($this),
                $outputDir
            ));
        }

        $tmpFilename = $this->_executeRequest(
            'file',
            array(
                'action' => 'export',
                'record' => $recordField->recordID,
                'field' => $recordField->fieldName,
                'event' => $recordField->redcapEventName,
            ),
            true,
            sprintf(
                '%s/%s',
                $this->_tempDirectory,
                sha1(sprintf('%s%s%s', $recordField->formName, $recordField->fieldName, rand(0, 100)))
            ),
            true,
            false
        );

        list($headers, $byteOffset) = FileUtility::extractHeadersFromFile($tmpFilename);

        if (null === $headers)
        {
            trigger_error(
                sprintf(
                    '%s::getFile - Unable to parse headers from response, this could be cause either by a malformed response or improper CURLOPT specification.  Form Name: %s, Field Name: %s, Record ID: %s',
                    get_class($this),
                    $recordField->formName,
                    $recordField->fieldName,
                    $recordField->recordID
                ),
                E_USER_WARNING
            );

            return $tmpFilename;
        }

        $headers = end($headers);
        if (isset($headers['Content-Type']))
        {
            preg_match('{name=["\']([^"\']+)["\']}S', $headers['Content-Type'], $filename);

            if (count($filename) === 2)
            {
                $file = FileUtility::removeHeadersAndMoveFile($tmpFilename, $byteOffset, $outputDir, $filename[1]);

                return new RecordFieldFile(
                    $file,
                    trim(substr($headers['Content-Type'], 0, strlen($headers['Content-Type']) - strlen($filename[0])), " ;")
                );
            }
        }

        throw new \RuntimeException(sprintf(
            '%s::getFile - Unable to determine filename from response headers.  Form: %s, Field Name: %s, Record ID: %s',
            get_class($this),
            $recordField->formName,
            $recordField->fieldName,
            $recordField->recordID
        ));
    }

    /**
     * @return string
     */
    public function getREDCapVersion()
    {
        if (null === $this->_redcapVersion)
            $this->_redcapVersion = (string)$this->_executeRequest('version', array('format' => 'xml'), false);

        return $this->_redcapVersion;
    }

    /**
     * @return CurlPlusClient
     */
    public function getCurlClient()
    {
        return $this->_curlClient;
    }

    /**
     * @internal
     * @return array
     */
    public function _getCachedFiles()
    {
        return $this->_files;
    }

    /**
     * @param string $collectionClass
     * @param string $filename
     * @return \DCarbone\AmberHat\AbstractItemCollection
     */
    private function _createCollection($collectionClass, $filename)
    {
        /** @var \DCarbone\AmberHat\AbstractItemCollection $collectionClass */

        if (filesize($filename) < self::MAX_MEMORY_FILESIZE)
            $collection = $collectionClass::createFromXMLString(file_get_contents($filename));
        else
            $collection = $collectionClass::createFromXMLFile($filename);

        return $collection;
    }

    /**
     * @param string $content
     * @param array $parameters
     * @param bool $outputToFile
     * @param string $filename
     * @param bool $includeHeadersInResponse
     * @param bool $removeFileOnShutdown
     * @return \DCarbone\CurlPlus\Response\CurlPlusResponseInterface
     * @internal param $type
     */
    private function _executeRequest($content,
                                     array $parameters,
                                     $outputToFile = true,
                                     $filename = null,
                                     $includeHeadersInResponse = false,
                                     $removeFileOnShutdown = true)
    {
        $postFieldString = $this->_buildPostFields($content, $parameters);

        $this->_curlClient->initialize($this->_redcapApiEndpoint, false);
        $this->_curlClient
            ->setCurlOpt(CURLOPT_POST, true)
            ->setCurlOpt(CURLOPT_FOLLOWLOCATION, true)
            ->setCurlOpt(CURLOPT_POSTFIELDS, $postFieldString)
            ->removeCurlOpt(CURLOPT_FILE);

        if ($includeHeadersInResponse)
            $this->_curlClient->setCurlOpt(CURLOPT_HEADER, true);
        else
            $this->_curlClient->setCurlOpt(CURLOPT_HEADER, false);

        if ($outputToFile)
        {
            if (null === $filename)
                $filename = sprintf('%s/%s.xml', $this->_tempDirectory, sha1($postFieldString));

            $fh = fopen($filename, 'w+b');

            if ($fh)
            {
                $this->_curlClient
                    ->setCurlOpt(CURLOPT_RETURNTRANSFER, false)
                    ->setCurlOpt(CURLOPT_FILE, $fh);

                $response = $this->_curlClient->execute(false);

                if (($error = $response->getError()) === '' && $response->getHttpCode() === 200)
                {
                    if ($removeFileOnShutdown)
                        $this->_files[] = $filename;

                    fclose($fh);
                    return $response;
                }

                throw new \RuntimeException(sprintf(
                    'Error received when querying for "%s" content. Code: %s,  Error: "%s"',
                    $content,
                    $response->getHttpCode(),
                    $error
                ));
            }

            throw new \RuntimeException(sprintf(
                'Unable to open temp file "%s" for writing. Please check runtime permissions at location.',
                $filename
            ));
        }
        else
        {
            $this->_curlClient->setCurlOpt(CURLOPT_RETURNTRANSFER, true);

            $response = $this->_curlClient->execute(false);

            if (($error = $response->getError()) === '' && $response->getHttpCode() === 200)
                return $response;

            throw new \RuntimeException(sprintf(
                'Error received when querying for "%s" content. HTTP Code: %s, Error: "%s"',
                $content,
                $response->getHttpCode(),
                $error
            ));
        }
    }

    /**
     * @param string $content
     * @param array $parameters
     * @return string
     */
    private function _buildPostFields($content, array $parameters = array())
    {
        return http_build_query(
            array_filter(
                array_merge(
                    $parameters,
                    array(
                        'content' => $content,
                        'token' => $this->_token
                    )
                ),
                function($value) {
                    if (is_array($value))
                        return count($value) > 0;
                    return '' !== $value;
                }
            )
        );
    }
}