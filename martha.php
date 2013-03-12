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
 * @subpackage Core
 */


/**
 * Configuration -- edit this with your Temboo and API credentials.
 *
 * See files for URLs to get new credentials.
 */
require_once('config.php');


/**
 * Temboo PHP SDK. Download from: https://temboo.com/download
 */
require_once('php-sdk/src/temboo.php');


/**
 * Martha is a friendly and helpful bot who will answer your questions via web,
 * SMS, or voice call. She does this by querying many different APIs in one
 * normalized fashion with the Temboo library.
 */
class Martha {

    /**
     * Authenticated session for executing Temboo choreographies.
     *
     * @var Temboo_Session
     */
    protected $_tembooSession;

    /**
     * The list of messages to send to the user.
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * The list of resource types (with synonyms) Martha knows how to search for, keyed by search method.
     *
     * @var array
     */
    protected $_resourceTypes = array(
        'searchImages' => array('image', 'photo', 'picture'),
        'searchVideos' => array('video', 'movie', 'film'),
        'searchTweets' => array('tweet', 'twitter', 'toot')
    );


    /**
     * Instantiate an instance of Martha with a Temboo connection.
     *
     * @return Martha new Martha instance
     */
    public function __construct() {
        // Instantiate Temboo session...
        $this->_tembooSession = new Temboo_Session(TEMBOO_ACCOUNT, TEMBOO_APP_NAME, TEMBOO_APP_KEY);
    }


    /**
     * Search images.
     *
     * Choreos used in this method:
     * https://live.temboo.com/library/Library/Flickr/Photos/Search/
     *
     * @param string $subject the thing to search for, e.g. "cats"
     * @param int $limit (optional) max number of results
     * @return string a url to a rendered list of results, or an error message on failure
     * @throws Temboo_Exception should unforseen misfortunes befall us
     */
    protected function searchImages($subject, $limit = false) {
        // Instantiate the Choreo, using a previously instantiated Temboo_Session object
        $search = new Flickr_Photos_Search($this->_tembooSession);

        // Get an input object for the Choreo
        $searchInputs = $search->newInputs();

        // Set credential to use for execution
        $searchInputs->setCredential(TEMBOO_FLICKR_CREDENTIAL);

        // Set inputs
        $searchInputs->setText($subject)->setMedia("photos")->setResponseFormat("json");
        if($limit) {
            $searchInputs->setPerPage($limit);
        }

        // Execute Choreo and get results
        $searchResults = $search->execute($searchInputs)->getResults();

        $response = json_decode($searchResults->getResponse());

        // Render the list
        return $this->renderListResults(__FUNCTION__, $subject, $response->photos->photo);
    }


    /**
     * Search videos.
     *
     * Choreos used in this method:
     * https://live.temboo.com/library/Library/YouTube/SearchVideos/
     *
     * @param string $subject the thing to search for, e.g. "cats"
     * @param int $limit (optional) max number of results
     * @return string a url to a rendered list of results, or an error message on failure
     * @throws Temboo_Exception should unforseen misfortunes befall us
     */
    protected function searchVideos($subject, $limit = false) {
        // Instantiate the Choreo, using a previously instantiated Temboo_Session object
        $searchVideos = new YouTube_SearchVideos($this->_tembooSession);

        // Get an input object for the Choreo
        $searchVideosInputs = $searchVideos->newInputs();

        // Set inputs
        $searchVideosInputs->setQuery($subject)->setResponseFormat("json");
        if($limit) {
            $searchVideosInputs->setMaxResults($limit);
        }

        // Execute Choreo and get results
        $searchVideosResults = $searchVideos->execute($searchVideosInputs)->getResults();

        $response = json_decode($searchVideosResults->getResponse());

        // Render the list
        return $this->renderListResults(__FUNCTION__, $subject, $response->feed->entry);
    }


