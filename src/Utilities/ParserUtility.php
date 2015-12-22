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
    public static function parseMetadataJsonResponse(CurlPlusResponseInterface $response,
                                                     AbstractItemCollection $collection)
    {
        $data = json_decode((string)$response, true);
        $error = json_last_error();
        if ($error === JSON_ERROR_NONE)
        {
            if (count($data) === 1 && isset($data['error']))
                throw new REDCapApiException($data['error'], 500);

            foreach($data as $item)
            {
                $collection->buildAndAppendItem($item);
            }
        }
        else
        {
            throw new \DomainException(sprintf(
                '%s - Invalid JSON response seen.  Error: "%s"',
                get_class($collection),
                JsonErrorHelper::invoke(true, $error)
            ));
        }
    }
}