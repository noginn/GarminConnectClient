Garmin Connect Client
=====================

Provides a simple PHP library to interact with the Garmin Connect website and APIs.

Usage
-----

### Export the 25 most recent activities

```php
<?php

use Buzz\Browser;
use Buzz\Client\Curl;
use Endurance\GarminConnect\GarminConnectClient;

$username = 'test';
$password = 'qwerty';

$browser = new Browser(new Curl())
$client = new GarminConnectClient($browser);
$client->signIn($username, $password);

$activities = $client->fetchActivities($username, 25);
foreach ($activities as $activity) {
    $client->downloadActivity($activity->getId(), __DIR__ . '/activities/' . $activity->getId() . '.tcx');
}
```

### Upload an activity FIT file

```php
<?php

use Buzz\Browser;
use Buzz\Client\Curl;
use Endurance\GarminConnect\GarminConnectClient;

$username = 'test';
$password = 'qwerty';

$browser = new Browser(new Curl());
$client = new GarminConnectClient($browser);
$client->signIn($username, $password);
$client->uploadActivity('/path/to/activity.fit');
```

Running the export script
-------------------------

    $ cd garmin-connect-client
    $ php composer.phar install

    $ ./bin/export username password [/path/to/save/files]
