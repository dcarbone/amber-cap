<?php namespace DCarbone\AmberHat\ExportError;

/**
 * Interface ExportErrorInterface
 * @package DCarbone\AmberHat\ExportError
 *
 * @property array postFields
 * @property int httpCode
 * @property string message
 */
interface ExportErrorInterface
{
    /**
     * @return array
     */
    public function getPostFields();

    /**
     * @return int
     */
    public function getHttpCode();

    /**
     * @return string
     */
    public function getMessage();
}