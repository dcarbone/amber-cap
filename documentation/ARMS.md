# ARM Data Export

Once you have built your client, you may request Arm data for a project that uses the
arm concept using the following method:

```php
$armsCollection = $client->getArms();
```

You may optionally pass in an array of known Arm Numbers if you only wish to receive data
about a few specific ones.

The response comes in the form of a [ArmsCollection](../src/Arm/ArmsCollection.php) object, with
each object in the collection implementing [ArmItemInterface](../src/Arm/ArmItemInterface.php).

Ex:
```php
/** @var \DCarbone\AmberHat\Arm\ArmItemInterface $armItem **/
echo '<pre>';
foreach($client->getArms() as $armItem)
{
    echo sprintf("%s: %s\n", $armItem['arm_num'], $armItem['name']);
}
echo '</pre>';
```