<?php namespace DCarbone\AmberHat\Utilities;

use DCarbone\AmberHat\AbstractItemCollection;
use DCarbone\AmberHat\Exception\REDCapApiException;
use DCarbone\CurlPlus\Response\CurlPlusResponseInterface;
use DCarbone\Helpers\JsonErrorHelper;

/**
 * Class ParserUtility
 * @package DCarbone\AmberHat\Utilities
 */
abstract class ParserUtility
{
    /**
     * @param CurlPlusResponseInterface $response
     * @param AbstractItemCollection $collection
     * @throws REDCapApiException
     */
    public static function populateMetadataCollection(CurlPlusResponseInterface $response,
                                                      AbstractItemCollection $collection)
    {
        foreach(static::parseJsonResponse($response) as $item)
        {
            $collection->buildAndAppendItem($item);
        }
    }

    /**
     * @param CurlPlusResponseInterface $response
     * @return array
     * @throws REDCapApiException
     */
    public static function parseJsonResponse(CurlPlusResponseInterface $response)
    {
        $data = json_decode((string)$response, true);
        $error = json_last_error();
        if ($error === JSON_ERROR_NONE)
        {
            if (count($data) === 1 && isset($data['error']))
                throw new REDCapApiException($data['error'], 500);

            return $data;
        }

        throw new \DomainException(sprintf(
            'Invalid JSON response seen.  Error: "%s"',
            JsonErrorHelper::invoke(true, $error)
        ));
    }
}