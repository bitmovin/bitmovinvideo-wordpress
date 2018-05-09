=== Bitmovin Wordpress Plugin ===
Contributors: Gernot Zwantschko, Lukas Kröpfl, Tristan Boyd, Patrick Struger
Tags: bitmovin, video, vr, ads, html5, dash, hls, mpeg-dash, mp4, smooth
Requires at least: 4.5.3
Tested up to: 4.9.5
Stable tag: 2.0.2
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
- VR / 360°
- HLS, MPEG-DASH, Smooth, progressive content (mp4, and more)
- Fastest loading times

VR and 360° Video and Adaptive Bitrate Streaming

The Bitmovin Wordpress Plugin utilizes the browser build-in HTML5 Media Source Extensions (MSE)
to playback VR and omnidirectional content natively through the browser decoding engine.
This playout technique is available on most modern web browsers.



== Installation ==

This section describes how to install the plugin and get it working.

1. Sign up for Bitmovin [here](https://dashboard.bitmovin.com/signup).
2. Once logged in, go to [account settings of your Bitmovin user account](https://dashboard.bitmovin.com/account), get your **API key**.
3. Do not forget to add the domain your Wordpress is running on to the allowed domains in the [player licenses view](https://dashboard.bitmovin.com/player/licenses).
4. Install Bitmovin Player Wordpress Plugin from your Wordpress dashboard or unzip the plugin archive in the `wp-content/plugins` directory.
5. Activate the plugin through the **Plugins** menu in Wordpress.
6. Go to the **Bitmovin Settings** left menu page and fill in your API key.
7. Go to the **Bitmovin** left menu videos page and add a video with your configuration.
8. Copy the shortcode from the videos page table which looks like this **[bitmovin_player id='1']** to your post.



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



== Changelog ==

= v2.0.2 =
* fixed display of selected player key
* added composer.json

= v2.0.1 =
* Plugin works with Bitmovin API now (IMPORTANT: it is no longer compatible with accounts that were created at app.bitmovin.com! Please see the installation instructions for more details)

= v0.6.1 =
* Fixed VR settings menu

= v0.6.0 =
* Added initial support to use our latest player versions as well
* Minor updates / improvements

= v0.5.1 =
* Implemented the possibility to manually change the player version depending on the channel.
* Implemented a tooltip at the "Bitmovin Settings" page to provide the right API key.
* Implemented "Advanced" tag when editing or adding a video to bitmovin player. Customer can now manually paste the link to the preferred player version


= v0.5.0 =
* Implemented beta version 0.5.0 of Bitmovin Wordpress Plugin.


== Upgrade Notice ==

= v0.6.1 =
Due to recent changes, after saving a new player config, invalid value for VR settings were saved.
If you experienced that, edit your player configuration, update your VR settings, and save it again. Then it will work as expected again.

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