    /**
     * Search tweets.
     *
     * Choreos used in this method:
     * https://live.temboo.com/library/Library/Twitter/Search/Tweets/
     *
     * @param string $subject the thing to search for, e.g. "cats"
     * @param int $limit (optional) max number of results
     * @return string a url to a rendered list of results, or an error message on failure
     * @throws Temboo_Exception should unforseen misfortunes befall us
     */
    protected function searchTweets($subject, $limit = false) {
        // Instantiate the Choreo, using a previously instantiated Temboo_Session object
        $tweets = new Twitter_Search_Tweets($this->_tembooSession);

        // Get an input object for the Choreo
        $tweetsInputs = $tweets->newInputs();

        // Set credential to use for execution
        $tweetsInputs->setCredential(TEMBOO_TWITTER_CREDENTIAL);

        // Set inputs
        $tweetsInputs->setQuery($subject);
        if($limit) {
            $tweetsInputs->setCount($limit);
        }

        // Execute Choreo and get results
        $tweetsResults = $tweets->execute($tweetsInputs)->getResults();

        $response = json_decode($tweetsResults->getResponse());

        // Render the list
        return $this->renderListResults(__FUNCTION__, $subject, $response->statuses);
    }


    /**
     * Create a file on Dropbox or S3 (in that order) and return a public URL
     *
     * Choreos used in this method:
     * https://live.temboo.com/library/Library/Dropbox/UploadFile/
     * https://live.temboo.com/library/Library/Dropbox/GetShareableLink/
     * https://live.temboo.com/library/Library/Amazon/S3/PutObject/
     *
     * @param string $filename filename to create
     * @param string $contents file contents to upload
     * @return string public url for file, or false if upload failed
     */
    protected function shareFile($filename, $contents) {
        if(defined('TEMBOO_DROPBOX_CREDENTIAL') && TEMBOO_DROPBOX_CREDENTIAL) {
            try {
                // Instantiate the Choreo, using a previously instantiated Temboo_Session object
                $uploadFile = new Dropbox_UploadFile($this->_tembooSession);

                // Get an input object for the Choreo
                $uploadFileInputs = $uploadFile->newInputs();

                // Set credential to use for execution
                $uploadFileInputs->setCredential(TEMBOO_DROPBOX_CREDENTIAL);

                // Set inputs
                $uploadFileInputs->setFileName($filename)->setFileContents(base64_encode($contents))->setResponseFormat('json');

                // Execute Choreo and get results
                $uploadFileResults = $uploadFile->execute($uploadFileInputs);


                // Instantiate the Choreo, using a previously instantiated Temboo_Session object
                $getShareableLink = new Dropbox_GetShareableLink($this->_tembooSession);

                // Get an input object for the Choreo
                $getShareableLinkInputs = $getShareableLink->newInputs();

                // Set credential to use for execution
                $getShareableLinkInputs->setCredential(TEMBOO_DROPBOX_CREDENTIAL);

                // Set inputs
                $getShareableLinkInputs->setPath($filename)->setResponseFormat('json');

                // Execute Choreo and get results
                $getShareableLinkResults = $getShareableLink->execute($getShareableLinkInputs)->getResults();

                $response = json_decode($getShareableLinkResults->getResponse());

                if(isset($response->url)) {
                    return str_replace('www.dropbox.com', 'dl.dropbox.com', $response->url);
                }

            } catch(Temboo_Exception $e) {
                error_log(__METHOD__ . ' failed with ' . get_class($e) . ': ' . $e->getMessage());
                // Do nothing, try again with S3 below.
            }
        }

        // Dropbox was unavailable or failed. Try S3...

        if(defined('TEMBOO_S3_CREDENTIAL') && TEMBOO_S3_CREDENTIAL && defined('MARTHA_S3_BUCKET') && MARTHA_S3_BUCKET) {
            try {
                // Instantiate the Choreo, using a previously instantiated Temboo_Session object
                $putObject = new Amazon_S3_PutObject($this->_tembooSession);

                // Get an input object for the Choreo
                $putObjectInputs = $putObject->newInputs();

                // Set credential to use for execution
                $putObjectInputs->setCredential(TEMBOO_S3_CREDENTIAL);

                // Set inputs
                $putObjectInputs->setBucketName(MARTHA_S3_BUCKET)->setFileName($filename)->setFileContents(base64_encode($contents));

                // Execute Choreo and get results
                $putObjectResults = $putObject->execute($putObjectInputs)->getResults();

                if(defined('MARTHA_URL') && MARTHA_URL) {
                    $url = MARTHA_URL;
                    if(!preg_match('/(\/|\.php)$/', $url)) {
                        $url = $url . '/';
                    }
                } else {
                    $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
                    $url = $protocol . $_SERVER['SERVER_NAME'] . preg_replace('/\/(query\/)?[a-z]+.php$/i', '/index.php', $_SERVER['REQUEST_URI']);
                }
                return $url . '?answer=' . urlencode($filename);

            } catch(Temboo_Exception $e) {
                error_log(__METHOD__ . ' failed with ' . get_class($e) . ': ' . $e->getMessage());
                // Do nothing, return false below.
            }
        }

        return false;
    }


