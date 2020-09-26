<?php
require __DIR__ . '/vendor/autoload.php';
print_r($_POST);

if($_POST){
    if(validateFields() && validateReCapcha()){
        // repack date and time to match the desired calendar input
        // example what we have : 
            // date -> 09/16/2020
            // time -> 23:00
        // desired -> 2020-09-16T23:00:00
        $dt = ''; 
        $dateChunks = explode('/', $_POST["i_date"]);
        $time=$_POST["i_time"];
        $dt = "$dateChunks[2]-$dateChunks[0]-$dateChunks[1]T$time:00";

        createGoogleCalendarEvent(
            $_POST["i_name"],
            $_POST["i_phone"],
            $_POST["i_email"],
            $_POST['i_note'],
            $dt
        );

    }
}

function validateReCapcha(){
    // print_r($_POST);
		$url = "https://www.google.com/recaptcha/api/siteverify";
		$data = [
			'secret' => "6Le9xdAZAAAAAO6K7H1kPZ4U1E3K5ZIUK6cRmtRv",
			'response' => $_POST['recapcha_token'],
			'remoteip' => $_SERVER['REMOTE_ADDR']
		];

		$options = array(
		    'http' => array(
		      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		      'method'  => 'POST',
		      'content' => http_build_query($data)
		    )
		  );

		$context  = stream_context_create($options);
  		$response = file_get_contents($url, false, $context);

        $res = json_decode($response, true);
        echo $res['success'];
        return $res['success'];
}

//return: true : false
function validateFields(){
    if (
        isset($_POST["i_name"])     && 
        isset($_POST["i_phone"])    &&
        isset($_POST["i_email"])    &&
        isset($_POST["i_time"])     &&
        isset($_POST["i_date"])     &&
        isset($_POST["i_note"])     &&
        isset($_POST["recapcha_token"])
        ){
            if (
                preg_match("/^[A-z]+ [A-z]+$/i", $_POST["i_name"]) &&
                preg_match("/^[0-9]{8,10}$/i", $_POST["i_phone"]) &&
                filter_var($_POST["i_email"], FILTER_VALIDATE_EMAIL) &&
                preg_match("/^[0-9]{2}[:][0-9]{2}$/i", $_POST["i_time"]) &&
                preg_match("/^[0-9]{2}[\/][0-9]{2}[\/][0-9]{4}$/i", $_POST["i_date"]) 
            ){ return true;}
    }
    return false;
}

function createGoogleCalendarEvent($iname, $iphone, $iemail, $inote, $idatetime){
    // Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);

// Refer to the PHP quickstart on how to setup the environment:
// https://developers.google.com/calendar/quickstart/php
// Change the scope to Google_Service_Calendar::CALENDAR and delete any stored
// credentials.

$event = new Google_Service_Calendar_Event(array(
    'summary' => "$iname - $iphone",
    'sendNotifications' => true,
    'location' => '',
    'description' => "$inote",
    'start' => array(
      'dateTime' => "$idatetime",
      'timeZone' => 'Europe/Belgrade',
    ),
    'end' => array(
      'dateTime' => "$idatetime",
      'timeZone' => 'Europe/Belgrade',
    ),
    'recurrence' => array(
      'RRULE:FREQ=DAILY;COUNT=1'
    ),
    'attendees' => array(
      array('email' => "$iemail")
    ),
    'reminders' => array(
      'useDefault' => FALSE,
      'overrides' => array(
        array('method' => 'email', 'minutes' => 30), // 30min
        array('method' => 'email', 'minutes' => 15), // 15min
      ),
    ),
  ));
  
  $calendarId = 'primary';
  $event = $service->events->insert($calendarId, $event);
  printf('Event created: %s\n', $event->htmlLink);

}

function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Google Calendar API PHP Quickstart');
    $client->setScopes(Google_Service_Calendar::CALENDAR);
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'token.json';
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
?>