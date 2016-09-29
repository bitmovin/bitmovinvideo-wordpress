=== Bitmovin Wordpress Plugin ===
Contributors: Lukas Kröpfl, Tristan Boyd, Patrick Struger
Donate link: http://bitmovin.com
Tags:
Requires at least: 4.5.3
Tested up to: 4.5.3
Stable tag: 4.5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Bitmovin Wordpress Plugin provides best video quality via HTML5 adaptive streaming
with fast startup, no buffering and without additional plugins!


== Description ==

The Bitmovin Wordpress Plugin comes up with a lot of features and services.

Bitmovin HTML5 Adaptive Player

The Bitmovin Adaptive Streaming Player is a highly optimised MPEG-DASH & HLS player for various platforms and devices,
delivering the best streaming performance and user experience, particular in adverse (mobile) network conditions.
bitdash™ is the result of continued R&D investments and incorporates patent pending technology resulting in MPEG-DASH compliant client solutions
that deliver up to 101 % higher effective media throughput as well as significantly higher Quality of Experience (QoE)
compared to existing adaptive bitrate streaming technologies and clients.

It also comes with a lot of features:

- DRM support
- Ad support
- VR and 360°
- Encoding Service (HLS or MPEG-DASH)
- Fastest loading times


Bitmovin Encoding Service

The Bitmovin Wordpress Plugin also encodes your videos into modern Adaptive Bitrate Streaming formats such as MPEG-DASH and HLS.
When uploading multimedia files via Wordpress, the Plugin automatically convert them via the Bitmovin Encoding service running in background into those formats.

The general workflow is a follows: Your input videos can be uploaded by the provided wordpress upload page and can the be
encoded with the Encoding Feature of the Bitmovin Plugin into MPEG-DASH and HLS formats used for streaming.
Bitmovin transfers the encoded asset back to your storage. From this storage you can directly deliver your videos to your customers.
These cutting edge adaptive streaming formats enable smooth playback without buffering and low startup delay at the highest possible quality.
Your input videos can be transfered to our cloud encoding service through various input protocols
such as HTTP or FTP servers, Google Cloud Storage, Amazon S3, Microsoft Azure, Aspera


VR and 360° Video and Adaptive Bitrate Streaming

The Bitmovin Wordpress Plugin utilizes the browser build-in HTML5 Media Source Extensions (MSE)
to playback VR and omnidirectional content natively through the browser decoding engine.
This playout technique is available on most modern web browsers.



== Installation ==

This section describes how to install the plugin and get it working.

1.  If not already done you firstly have to create an Bitmovin Account at https://bitmovin.com/signup-player/.
2.  Once logged in, go to the settings of your Bitmovin user account and get your API key (Do not confound it with your Player key).
3.  Do not forget to add the domain of your Wordpress website to the allowed domains in the player overview.
4.  Install the Bitmovin Player Wordpress Plugin through your Wordpress dashboard or upload via FTP and unzip the plugin archive into the wp-content/plugins directory.
5.  Activate the plugin through the Plugins menu in Wordpress.
6.  Go to the Bitmovin Settings page via in the main admin menu and fill in your API key.
7.  Go to the Bitmovin videos page via in the main admin menu and add a video with your configuration.
8.  Copy the shortcode from the videos page table, which looks like this [bitmovin_player id='1'], to your post.



== Frequently Asked Questions ==


= Why does the player show the error message "Your player is not allowed to play on the domain evil.com"? =

The player contacts our licensing server to detect if it is allowed to play on the current domain.
Browser extensions, like Allow-Control-Allow-Origin: * allows CORS requests without honoring CORS headers from the server, but in return changes the origin from the actual domain to evil.com.
Please disable/remove such extensions and the player should work as expected.


= I get the error message  "1002: Key or domain is invalid" =

The error is shown, because either the key you entered in the configuration is incorrect or
if you are using the URL you entered during the checkout process, is not valid or incorrect.
Non-valid URLs can for instance be caused by incorrect sub-domains, the URL you provided does not fully match the URL of the deployment,
or you entered an IP address instead of the domain name.


= I setup the Bitmovin player, but no video element is shown" =

This can have different reasons.
Please open the console of your web browser (e.g. by pressing F12 in Google Chrome or Mozilla Firefox),
which provides information in more detail on possible errors.


= Why do I see error messages in my browser's console? =