    /**
     * Serve a file from S3. We route through Martha, so the S3 bucket need not be public.
     *
     * Choreos used in this method:
     * https://live.temboo.com/library/Library/Amazon/S3/GetBase64EncodedObject/
     *
     * @param string $filename filename to serve
     * @return string contents of file, or false if not found
     */
    public function serveFile($filename) {
        if(defined('TEMBOO_S3_CREDENTIAL') && TEMBOO_S3_CREDENTIAL && defined('MARTHA_S3_BUCKET') && MARTHA_S3_BUCKET) {
            try {
                // Instantiate the Choreo, using a previously instantiated Temboo_Session object;
                $getBase64EncodedObject = new Amazon_S3_GetBase64EncodedObject($this->_tembooSession);

                // Get an input object for the Choreo
                $getBase64EncodedObjectInputs = $getBase64EncodedObject->newInputs();

                // Set credential to use for execution
                $getBase64EncodedObjectInputs->setCredential(TEMBOO_S3_CREDENTIAL);

                // Set inputs
                $getBase64EncodedObjectInputs->setBucketName(MARTHA_S3_BUCKET)->setFileName($filename);

                // Execute Choreo and get results
                $getBase64EncodedObjectResults = $getBase64EncodedObject->execute($getBase64EncodedObjectInputs)->getResults();

                return base64_decode($getBase64EncodedObjectResults->getResponse());

            } catch(Temboo_Exception $e) {
                error_log(__METHOD__ . ' failed with ' . get_class($e) . ': ' . $e->getMessage());
                // Do nothing, return false below.
            }
        }
        return false;
    }


    /**
     * Shorten a URL, if possible.
     *
     * Choreos used in this method:
     * https://live.temboo.com/library/Library/Bitly/Links/ShortenURL/
     *
     * @param string $url a long url
     * @return string a short url, or the original if shortening failed
     */
    public function shortenUrl($url) {
        if(defined('TEMBOO_BITLY_CREDENTIAL') && TEMBOO_BITLY_CREDENTIAL) {
            try{
                // Instantiate the Choreo, using a previously instantiated Temboo_Session object
                $shortenURL = new Bitly_Links_ShortenURL($this->_tembooSession);

                // Get an input object for the Choreo
                $shortenURLInputs = $shortenURL->newInputs();

                // Set credential to use for execution
                $shortenURLInputs->setCredential(TEMBOO_BITLY_CREDENTIAL)->setResponseFormat('txt');

                // Set inputs
                $shortenURLInputs->setLongURL($url);

                // Execute Choreo and get results
                $shortenURLResults = $shortenURL->execute($shortenURLInputs)->getResults();

                return $shortenURLResults->getResponse();

            } catch(Temboo_Exception $e) {
                error_log(__METHOD__ . ' failed with ' . get_class($e) . ': ' . $e->getMessage());
                // Do nothing. Return original url below.
            }
        }

        return $url;
    }

