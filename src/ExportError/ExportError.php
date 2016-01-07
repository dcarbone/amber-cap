<?php namespace DCarbone\AmberHat\ExportError;

/**
 * Class ExportError
 * @package DCarbone\AmberHat\ExportError
 */
class ExportError implements ExportErrorInterface
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

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}