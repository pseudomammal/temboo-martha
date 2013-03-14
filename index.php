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

// Martha core files.
require_once('martha.php');

$martha = new Martha();


// Serve a previously rendered answer, e.g. a list of results.
if(isset($_GET['answer'])) {
	$answer = (string) $_GET['answer'];
	if(get_magic_quotes_gpc()) {
		$answer = stripslashes($answer);
	}
	$file = $martha->serveFile($answer);
	if($file) {
		echo $file;
		exit;
	}
	$martha->say($martha->error("I couldn't find that file!"));
}


// Process queries submitted via vanilla web form.
$query = '';
if(isset($_REQUEST['query'])) {
	$query = (string) $_REQUEST['query'];
	if(get_magic_quotes_gpc()) {
		$query = stripslashes($query);
	}
}

$martha->query($query);

// Present the web interface.
require_once('views/index.php');

?>