    /**
     * Send an SMS text message.
     *
     * Choreos used in this method:
     * https://live.temboo.com/library/Library/Twilio/SendSMS/
     *
     * @param string $to a phone number to text
     * @param string $message the message to send
     * @throws Temboo_Exception should unforseen misfortunes befall us
     */
    public function sendSMS($to, $message) {
        // Instantiate the Choreo, using a previously instantiated Temboo_Session object
        $sendSMS = new Twilio_SendSMS($this->_tembooSession);

        // Get an input object for the Choreo
        $sendSMSInputs = $sendSMS->newInputs();

        // Set credential to use for execution
        $sendSMSInputs->setCredential(TEMBOO_TWILIO_CREDENTIAL);

        // Set inputs
        $sendSMSInputs->setBody($message)->setTo($to)->setFrom(TWILIO_SMS_NUMBER);

        // Execute Choreo, discard results
        $sendSMS->execute($sendSMSInputs);
    }


    /**
     * Render a list of a given resource (images, tweets, etc.) into a shareble web page.
     *
     * @param string $searchMethod the search method that called renderListResults, from which we can infer the resource type
     * @param string $subject the search terms use to find the items in the list ("cats")
     * @param array $items array of resources
     * @return string a url to a rendered list of results, or an error message on failure
     */
    protected function renderListResults($searchMethod, $subject, $items = array()) {

        // Pick a random alias of this resource type
        $type = $this->randomItem($this->_resourceTypes[$searchMethod]);
        if(count($items) != 1) {
            $type = $type . 's'; // And now we can never search for irregular nouns!
        }

        // If it's an empty list, just say sorry.
        if(count($items) < 1) {
            return $this->foundNone() . ' ' . $subject . ' ' . $type;
        }

        // Cheat to render the template to a string.
        ob_start();
        require('views/'.$searchMethod.'.php');
        $html = ob_get_clean();

        // Upload to Dropbox or S3
        $filename = uniqid('martha-'.$type.'s-', true) . '.html';
        $url = $this->shareFile($filename, $html);

        // If a page was successfully uploaded, link to it for full results (and shorter text messages!)
        if($url) {
            $url = $this->shortenUrl($url);
            return $this->foundSome() . ' ' . count($items). ' ' . $subject . ' ' . $type . ': ' . $url;
        }

        return $this->error();
    }


    /**
     * Queue a new message to send to the user.
     *
     * @param string $message the next message
     */
    public function say($message) {
        $this->_messages[] = $message;
    }


    /**
     * Get the list of messages to send to the user.
     *
     * @return array messages to the user
     */
    public function messages() {
        return $this->_messages;
    }


    /**
     * Parse a user's query, routing it to the right choreos, and queueing up responses.
     *
     * Problem: I am not an expert in natural language processing.
     * I know! I'll use regular expressions!
     * Now I have two problems.
     *
     * @param string $query a user's query
     */
    public function query($query) {

        if(strlen($query) < 1) {
            return $this->say($this->greet());
        }

        // Some basic scrubbing to make the bigger regexes below simpler...

        // Remove punctuation.
        $query = str_replace(array('.', ',', ':', '!', '?', '"', "'"), '', $query);

        // Compact all types and lengths of whitespace to single spaces. Kills newlines.
        $query = trim(preg_replace('/\s+/', ' ', $query));

        // Remove any superfluous addressing of the bot. Tolerates one word before or aft ("dear martha", "martha dear").
        $query = preg_replace('/^(\w+ )?martha\S? /i', '', $query);
        $query = preg_replace('/ martha\S?( \w+)$/i', '', $query);

        // Remove any other polite pre/postamble.
        $query = preg_replace('/^((please|kindly|pray|help|go|run|do|perform|will\ you|would\ you|can\ i\ have|may\ i\ have|
                                could\ you|can\ you|quickly|immediately|try\ to|try)(\ for)?(\ me|\ us)?(\ an?)?\ )*/ix', '', $query);
        $query = preg_replace('/(\ (please|right\ now|now|quickly|immediately|stat|thanks|thank\ you|for\ me|for\ us))*$/ix', '', $query);

        // Remove superfluous language specifying a search/request.
        $query = trim(preg_replace('/^((i\ )?(we\ )?(find|get|search|bring|show|list|display|fetch|query|locate|look|
                                want|need|gett)(you\ to|ing)?(\ for)?(\ me|\ us)?\ )*/ix', '', $query));


