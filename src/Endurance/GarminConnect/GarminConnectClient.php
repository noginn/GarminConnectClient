<?php 

namespace Endurance\GarminConnect;

use Buzz\Browser;
use Buzz\Message\Form\FormRequest;
use Buzz\Message\Form\FormUpload;
use Buzz\Message\Response;
use Buzz\Util\Url;

class GarminConnectClient
{
    protected $browser;
    protected $username;

    public function __construct(Browser $browser)
    {
        $this->browser = $browser;

        // Set client options
        $client = $this->browser->getClient();

        // Don't follow redirects (important for asserting we signed in)
        $client->setOption(CURLOPT_FOLLOWLOCATION, false);

        // Initialise the cookie jar
        $cookieFile = tempnam('/tmp', 'GarminConnectClient');
        $client->setOption(CURLOPT_COOKIEJAR, $cookieFile);
        $client->setOption(CURLOPT_COOKIEFILE, $cookieFile);

        // Remove the timeout to allow time to download large files
        $client->setTimeout(0);
    }

    public function signIn($username, $password)
    {
        // Load the sign in page to get session cookies
        $this->browser->get('https://connect.garmin.com/signin');

        // Post the login form
        $response = $this->browser->post('https://connect.garmin.com/signin', array(), http_build_query(array(
            'login' => 'login',
            'login:loginUsernameField' => $username,
            'login:password' => $password,
            'login:signInButton' => 'Sign In',
            'javax.faces.ViewState' => 'j_id1'
        )));

        if ($response->getHeader('Location') === null) {
            throw new \RuntimeException('Unable to sign in');
        }

        // Set the username to be used later and to signify that we are signed in
        $this->username = $username;
    }

    public function isSignedIn()
    {
        return $this->username !== null;
    }

    public function fetchActivities($username = null, $limit = 50, $start = 1)
    {
        if (!$this->isSignedIn()) {
            throw new \RuntimeException('Not signed in');
        }

        if ($username === null) {
            // Default to the signed in user
            $username = $this->username;
        }

        $response = $this->browser->get('http://connect.garmin.com/proxy/activitylist-service/activities/' . $username . '?limit=' . $limit . '&start=' . $start);
        $result = json_decode($response->getContent(), true);

        return array_map(function ($info) {
            return new ActivityInfo($info);
        }, $result['activityList']);
    }

    public function downloadActivity($id, $file)
    {
        if (!$this->isSignedIn()) {
            throw new \RuntimeException('Not signed in');
        }

        $response = $this->browser->get('http://connect.garmin.com/proxy/activity-service-1.1/tcx/activity/' . $id . '?full=true');
        file_put_contents($file, $response->getContent());
    }

    public function uploadActivity($file)
    {
        $request = new FormRequest(FormRequest::METHOD_POST);

        // Set the request URL
        $url = new Url('https://connect.garmin.com/proxy/upload-service-1.1/json/upload/.fit');
        $url->applyToRequest($request);

        // Set the form fields
        $request->setField('responseContentType', 'text/html');

        $data = new FormUpload($file);
        $data->setContentType('image/x-fits');
        $request->setField('data', $data);

        $response = new Response();
        $this->browser->getClient()->send($request, $response);

        return json_decode($response->getContent(), true);
    }
}
