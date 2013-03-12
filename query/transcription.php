<?php

/**
 * Martha
 *
 * An artificially intelligent-ish personal assistant built on the
 * Temboo API library.
 *
 * PHP version 5
 *
 * LICENSE: Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author     Nick Blanchard-Wright <nick.wright@temboo.com>
 * @copyright  2013 Temboo, Inc.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link       http://www.temboo.com
 * @package    Martha
 * @subpackage Controllers
 */


// Authentication FROM Twilio. Prevents others abusing Martha to spam innocent phones.
// Please please PLEASE also use SSL for these callbacks.
require_once('../config.php');
if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] != TWILIO_CALLBACK_USERNAME || $_SERVER['PHP_AUTH_PW'] != TWILIO_CALLBACK_PASSWORD) {
    header('WWW-Authenticate: Basic realm="Martha"');
    header('HTTP/1.0 401 Unauthorized');
    exit;
}

// Martha core files.
require_once('../martha.php');

$martha = new Martha();

// Input from Twilio callback
$from = isset($_POST['From']) ? preg_replace('/[^a-zA-Z0-9]/', '', (string) $_POST['From']) : false;
$transcriptionText = isset($_POST['TranscriptionText']) ? (string) $_POST['TranscriptionText'] : '';
if(get_magic_quotes_gpc()) {
	$transcriptionText = stripslashes($transcriptionText);
}



$martha->say('I think you said: ' . $transcriptionText);

$martha->query($transcriptionText);

$responses = array();

foreach($martha->messages() as $response) {
	$response = str_replace("\r\n", "\n", $response);
	$response = wordwrap($response, 150, "\r\n", true);
	$responses = array_merge($responses, explode("\r\n", $response));
}
$total = count($responses);

try {
	foreach($responses as $index => $response) {
	    $martha->sendSMS($from, $response . ' (' . ($index + 1) . '/' . $total . ')');
	}
} catch(Temboo_Exception $e) {
    error_log(get_class($martha) . '::sendSMS failed with ' . get_class($e) . ': ' . $e->getMessage());
}

// No view for transcription.

?>