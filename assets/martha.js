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
 * @subpackage Assets
 */

$(function(){
	var form = $('#martha-form');
	var input = $('input[name=query]', form);
	var submit = $('input[type=submit]', form);
	submit.fadeOut('fast', function(){
		submit.attr('disabled', false);
		submit.fadeIn('fast');
	});
	var fadeInterval;
	var dialog = $('#martha-dialog');
	linkify(dialog);
	form.submit(function(e){
		e.preventDefault();
		var query = input.val();
		if(query.length > 0) {
			$('<div class="query"/>').text(query).appendTo(dialog);
		}
		input.val('').attr('disabled', true);
		submit.attr('disabled', true);
		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
			input.blur();
		}
		fadeOut();
		$.post(form.data('action'), { query: query }, function(results){
			input.attr('disabled', false);
			submit.attr('disabled', false).stop().fadeTo(1, 1);
			if(results.messages && results.messages.length) {
				for(var i = 0, j = results.messages.length; i < j; i++) {
					var message = results.messages[i];
					linkify($('<div class="message"/>').text(message).appendTo(dialog));
					$('html, body').animate({ scrollTop: $(document).height() }, 'fast');
				}
			}
		}, 'json');
	});

	function linkify(linkContainer) {
		linkContainer = $(linkContainer);
		var newHtml = linkContainer.html();
		newHtml = newHtml.replace(/(http:\/\/bit.ly\/\w+|https:\/\/github.com\/temboo|https:\/\/github.com\/pseudomammal\/temboo-martha|https:\/\/temboo.com)/g, '<a href="$1" target="_blank">$1</a>');
		newHtml = newHtml.replace(/(^|\s+)@(\w+)/g, '$1<a href="http://twitter.com/$2" target="_blank">@$2</a>');
		linkContainer.html(newHtml);
		return linkContainer;
	}

	function fadeOut() {
		if(submit.attr('disabled')) {
			submit.fadeTo('slow', 0.5, fadeIn);
		}
	}

	function fadeIn() {
		if(submit.attr('disabled')) {
			submit.fadeTo('slow', 1, fadeOut);
		}
	}

	$('html, body').animate({ scrollTop: $(document).height() }, 'fast');
});