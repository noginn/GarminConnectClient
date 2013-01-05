<?php 

namespace Endurance\Exporter\GarminConnect;

use Buzz\Browser;

class GarminConnectExporter
{
    protected $browser;

    public function __construct(Browser $browser)
    {
        $this->browser = $browser;

        // Remove the timeout to allow time to download large files
        $this->browser->getClient()->setTimeout(0);
    }

    public function fetchActivities($limit = 50, $start = 1)
    {
        $response = $this->browser->get('http://connect.garmin.com/proxy/activitylist-service/activities/noginn?limit=' . $limit . '&start=' . $start);
        $result = json_decode($response->getContent(), true);

        return array_map(function ($info) {
            return new ActivityInfo($info);
        }, $result['activityList']);
    }

    public function downloadActivityTCX($id, $file)
    {
        $response = $this->browser->get('http://connect.garmin.com/proxy/activity-service-1.1/tcx/activity/' . $id . '?full=true');
        file_put_contents($file, $response->getContent());
    }
}