        // If nothing survived all that, just say hello.
        if(strlen($query) < 1) {
            return $this->say($this->greet());
        }

        // Check for a request to limit the number of results.
        $limit = false;

        // A nice simpler numeric quantity would be nice. Fortunately Twilio transcription does this!
        if(preg_match('/^(?P<limit>[0-9]+) (?P<query>.+)$/i', $query, $matches)) {
            $query = $matches['query'];
            $limit = (int) $matches['limit'];

        } else { // No? Fine. We'll do it the hard way.

            // What? "Some" is definitely, objectively equal to 5. Look it up.
            $limitAliases =  array('some' => 5, 'a few' => 3, 'a couple' => 2, 'many' => 20, 'several' => 20, 'all' => 100,
                'a bunch' => 15, 'any' => 10, 'an' => 1, 'a' => 1,
                'ten' => 10, 'eleven' => 11, 'twelve' => 12, 'thirteen' => 13, 'fourteen' => 14, 'fifteen' => 15,
                'sixteen' => 16, 'seventeen' => 17, 'eighteen' => 18, 'nineteen' => 19, 'twenty' => 20,
                'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 6, 'seven' => 7, 'eight' => 8, 'nine' => 9);

            $limitAliasesRegex = implode('|', array_keys($limitAliases));
            if(preg_match('/^(?P<limit>' . $limitAliasesRegex . ')( of)?( the)? (?P<query>.+)$/i', $query, $matches)) {
                $query = $matches['query'];
                $alias = strtolower($matches['limit']);
                $limit = $limitAliases[$alias];
            }
        }

        // Laziness, Impatience and Hubris.
        $resourceTypeRegex = implode('|',call_user_func_array('array_merge', $this->_resourceTypes));

        // Check for queries in form "<resource type> of/about/with <subject>", e.g. "pictures of cats".
        // or "<subject> <resource type>", e.g. "cat pictures".
        if(preg_match('/^(?P<resource>' . $resourceTypeRegex . ')s? ((about|for|of|showing|with|like|having) )+(?P<subject>.+)$/i', $query, $matches)
            ||preg_match('/^(?P<subject>.+) (?P<resource>' . $resourceTypeRegex . ')s?$/i', $query, $matches)) {
            $type = strtolower($matches['resource']);
            $subject = $matches['subject'];
            foreach($this->_resourceTypes as $searchMethod => $types) {
                if(in_array($type, $types)) {
                    try {
                        return $this->say(call_user_func(array($this, $searchMethod), $subject, $limit));
                    } catch(Temboo_Exception $e) {
                        error_log(__CLASS__ . '::' . $searchMethod . ' failed with ' . get_class($e) . ': ' . $e->getMessage());
                        return $this->say($this->error($e->getMessage()));
                    }
                }
            }
        }

        // Okay, not a search. Maybe some banter?
        if(preg_match('/^(hi|hello|hey|howdy|good (morning|afternoon|evening|day))/i', $query)) {
            return $this->say($this->greet());
        }
        if(preg_match('/^how (are|is|have)/i', $query)) {
            return $this->say($this->howAre());
        }

        // A plea for help?
        if(preg_match('/^(help|--help|what now|what can you|how do|you know|about|tell me about|what are you|ideas)/i', $query)) {
            return $this->help();
        }

        // Give up!
        $this->say($this->sorryDave());
    }


    /**
     * A random greeting.
     *
     * @return string randomly selected string
     */
    public function greet() {
        return $this->randomItem(
            'Hello! How can I help you?',
            'Martha at your service!',
            'Yes, this is dog. I mean, Martha.',
            'What are you looking for today?',
            'What can I do for you?'
        );
    }


    /**
     * A random acknowledgement of a request.
     *
     * @return string randomly selected string
     */
    public function okay() {
        return $this->randomItem(
            "Okay, I'm on it!",
            "I'll get right on that!",
            'You got it, boss.',
            'No problem!',
            'Sure thing.'
        );
    }


