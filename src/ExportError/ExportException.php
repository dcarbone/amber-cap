<?php namespace DCarbone\AmberHat\ExportError;

/**
 * Class ExportException
 * @package DCarbone\AmberHat\ExportError
 */
class ExportException extends \RuntimeException implements ExportErrorInterface
{
    /** @var array */
    public $postFields = array();
    /** @var int */
    public $httpCode = null;
    /** @var string */
    public $message = null;

    /**
     * Constructor
     *
     * @param string $postFields
     * @param int $httpCode
     * @param string $message
     */
    public function __construct($postFields, $httpCode, $message)
    {
        parent::__construct($message, $httpCode);

        $fields = array();
        @parse_str($postFields, $fields);

        $tokenKey = null;
        foreach($fields as $key=>$value)
        {
            if (preg_match('{^(token)$}i', $key))
            {
                $tokenKey = $key;
                break;
            }
        }

        if ($tokenKey)
            unset($postFields[$tokenKey]);

        $this->postFields = $fields;
        $this->httpCode = $httpCode;
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getPostFields()
    {
        return $this->postFields;
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }
}