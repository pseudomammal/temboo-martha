###About Martha
[@edd](https://twitter.com/edd) challenged @temboo to ["write an open source Siri implementation"](https://twitter.com/edd/status/297183175472476160).

Well, meet Martha! Using Temboo's [vast API library](https://live.temboo.com/library/) she can:

 * Take your requests via web, SMS, or voice with [Twilio](https://live.temboo.com/library/Library/Twilio/)
 * Find photos on [Flickr](https://live.temboo.com/library/Library/Flickr/)
 * Search for videos on [YouTube](https://live.temboo.com/library/Library/YouTube/)
 * Check statuses on [Twitter](https://live.temboo.com/library/Library/Twitter/)
 * Upload the results of her searches to [Dropbox](https://live.temboo.com/library/Library/Dropbox/) or [S3](https://live.temboo.com/library/Library/Amazon/S3/)
 * Shorten those URLs with [Bitly](https://live.temboo.com/library/Library/Bitly/), the better to txt them back to you with [Twilio](https://live.temboo.com/library/Library/Twilio/)

And that's just what I got done this weekend. You can add more services to her really easily.

In fact, you can generate most of the code necessary live in the [Temboo library](https://live.temboo.com/library/), then paste it in. Browse through `martha.php` and you'll find library links above every method. If you follow those links you'll find code snippets that look awfully familiar.

###Quickstart
 1. Sign up for a free account at www.temboo.com
 2. Clone the repo: `git clone git@github.com:pseudomammal/temboo-martha.git`
 3. Copy `config.php.template` to `config.php` and edit with your Temboo credentials. Follow the links in this file to establish credentials for each API.

###Why PHP? Why no framework? You call this a Siri?
Yeah, okay, I don't expect Siri will be fearing for her job any time soon. There's really not much to this but Temboo calls and some hairy regular expressions. But that's the point! Martha is here to show off the awesome power of Temboo (which definitely exists), not my Natural Language Processing expertise (which definitely does not). That said, if you happen to be a bored NLP domain expert, pull requests welcome!

###About Temboo
Temboo lets developers focus on what makes their software unique.

Our SDKs give you normalized access to 100+ APIs, databases, and more. Use Temboo if you want to spend less time wrestling with data and more time building what matters to you.

Learn more, and get the Temboo SDK, at www.temboo.com. Or you can watch a [screencast of my recent presentation](http://temboo.tumblr.com/post/44816556635/scaling-api-access-why-automation-will-make) at the [API Strategy Conference](http://www.apistrategyconference.com).

###Copyright and License
Copyright 2013, Temboo Inc.

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
