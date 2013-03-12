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
		<title><?php echo htmlentities($subject, ENT_NOQUOTES, 'UTF-8'); ?> tweets found by Martha</title>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	</head>
	<body>
	<?php foreach($items as $status): ?>
		<blockquote class="twitter-tweet">
			<a href="https://twitter.com/<?php echo urlencode($status->user->screen_name); ?>/statuses/<?php echo urlencode($status->id); ?>">
				<?php echo htmlentities($status->text, ENT_NOQUOTES, 'UTF-8'); ?>
			</a>
		</blockquote>
	<?php endforeach; ?>
	<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
	</body>
</html>