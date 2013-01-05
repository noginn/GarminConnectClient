Garmin Connect Export
=====================

Endurance is a PHP library for parsing cycling GPS activities and calculating metrics.

Usage
-----

### Export the 25 most recent activities

```php
<?php

use Buzz\Browser;
use Buzz\Client\Curl;
use Endurance\Exporter\GarminConnect\GarminConnectExporter;

$browser = new Browser(new Curl());
$exporter = new GarminConnectExporter($browser);

$activities = $exporter->fetchActivities(100);
foreach ($activities as $activity) {
    $exporter->downloadActivityTCX($activity->getId(), __DIR__ . '/activities/' . $activity->getId() . '.tcx');
}

```

Running the export script
-------------------------

    $ cd garmin-connect-exporter
    $ php composer.phar install --dev

    $ ./bin/export [/path/to/save/files]
