# ARM Data Export

## Method
```php
$arms = $client->getArms();
```

You may optionally pass in an array of known Arm Numbers if you only wish to receive data
about a few specific ones.

## Response

Response will be of type [ArmsCollection](../src/Arm/ArmsCollection.php), containing 0+
[ArmItemInterface](../src/Arm/ArmItemInterface.php) objects.

## Example Usage

```php
echo '<pre>';
foreach($client->getArms() as $armItem)
{
    echo sprintf("%s: %s\n", $armItem['arm_num'], $armItem['name']);
}
echo '</pre>';
```