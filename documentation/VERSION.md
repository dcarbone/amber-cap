# REDCap Version Export

## Method
```php
$version = $client->getREDCapVersion();
```

## Response

Response will be a string containing the version \# of your REDCap instance.

**Note**: This method only executes a requests against REDCap once per thread.  Subsequent calls
to this method will simply returned the cached value seen from the first request.