    /**
     * A random success report.
     *
     * @return string randomly selected string
     */
    public function foundSome() {
        return $this->randomItem(
            "You'll be pleased to know",
            'Good news!',
            'Ta-da.',
            'Woohoo,',
            'Here you go!',
            'Am I good or what?',
            'Yes!'
        )
        . ' '
        . $this->randomItem(
            'I found',
            'I located',
            'I have, just for you,',
            'I got you'
        );
    }

    /**
     * A random empty result report.
     *
     * @return string randomly selected string
     */
    public function foundNone() {
        return $this->randomItem(
            "Sorry, I couldn't find any",
            "That's odd, there don't seem to be any",
            'Sorry, the internet is fresh out of',
            'Alas, I failed to bring you your'
        );
    }

    /**
     * A random error report.
     *
     * @param string $message (optional) detailed error to append to random apology.
     * @return string randomly selected string
     */
    public function error($message = null) {
        $apology = $this->randomItem(
            'Sorry, something went wrong!',
            'Oops, I had an error.',
            'Oh dear, an error.',
            'Help, I need a debugger!'
        );
        return $message ? "$apology $message" : $apology;

    }


    /**
     * A random refusal for a bad or unknown request.
     *
     * @return string randomly selected string
     */
    public function sorryDave() {
        return $this->randomItem(
            "I'm sorry, Dave, I can't do that.",
            "I'm sorry, Dave. I can do that, but I just don't want to.",
            'Yeah, sorry. No.',
            'Nope. Better luck next time!',
            'Huh?'
        );
    }


    /**
     * A random response to garbled voice communication.
     *
     * @return string randomly selected string
     */
    public function cantHearYou() {
        return $this->randomItem(
            "Sorry, I didn't quite catch that.",
            'Can you repeat that?',
            'One more time, please?',
            'You want what now?'
        );
    }


    /**
     * A random upbeat report of how things (most likely Martha) are.
     *
     * @return string randomly selected string
     */
    public function howAre() {
        return $this->randomItem(
            "Super, thanks for asking.",
            'Pretty good.',
            'Just fine.',
            'Never better.',
            'Great!'
        );
    }


    /**
     * Usage suggestions and info about Martha.
     *
     * @return string randomly built string
     */
    public function help() {

        $this->say("I'm Martha and I can help you find things online! Try asking:");

        $searches = array('Find me', 'Search for', 'Get me', 'Run a search for', 'Look for', 'Find');
        shuffle($searches);
        $search1 = $searches[0];
        $search2 = $searches[1];

        $number1 = $this->randomItem('two', 'three', 'four', 'five', 'ten', 'twenty', 'some', 'all the');
        $number2 = rand(2,50);

        $randomTypes = $this->_resourceTypes;
        shuffle($randomTypes);
        $type1 = $this->randomItem($randomTypes[0]) . 's';
        $type2 = $this->randomItem($randomTypes[1]) . 's';

        $thing1 = $this->randomItem('cats', 'dogs', 'penguins', 'cake', 'robots', 'sunsets', 'New York', 'maps', 'beer');
        $thing2 = $this->randomItem('cat', 'dog', 'penguin', 'cake', 'robot', 'sunset', 'New York', 'map', 'beer');

        $this->say('"' . implode(' ', array($search1, $number1, $type1, $this->randomItem('of', 'about', 'with'), $thing1)) . '."');
        $this->say('"' . implode(' ', array($search2, $number2, $thing2, $type2)) . '."');

        $this->say("I can talk to all of these APIs thanks to Temboo! https://temboo.com");
        $this->say("You can read my source at https://github.com/temboo");
    }


    /**
     * Convenience method to pick random items from parameters or arrays
     *
     * @param mixed $item,... a single array, or multiple parameters of any type
     * @return mixed a single random item from the array or parameter list
     */
    protected function randomItem($item) {
        $args = func_get_args();
        if(count($args) == 1 && is_array($item)) {
            $args = $item;
        }
        $key = array_rand($args);
        return $args[$key];
    }

}

?>