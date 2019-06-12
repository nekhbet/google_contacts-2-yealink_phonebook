<?php

// Protect it ..
$akey = $_GET['akey'] ?? '';
if ($akey != 'YOUR_SECRET_KEY')
{
    die('non auth');
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ( ! function_exists('ddd'))
{
    function ddd($array, $die = TRUE)
    {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
        if ($die) die();
    }
}

require_once(__DIR__.'/../vendor/autoload.php');

//if (php_sapi_name() != 'cli') {
//    throw new Exception('This application must be run on the command line.');
//}
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Google Sheets API PHP Quickstart');
    $client->setScopes(Google_Service_People::CONTACTS_READONLY);
    $client->setAuthConfig(__DIR__.'/../tokens/client_secret.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = __DIR__.'/../tokens/token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}


// Get the API client and construct the service object.
$client = getClient();

$service = new Google_Service_People($client);

$optParams = array(
    'pageSize' => 1000,
//  'personFields' => 'names,emailAddresses',
    'requestMask.includeField' => 'person.names,person.phoneNumbers'
);

$connections = $service->people_connections->listPeopleConnections(
    'people/me', $optParams
);


$numbers = [];
// Retrieve numbers
if ($connections)
{
    foreach ($connections as $connection)
    {
        $data = (array) $connection;
        if ( ! isset($data['names']))
        {

        }
        else
        {
            $name = $data['names'][0]->displayName;
            if ($name)
            {
                foreach ($data['phoneNumbers'] ?? [] as $phone)
                {
                    $number = $phone->canonicalForm;
                    if ($number)
                    {
                        $numbers[$number] = trim($name);
                    }
                }
            }
        }
    }
}
if ($numbers)
{
    // Sort them by name
    asort($numbers);
}


header('Content-Type: application/xml; charset=utf-8');
?><?xml version="1.0" encoding="UTF-8"?>
<YealinkIPPhoneBook>
    <Title>Phone Contacts Import</Title>
    <Menu Name="Phone Contacts">
        <?php foreach ($numbers ?? [] as $phone => $name): ?>
            <Unit Name="<?php echo $name; ?>" default_photos="" Phone3="" Phone2="" Phone1="<?php echo $phone; ?>"></Unit>
        <?php endforeach; ?>
    </Menu>
</YealinkIPPhoneBook>

