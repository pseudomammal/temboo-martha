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
		<title><?php echo htmlentities($subject, ENT_NOQUOTES, 'UTF-8'); ?> videos found by Martha</title>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script>
			$(function(){
				$('a').replaceWith(function(){
					return $(this).attr('href').replace(/.*\?v=([^&]+).*/g, '<iframe width="420" height="345" src="http://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>');
				});
			});
		</script>
	</head>
	<body>
		<?php foreach($items as $video): ?>
				<a href="<?php echo htmlentities($video->link[0]->href, ENT_COMPAT, 'UTF-8'); ?>"><?php echo htmlentities($video->link[0]->href, ENT_NOQUOTES, 'UTF-8'); ?></a>
		<?php endforeach; ?>
	</body>
</html>