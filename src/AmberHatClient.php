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

use DCarbone\AmberHat\Metadata\MetadataCollection;
use DCarbone\AmberHat\Project\ProjectInformation;
use DCarbone\AmberHat\Record\RecordParser;

/**
 * Class AmberHatClient
 * @package DCarbone\AmberHat
 */
class AmberHatClient
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
            $this->_executeRequest('arm', 'flat', array('arms' => $armNumbers), false)
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
            $this->_executeRequest('event', 'flat', array('arms' => $armNumbers), false)
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
            $this->_executeRequest('metadata', 'flat', array('forms' => $forms, 'fields' => $fields), false)
        );
    }

    /**
     * @return \DCarbone\AmberHat\ExportFieldName\ExportFieldNamesCollection
     */
    public function getExportFieldNames()
    {
        return $this->_createCollection(
            '\\DCarbone\\AmberHat\\ExportFieldName\\ExportFieldNamesCollection',
            $this->_executeRequest('exportFieldNames', 'flat')
        );
    }

    /**
     * @return \DCarbone\AmberHat\Project\ProjectInformationInterface
     */
    public function getProjectInformation()
    {
        return ProjectInformation::createWithXMLString(
            $this->_executeRequest('project', 'flat', array(), true)
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
        $filename = $this->_executeRequest(
            'record',
            'eav',
            array('forms' => $formName, 'events' => $events, 'fields' => $fields));

        $parser = RecordParser::createWithXMLFile($filename, $formName, $metadataCollection);

        return $parser;
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
     * TODO: Make this less messy
     *
     * @param string $content
     * @param $type
     * @param array $additionalParams
     * @param bool $returnData
     * @return string
     */
    private function _executeRequest($content, $type, array $additionalParams = array(), $returnData = false)
    {
        $postFieldString = $this->_buildPostFields($content, $type, $additionalParams);

        if ($returnData)
        {
            $ch = curl_init($this->_redcapApiEndpoint);

            if ($ch)
            {
                curl_setopt_array($ch, array(
                    CURLOPT_POST => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POSTFIELDS => $postFieldString,
                    CURLOPT_SSL_VERIFYPEER => false
                ));

                $data = curl_exec($ch);

                if (($error = curl_error($ch)) === '')
                {
                    curl_close($ch);
                    return $data;
                }

                if (gettype($ch) === 'resource')
                    curl_close($ch);

                throw new \RuntimeException(sprintf(
                    'Error received when querying for "%s" content.  Error: "%s"',
                    $content,
                    $error
                ));
            }

            throw new \RuntimeException('Unable to initialize CURL resource.');
        }
        else
        {
            $filename = sprintf('%s/%s.xml', $this->_tempDirectory, sha1($postFieldString));

            $fh = fopen($filename, 'w+');

            if ($fh)
            {
                $ch = curl_init($this->_redcapApiEndpoint);

                if ($ch)
                {
                    curl_setopt_array($ch, array(
                        CURLOPT_POST => true,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_FILE => $fh,
                        CURLOPT_POSTFIELDS => $postFieldString,
                        CURLOPT_SSL_VERIFYPEER => false
                    ));

                    curl_exec($ch);

                    if (($error = curl_error($ch)) === '')
                    {
                        $this->_files[] = $filename;
                        fclose($fh);
                        curl_close($ch);
                        return $filename;
                    }

                    fclose($fh);

                    if (gettype($ch) === 'resource')
                        curl_close($ch);

                    throw new \RuntimeException(sprintf(
                        'Error received when querying for "%s" content.  Error: "%s"',
                        $content,
                        $error
                    ));
                }

                throw new \RuntimeException('Unable to initialize CURL resource.');
            }

            throw new \RuntimeException(sprintf(
                'Unable to open temp file "%s" for writing. Please check runtime permissions at location.',
                $filename
            ));
        }
    }

    /**
     * @param string $content
     * @param string $type
     * @param array $others
     * @return string
     */
    private function _buildPostFields($content, $type, array $others = array())
    {
        return http_build_query(
            array_filter(
                array_merge(
                    $others,
                    array(
                        'content' => $content,
                        'token' => $this->_token,
                        'format' => 'xml',
                        'type' => $type,
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