The error messages as shown in the image below may appear in a browser’s console.
This is not a bitmovin player error, but the errors are thrown from Google’s ChromeCast SDK if the Cast extensions can’t be found.
Unfortunately, there is currently no way to prevent these errors from happening or from showing up in the console, as explained by the developers in the related Google Cast SDK issue.
These errors do not prevent your website or the bitmovin player from working as expected. ChromeCast errors in browser’s console This affects pro and enterprise edition only..


= Why are the colors of my video not the same in different browsers? =

The bitmovin player relies on the browser’s capabilities to decode and render the video.
This has the advantage to be able to use hardware decoding which is much faster and also better for battery life / energy consumption than software decoding.
But different browsers may use different encoders with different settings.
This difference is especially visible between HTML5 and FLASH videos.
So unfortunately we can not change this behaviour.


= The player is not loading segments, altough the MPD is correct? =

If the player is not loading segments which lies on another server,
make sure CORS (for the HTML5 player) is enabled a crossdomain.xml
(for the Flash based version of bitdash) is placed on the server containing the element.


== Screenshots ==

1.  This screen shot description corresponds to plugin.png.
    This screen shot describes the Plugin site of the Wordpress instance.
    After you have installed the Bitmovin Player Wordpress Plugin through your Wordpress dashboard or
    uploaded via FTP and unzipped the plugin archive into the wp-content/plugins directory, you should now be able to
    see the Plugin in the list with the state "Deactivated". Next step is just to click on "Activate" to go ahead.

2.  This screen shot description corresponds to setting.png.
    This screen shot describes the "Bitmovin Settings" site in your Wordpress Dashboard after activating the plugin.
    Here you have to insert your Bitmovin API key which could be found under Settings of your Bitmovin Account and click
    on the "Save" button to full enjoy your player plugin. If you need additional help there is an information sign
    which is linked to the login of your bitmovin account.

3.  This screen shot description corresponds to all-videos.png.
    This screen shot describes the "Bitmovin > All Videos" page, where you can find all your configured videos.
    You can also see the shortcode which is used to reference to the player with the given video in your posts.

4.  This screen shot description corresponds to video-source.png.
    This screen shot describes the video page when you clicked on a individual video in the "Bitmovin > All Videos" page.
    As you can see you have the possibility to set or edit your video source by the given text inputs here.
    It is the same page as "Add new Video", so feel free to change or set links to videos with the right formats here.

5.  This screen shot description corresponds to video-features.png.
    This screen shot describes also the video page with the possibility to change your player version manually or to add
    Ads, change the style of the player, set DRM or to edit your VR settings. After clicking on "Update" at the right side
    above of the page, your changes are going to apply immediately.

6.  This screen shot description corresponds to player-preview.png.
    This screen shot describes the player preview at the bottom of the video page.
    If everything was done well, you are now able to see the preview of the player, which is also shown in your posts.
    Here you can see, if your settings matches with the player configuration and if your links to the streams are working as well.

7.  This screen shot description corresponds to bitmovin-post.png.
    This screen shot describes the implementation of the player in your post.
    Firstly you have to change to the page where you create and edit posts under wordpress.
    When adding a post you just have to copy the shortcut provided by your video e.g. [bitmovin_player id='4'] to the
    text area of your post editor.

8.  This screen shot description corresponds to player-post.png.
    This screen shot describes the correct implementation of your bitmovin player in your posts.
    After you correctly followed the instructions of the installation you should now be able to enjoy your streams in your posts.



== Changelog ==

= v1.0.1 =
* Minor bugfixes

= v0.5.1 =
* Implemented the possibility to manually change the player version depending on the channel.
* Implemented a tooltip at the "Bitmovin Settings" page to provide the right API key.
* Implemented "Advanced" tag when editing or adding a video to bitmovin player. Customer can now manually paste the link to the preferred player version


= v0.5.0 =
* Implemented beta version 0.5.0 of Bitmovin Wordpress Plugin.


== Upgrade Notice ==

= v0.5.1 =
With the newest upgrade you are now able to change to the player version you prefer.
There are also some minor changes in the code and some bugfixes and we also added more information for the customer.
It is strongly recommended to upgrade to the latest version of the plugin.

= v0.5.0 =
This is the first beta version of the bitmovin player plugin.

== A brief Markdown Example ==


* Video streaming with highest possible quality and low buffering time
* Bitmovin encoding service for MPEG-Dash and HLS
* VR and 360° video support


Here's a link to [Bitmovin](http://bitmovin.com/ "Video Infrastructure for the Web")
Our sales team is always available to take your call, so email us:
 *sales@bitmovin.com*