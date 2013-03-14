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
 * @subpackage Views
 */

?>
<!DOCTYPE html>
<html lang="en-us">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" media="(device-height: 568px)" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
		<title>Martha</title>
		<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,700"/>
		<link rel="stylesheet" href="assets/martha.css">
		<link rel="shortcut icon" href="assets/temboo-drop-orange.png" type="image/png"/>
		<link rel="apple-touch-icon" href="assets/temboo-drop-touch-icon.png"/>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="assets/martha.js"></script>
	</head>
	<body>
		<form id="martha-form" action="" data-action="query/ajax.php" method="POST">
			<div class="hal">
				<a id="help" href="?query=help">?</a>
				<div id="martha-dialog">
					<?php if(isset($query) && strlen($query) > 0): ?>
						<div class="query"><?php echo htmlentities($query, ENT_NOQUOTES, 'UTF-8'); ?></div>
					<?php endif; ?>
					<?php foreach($martha->messages() as $index => $message): ?>
						<div class="message"><?php echo htmlentities($message, ENT_NOQUOTES, 'UTF-8'); ?></div>
					<?php endforeach; ?>
				</div>
				<input type="text" name="query" id="martha-query" autocomplete="off" />
				<p id="credits"><a href="https://temboo.com/" target="_blank">Powered by Temboo</a></p>
			</div>
			<input type="submit" value="" disabled="disabled" />
		</form>
	</body>
</html>