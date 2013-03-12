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


// No query here. Just a TwiML response to greet the user and request their voice
// recording be transcribed. Twilio will then send the transcription text in a
// separate callback.
require_once('../views/voice.php');

?>