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
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; // thwarting short php tags ?>
<Response>
	<?php if(!isset($_POST['RecordingDuration'])): ?>
		<Say voice="woman"><?php echo htmlentities($martha->greet(), ENT_NOQUOTES, 'UTF-8') ?></Say>
		<Record timeout="3" maxLength="10" transcribeCallback="//<?php echo htmlentities(TWILIO_CALLBACK_USERNAME . ':' . TWILIO_CALLBACK_PASSWORD . '@' . $_SERVER['SERVER_NAME'] .  str_replace('voice', 'transcription', $_SERVER['REQUEST_URI']), ENT_COMPAT, 'UTF-8'); ?>" />
		<Say voice="woman"><?php echo htmlentities($martha->cantHearYou(), ENT_NOQUOTES, 'UTF-8') ?></Say>
		<Record timeout="5" maxLength="10" transcribeCallback="//<?php echo htmlentities(TWILIO_CALLBACK_USERNAME . ':' . TWILIO_CALLBACK_PASSWORD . '@' . $_SERVER['SERVER_NAME'] .  str_replace('voice', 'transcription', $_SERVER['REQUEST_URI']), ENT_COMPAT, 'UTF-8'); ?>" />
		<Say voice="woman"><?php echo htmlentities($martha->sorryDave(), ENT_NOQUOTES, 'UTF-8') ?></Say>
	<?php else: ?>
		<Say voice="woman"><?php echo htmlentities($martha->okay(), ENT_NOQUOTES, 'UTF-8') ?> I'll text you the result!</Say>
	<?php endif; ?>
</Response>