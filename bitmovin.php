<?php
/*
  Plugin Name: Bitmovin
  Plugin URI: http://bitmovin.com/wordpress-plugin
  Description: <strong>Bitmovin's</strong> encoding and adaptive streaming player in Wordpress
  Version: 0.5.1
  Author: Bitmovin
  Author URI: http://bitmovin.com
  License: GPLv2 or later
*/

require_once __DIR__.'/vendor/autoload.php';

register_activation_hook(__FILE__, 'bitmovin_plugin_activation');
function bitmovin_plugin_activation()
{

}

register_deactivation_hook(__FILE__, 'bitmovin_plugin_deactivation');
function bitmovin__plugin_deactivation()
{

}

add_action("admin_enqueue_scripts", "bitmovin_media_uploader");
function bitmovin_media_uploader()
{
    wp_enqueue_media();
}

add_action('admin_enqueue_scripts', 'bitmovin_admin_assets');
function bitmovin_admin_assets()
{
    wp_register_script('bitmovin_script', plugins_url('js/bitmovin.js', __FILE__));
    wp_enqueue_script('bitmovin_script');
    wp_localize_script( 'bitmovin_script', 'bitmovin_script', array( 'plugin_url' => plugins_url( 'bitmovin/'),
        'apiKey' => get_option('bitmovin_api_key')));

    wp_register_style('bitmovin_style', plugins_url('css/bitstyle.css', __FILE__));
    wp_enqueue_style('bitmovin_style');
}

add_action('init', 'bitmovin_register');
function bitmovin_register()
{
    $labels = array(
        'name' => __('Videos', 'bitmovin_player'),
        'singular_name' => __('Video', 'bitmovin_player'),
        'menu_name' => __('Bitmovin', 'bitmovin_player'),
        'add_new' => __('Add New Video', 'bitmovin_player'),
        'add_new_item' => __('Add New Video', 'bitmovin_player'),
        'new_item' => __('New Video', 'bitmovin_player'),
        'edit_item' => __('Edit Video', 'bitmovin_player'),
        'view_item' => __('View Video', 'bitmovin_player'),
        'all_items' => __('All Videos', 'bitmovin_player'),
        'search_items' => __('Search Videos', 'bitmovin_player')
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'description' => 'Video',
        'supports' => array('title'),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => false,
        'publicly_queryable' => true,
        'exclude_from_search' => true,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'menu_icon' => plugins_url('images/bitlogo.png', __FILE__)
    );

    register_post_type('bitmovin_player', $args);
}


add_filter('manage_edit-bitmovin_player_columns', 'bitmovin_player_columns');
function bitmovin_player_columns($columns)
{
    return $columns
    + array('bitmovin_player_shortcode' => __('Shortcode'));
}

add_action('manage_bitmovin_player_posts_custom_column', 'bitmovin_player_column', 10, 2);
function bitmovin_player_column($column, $post_id)
{
    switch ($column) {
        case 'bitmovin_player_shortcode':
            echo "[bitmovin_player id='$post_id'/]";
            break;
    }
}

add_action('add_meta_boxes', 'bitmovin_video_meta_box');
function bitmovin_video_meta_box()
{
    add_meta_box("bitmovin_player_configuration_encoding", "Upload/Encoding", 'bitmovin_player_configuration_encoding', "bitmovin_player", "normal", "high");
    add_meta_box("bitmovin_player_configuration_video", "Video", 'bitmovin_player_configuration_video', "bitmovin_player", "normal", "high");
    add_meta_box("bitmovin_player_configuration_player", "Player", 'bitmovin_player_configuration_player', "bitmovin_player", "normal", "high");
    add_meta_box("bitmovin_player_configuration_drm", "DRM", 'bitmovin_player_configuration_drm', "bitmovin_player", "normal", "high");
    add_meta_box("bitmovin_player_configuration_ads", "Ads", 'bitmovin_player_configuration_ads', "bitmovin_player", "normal", "high");
    add_meta_box("bitmovin_player_configuration_vr", "VR", 'bitmovin_player_configuration_vr', "bitmovin_player", "normal", "high");
    add_meta_box("bitmovin_player_configuration_style", "Style", 'bitmovin_player_configuration_style', "bitmovin_player", "normal", "high");
    add_meta_box("bitmovin_player_configuration_custom", "Custom", 'bitmovin_player_configuration_custom', "bitmovin_player", "normal", "high");
    add_meta_box("bitmovin_player_configuration_advanced", "Advanced", 'bitmovin_player_configuration_advanced', "bitmovin_player", "normal", "high");

    add_meta_box("bitmovin_player_preview", "Player Preview", 'bitmovin_player_preview', "bitmovin_player", "normal");
}

function bitmovin_player_configuration_encoding()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="encoding">';
    $html .= bitmovin_getEncodingTable($post->ID);
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_player_configuration_video()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="video">';
    $html .= bitmovin_getVideoTable($post->ID);
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_player_configuration_player()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="playerConfig">';
    $html .= bitmovin_getPlayerTable($post->ID);
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_player_configuration_advanced()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="video">';
    $html .= bitmovin_getAdvancedTable($post->ID);
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_player_configuration_drm()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="drm" class="configContent">';
    $html .= bitmovin_getDrmTable($post->ID);
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_player_configuration_ads()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="ads" class="configContent">';
    $html .= bitmovin_getAdsTable($post->ID);
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_player_configuration_vr()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="vr" class="configContent">';
    $html .= bitmovin_getVrTable($post->ID);
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_player_configuration_style()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="style" class="configContent">';
    $html .= bitmovin_getStyleTable($post->ID);
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_player_configuration_custom()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="custom" class="configContent">';
    $html .= bitmovin_getCustomTable($post->ID);
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_player_preview()
{
    global $post;
    $html = '<div>';
    $html .= '<p>To apply your changes in the config click "Update" on the right side menu. To include the player in a post just copy and paste this <strong>[bitmovin_player id=\'' . $post->ID . '\']</strong> shortcode to your post.</strong>';
    $html .= '<div style="width: 600px; margin: auto">';
    $html .= bitmovin_generate_player(array("id"=>$post->ID));
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_getEncodingTable($id)
{
    $encoding_profile = get_post_meta($id, "_config_encoding_profile", true);
    $video_width = get_post_meta($id, "_config_encoding_width", true);
    $video_height = get_post_meta($id, "_config_encoding_height", true);
    $video_bitrate = get_post_meta($id, "_config_encoding_video_bitrate", true);
    $audio_bitrate = get_post_meta($id, "_config_encoding_audio_bitrate", true);

    $encoding_video_src = get_post_meta($id, "_config_encoding_video_src", true);

    $ftp_server = get_post_meta($id, "_config_ftp_server", true);
    $ftp_usr = get_post_meta($id, "_config_ftp_usr", true);
    $ftp_pw = get_post_meta($id, "_config_ftp_pw", true);

    $aws_name = get_post_meta($id, "_config_s3_name", true);
    $bucket = get_post_meta($id, "_config_s3_bucket", true);
    $access_key = get_post_meta($id, "_config_s3_access_key", true);
    $secret_key = get_post_meta($id, "_config_s3_secret_key", true);

    $encodingTable = '<table class="wp-list-table widefat fixed striped">';
    $encodingTable .= "<tr><td colspan='2'>Encoding Configuration</td></tr>";

    $encodingTable .= "<tr><td colspan='2'>General</td></tr>";
    $encodingTable .= getTableRowInput("Encoding Profile", "config_encoding_profile", $encoding_profile, "My first Wordpress Encoding Profile");
    $encodingTable .= getTableRowInput("Video Height", "config_encoding_height", $video_height, "e.g. 720");
    $encodingTable .= getTableRowInput("Video Width", "config_encoding_width", $video_width, "e.g. 1280");
    $encodingTable .= getTableRowInput("Video Bitrate", "config_encoding_video_bitrate", $video_bitrate, "e.g. 1024000 Bits/s");
    $encodingTable .= getTableRowInput("Audio Bitrate", "config_encoding_audio_bitrate", $audio_bitrate, "e.g. 256000 Bits/s");

    $encodingTable .= "<tr><td colspan='2'>Video Source</td></tr>";
    $encodingTable .= getTableRowInput("Video URL", "config_encoding_video_src", $encoding_video_src, "http://localhost/wordpress/wp-content/uploads/video.mkv");

    $encodingTable .= "<tr><td colspan='2'>Output</td></tr>";
    $encodingTable .= "<tr><td>";
    $encodingTable .= "<form>";
    $encodingTable .= getTableRowRadio("FTP", "config_encoding_output_ftp", "ftp");
    $encodingTable .= getTableRowRadio("AWS", "config_encoding_output_s3", "s3");
    $encodingTable .= "</form>";
    $encodingTable .= "</td></tr>";

    $encodingTable .= "<tr><td colspan='2'>FTP Configuration</td></tr>";
    $encodingTable .= getTableRowInput("FTP Server", "config_ftp_server", $ftp_server, "ftp://path/to/upload/directory/myEncodedVideo");
    $encodingTable .= getTableRowInput("FTP Username", "config_ftp_usr", $ftp_usr, "FTP Username");
    $encodingTable .= getTableRowPWInput("FTP Password", "config_ftp_pw", $ftp_pw, "FTP Password");

    $encodingTable .= "<tr><td colspan='2'>AWS (Amazon Web Services) Configuration</td></tr>";
    $encodingTable .= getTableRowInput("Name", "config_s3_name", $aws_name, "Your AWS Output Name");
    $encodingTable .= getTableRowInput("Access Key", "config_s3_access_key", $access_key, "yourAWSAccessKey");
    $encodingTable .= getTableRowPWInput("Secret Key", "config_s3_secret_key", $secret_key, "yourAWSSecretKey");
    $encodingTable .= getTableRowInput("Bucket", "config_s3_bucket", $bucket, "yourBucketName");

    //class="button button-primary button-large"
    $encodingTable .= '<tr><td></td></tr><button id="insert-media-button" class="button insert-media add_media" type="button" onclick="open_media_encoding_video()" data-editor="content">Select Video from Mediathek</button></td></tr>';
    $encodingTable .= '<tr><td><button id="bEncode" class="button insert-media add_media" name="bEncode">Encode Uploaded Video</button></td>';
    $encodingTable .= '<td><div id="response"></div></td>';
    $encodingTable .= '</tr>';

    $encodingTable .= "</table>";

    return $encodingTable;
}

function bitmovin_getVideoTable($id)
{
    $dash_url = get_post_meta($id, "_config_src_dash", true);
    $hls_url = get_post_meta($id, "_config_src_hls", true);
    $prog_url = get_post_meta($id, "_config_src_prog", true);
    $poster_url = get_post_meta($id, "_config_src_poster", true);

    $videoTable = '<table class="wp-list-table widefat fixed striped">';
    $videoTable .= "<tr><td colspan='2'>Video Configuration<a href='https://bitmovin.com/player-documentation/player-configuration/#Source' target='_blank'>Documentation</a></td></tr>";

    $videoTable .= getTableRowInput("Dash URL", "config_src_dash", $dash_url, "http://path/to/mpd/file.mpd");
    $videoTable .= getTableRowInput("HLS URL", "config_src_hls", $hls_url, "http://path/to/hls/playlist/file.m3u8");
    $videoTable .= getTableRowInput("Progressive URL", "config_src_prog", $prog_url, "http://path/to/mp4");
    $videoTable .= getTableRowInput("Poster URL", "config_src_poster", $poster_url, "http://path/to/poster.jpg");

    $videoTable .= '<tr><td></td></tr><button id="insert-media-button" class="button insert-media add_media" type="button" onclick="open_media_progressive_video()" data-editor="content">Select Video from Mediathek</button></td></tr>';

    $videoTable .= "</table>";

    return $videoTable;
}

function bitmovin_getPlayerTable($id)
{
    $player_channel = get_post_meta($id, "_config_player_channel", true);
    $player_version = get_post_meta($id, "_config_player_version", true);

    $playerTable = '<table class="wp-list-table widefat fixed striped">';
    $playerTable .= "<tr><td colspan='2'>Player Channels/Versions</td></tr>";
    $playerTable .= getTableRowSelect("Channel", "config_player_channel", $player_channel, array(""));
    $playerTable .= getTableRowSelect("Version", "config_player_version", $player_version, array(""));
    $playerTable .= "</table>";

    return $playerTable;
}

function bitmovin_getDrmTable($id)
{
    $widevine_la_url = get_post_meta($id, "_config_src_drm_widevine_la_url", true);
    $playready_la_url = get_post_meta($id, "_config_src_drm_playready_la_url", true);
    $playready_customData = get_post_meta($id, "_config_src_drm_playready_customData", true);
    $access_la_url = get_post_meta($id, "_config_src_drm_access_la_url", true);
    $access_authToken = get_post_meta($id, "_config_src_drm_access_authToken", true);
    $primetime_la_url = get_post_meta($id, "_config_src_drm_primetime_la_url", true);
    $primetime_indivURL = get_post_meta($id, "_config_src_drm_primetime_indivURL", true);
    $fairplay_la_url = get_post_meta($id, "_config_src_drm_fairplay_la_url", true);
    $fairplay_certificateURL = get_post_meta($id, "_config_src_drm_fairplay_certificateURL", true);

    $drmTable = "<table class='wp-list-table widefat fixed striped'>";
    $drmTable .= "<tr><td colspan='2'>DRM Configuration<a href='https://bitmovin.com/player-documentation/player-configuration/#DRM' target='_blank'>Documentation</a></td></tr>";

    $drmTable .= "<tr><td colspan='2'>Widevine</td></tr>";
    $drmTable .= getTableRowInput("LA_URL", "config_src_drm_widevine_la_url", $widevine_la_url, "https://mywidevine.licenseserver.com/");

    $drmTable .= "<tr><td colspan='2'>Playready</td></tr>";
    $drmTable .= getTableRowInput("LA_URL", "config_src_drm_playready_la_url", $playready_la_url, "https://myplayready.licenseserver.com/");
    $drmTable .= getTableRowInput("customData", "config_src_drm_playready_customData", $playready_customData);

    $drmTable .= "<tr><td colspan='2'>Access</td></tr>";
    $drmTable .= getTableRowInput("LA_URL", "config_src_drm_access_la_url", $access_la_url, "https://myaccess.licenseserver.com/");
    $drmTable .= getTableRowInput("authToken", "config_src_drm_access_authToken", $access_authToken, "YOUR-BASE64-ENCODED-AUTH-TOKEN");

    $drmTable .= "<tr><td colspan='2'>Primetime</td></tr>";
    $drmTable .= getTableRowInput("LA_URL", "config_src_drm_primetime_la_url", $primetime_la_url, "https://myprimetime.licenseserver.com/");
    $drmTable .= getTableRowInput("indivURL", "config_src_drm_primetime_indivURL", $primetime_indivURL);

    $drmTable .= "<tr><td colspan='2'>Fairplay</td></tr>";
    $drmTable .= getTableRowInput("LA_URL", "config_src_drm_fairplay_la_url", $fairplay_la_url, "https://fairplay.licenseserver.com/");
    $drmTable .= getTableRowInput("certificateUrl", "config_src_drm_fairplay_certificateURL", $fairplay_certificateURL, "https://fairplay.licenseserver.com/certificate-url");

    $drmTable .= "</table>";
    return $drmTable;
}

function bitmovin_getAdsTable($id)
{
    $client = get_post_meta($id, "_config_advertising_client", true);
    $admessage = get_post_meta($id, "_config_advertising_admessage", true);

    $schedule1Offset = get_post_meta($id, "_config_advertising_schedule1_offset", true);
    $schedule1Tag = get_post_meta($id, "_config_advertising_schedule1_tag", true);

    $schedule2Offset = get_post_meta($id, "_config_advertising_schedule2_offset", true);
    $schedule2Tag = get_post_meta($id, "_config_advertising_schedule2_tag", true);

    $schedule3Offset = get_post_meta($id, "_config_advertising_schedule3_offset", true);
    $schedule3Tag = get_post_meta($id, "_config_advertising_schedule3_tag", true);

    $schedule4Offset = get_post_meta($id, "_config_advertising_schedule4_offset", true);
    $schedule4Tag = get_post_meta($id, "_config_advertising_schedule4_tag", true);

    $adsTable = "<table class='wp-list-table widefat fixed striped'>";
    $adsTable .= "<tr><td colspan='2'>Ads Configuration<a href='https://bitmovin.com/player-documentation/player-configuration/#Advertising_8211_VAST' target='_blank'>Documentation</a></td></tr>";

    $adsTable .= getTableRowInput("Client", "config_advertising_client", $client);
    $adsTable .= getTableRowInput("Ad message", "config_advertising_admessage", $admessage);

    $adsTable .= "<tr><td colspan='2'>Schedule 1</td></tr>";
    $adsTable .= getTableRowInput("Offset", "config_advertising_schedule1_offset", $schedule1Offset);
    $adsTable .= getTableRowInput("Tag", "config_advertising_schedule1_tag", $schedule1Tag);
    $adsTable .= "<tr><td colspan='2'>Schedule 2</td></tr>";
    $adsTable .= getTableRowInput("Offset", "config_advertising_schedule2_offset", $schedule2Offset);
    $adsTable .= getTableRowInput("Tag", "config_advertising_schedule2_tag", $schedule2Tag);
    $adsTable .= "<tr><td colspan='2'>Schedule 3</td></tr>";
    $adsTable .= getTableRowInput("Offset", "config_advertising_schedule3_offset", $schedule3Offset);
    $adsTable .= getTableRowInput("Tag", "config_advertising_schedule3_tag", $schedule3Tag);
    $adsTable .= "<tr><td colspan='2'>Schedule 4</td></tr>";
    $adsTable .= getTableRowInput("Offset", "config_advertising_schedule4_offset", $schedule4Offset);
    $adsTable .= getTableRowInput("Tag", "config_advertising_schedule4_tag", $schedule4Tag);

    $adsTable .= "</table>";

    return $adsTable;
}

function bitmovin_getVrTable($id)
{
    $startupMode = get_post_meta($id, "_config_src_vr_startupMode", true);
    $startPosition = get_post_meta($id, "_config_src_vr_startPosition", true);
    $initialRotation = get_post_meta($id, "_config_src_vr_initialRotation", true);
    $initialRotateRate = get_post_meta($id, "_config_src_vr_initialRotateRate", true);

    $vrTable = "<table class='wp-list-table widefat fixed striped'>";
    $vrTable .= "<tr><td colspan='2'>Vr Configuration<a href='https://bitmovin.com/player-documentation/player-configuration/#VR_and_360_Video' target='_blank'>Documentation</a></td></tr>";

    $vrTable .= getTableRowSelect("Startup mode", "config_src_vr_startupMode", $startupMode, array("disabled", "2d", "stereo-2d", "3d", "stereo-3d", "no-vr"));
    $vrTable .= getTableRowInputNumber("Start position", "config_src_vr_startPosition", $startPosition, 180);
    $vrTable .= getTableRowSelect("Initial rotation", "config_src_vr_initialRotation", $initialRotation, array("disabled", "true"));
    $vrTable .= getTableRowInputNumber("Initial rotation rate", "config_src_vr_initialRotateRate", $initialRotateRate, 0.025);

    $vrTable .= "</table>";

    return $vrTable;
}

function bitmovin_getStyleTable($id)
{
    $width = get_post_meta($id, "_config_style_width", true);
    $height = get_post_meta($id, "_config_style_height", true);
    $aspectRatio = get_post_meta($id, "_config_style_aspectRatio", true);

    $styleTable = "<table class='wp-list-table widefat fixed striped'>";
    $styleTable .= "<tr><td colspan='2'>Style Configuration<a href='https://bitmovin.com/player-documentation/player-configuration/#Style' target='_blank'>Documentation</a></td></tr>";

    $styleTable .= getTableRowInput("Width", "config_style_width", $width, "100%");
    $styleTable .= getTableRowInput("Height", "config_style_height", $height, "100%");
    $styleTable .= getTableRowInput("Aspect ratio", "config_style_aspectRatio", $aspectRatio, "16:9");

    $styleTable .= "</table>";

    return $styleTable;
}

function bitmovin_getCustomTable($id)
{
    $customConf = json_decode(get_post_meta($id, "_config_custom_conf", true));
    $customSource = json_decode(get_post_meta($id, "_config_custom_source", true));

    $customTable = "<table class='wp-list-table widefat fixed striped'>";
    $customTable .= "<tr><td colspan='2'>Custom Configuration</td></tr>";

    $customTable .= "<tr><td>Appended to configuration</td><td><pre>var conf = {<br><div class='intend1'>...<br>...<br><textarea id='config_custom' name='config_custom_conf'>" . $customConf . "</textarea></div>};</pre></td></tr>";
    $customTable .= "<tr><td>Appended to configuration -> source</td><td><pre>var conf = {<br><div class='intend1'>source: {<div class='intend1'>...<br>...<br><textarea id='config_custom_source' name='config_custom_source'>" . $customSource . "</textarea></div>},<br>...<br>...</div>};</pre></td></tr>";
    $customTable .= "<tr><td colspan='2' class='hint'>Make sure you start your custom configuration with an ','</td></tr>";

    $customTable .= "</table>";

    return $customTable;
}

function bitmovin_getAdvancedTable($id)
{
    $version_link = get_post_meta($id, "_config_version_link", true);

    $advancedTable = "<table class='wp-list-table widefat fixed striped'>";
    $advancedTable = "<tr><td><p></p>To provide our users the right version of our player, we have four public player channels available.
    In order of latest stable to most stable, we offer the Developer Channel, the Beta Channel, the Staging Channel, and finally the Stable Channel (default for every account).
    More information about the different channels and their meaning can be found in our <a href='https://bitmovin.com/player-documentation/release-channels/'>support section</a>.</td></tr>";
    $advancedTable .= getTableRowInput("", "config_version_link", $version_link, "https://bitmovin-a.akamaihd.net/bitmovin-player/channel/version/bitdash.min.js");
    $advancedTable .= "</table>";

    return $advancedTable;
}

function getTableRowInput($propertyDisplayName, $propertyName, $propertyValue, $placeHolder = "")
{
    return "<tr><th>" . $propertyDisplayName . "</th><td><input id='" . $propertyName . "' name='" . $propertyName . "' type='text' value='" . json_decode($propertyValue) . "' placeholder='" . $placeHolder . "'/></td></tr>";
}

function getTableRowPWInput($propertyDisplayName, $propertyName, $propertyValue, $placeHolder = "")
{
    return "<tr><th>" . $propertyDisplayName . "</th><td><input id='" . $propertyName . "' name='" . $propertyName . "' type='password' value='" . json_decode($propertyValue) . "' placeholder='" . $placeHolder . "'/></td></tr>";
}

function getTableRowInputNumber($propertyDisplayName, $propertyName, $propertyValue, $placeHolder = "")
{
    return "<tr><th>" . $propertyDisplayName . "</th><td><input id='" . $propertyName . "' name='" . $propertyName . "' type='number' value='" . json_decode($propertyValue) . "' placeholder='". $placeHolder . "' step='any'/></td></tr>";
}

function getTableRowRadio($propertyDisplayName, $propertyName, $propertyValue)
{
    if ($propertyValue == "ftp")
    {
        return "<input type='radio' id='{$propertyName}' name='output' value='{$propertyValue}' onclick='checkOutputChoice()' checked>{$propertyDisplayName}<br><br>";
    }
    else
    {
        return "<input type='radio' id='{$propertyName}' name='output' value='{$propertyValue}' onclick='checkOutputChoice()'>{$propertyDisplayName}<br><br>";
    }
}

function getTableRowSelect($propertyDisplayName, $propertyName, $selectedOption, $options)
{
    $selectedOption = json_decode($selectedOption);

    if ($propertyDisplayName == "Channel")
    {
        $tableRowSelect = "<tr><th>" . $propertyDisplayName . "</th><td><select id='" . $propertyName . "' onchange='getVersions()' name='" . $propertyName . "'>";
    }
    else
    {
        $tableRowSelect = "<tr><th>" . $propertyDisplayName . "</th><td><select id='" . $propertyName . "' name='" . $propertyName . "'>";
    }

    foreach($options as $option) {
        if ($option != "") {
            $tableRowSelect .= "<option value='" . $option . "'" . (($option == $selectedOption) ? "selected=\"selected\"" : "") . ">" . $option . "</option>";
        }
    }

    $tableRowSelect .= "</select></td></tr>";

    return $tableRowSelect;
}

add_action('save_post', 'bitmovin_player_save_configuration');
function bitmovin_player_save_configuration($post_id)
{
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // check permissions
    if ('bitmovin_player' == $_POST['post_type'] && current_user_can('edit_post', $post_id)) {

        $encoding_profile = bitmovin_getParameter("config_encoding_profile");
        $video_width = bitmovin_getParameter("config_encoding_width");
        $video_height = bitmovin_getParameter("config_encoding_height");
        $video_bitrate = bitmovin_getParameter("config_encoding_video_bitrate");
        $audio_bitrate = bitmovin_getParameter("config_encoding_audio_bitrate");

        $encoding_video_src = bitmovin_getParameter("config_encoding_video_src");

        $ftp_server = bitmovin_getParameter("config_ftp_server");
        $ftp_usr = bitmovin_getParameter("config_ftp_usr");
        $ftp_pw = bitmovin_getParameter("config_ftp_pw");

        $aws_name = bitmovin_getParameter("config_s3_name");
        $bucket = bitmovin_getParameter("config_s3_bucket");
        $access_key = bitmovin_getParameter("config_s3_access_key");
        $secret_key = bitmovin_getParameter("config_s3_secret_key");

        update_post_meta($post_id, "_config_encoding_profile", $encoding_profile);
        update_post_meta($post_id, "_config_encoding_width", $video_width);
        update_post_meta($post_id, "_config_encoding_height", $video_height);
        update_post_meta($post_id, "_config_encoding_video_bitrate", $video_bitrate);
        update_post_meta($post_id, "_config_encoding_audio_bitrate", $audio_bitrate);

        update_post_meta($post_id, "_config_encoding_video_src", $encoding_video_src);

        update_post_meta($post_id, "_config_ftp_server", $ftp_server);
        update_post_meta($post_id, "_config_ftp_usr", $ftp_usr);
        update_post_meta($post_id, "_config_ftp_pw", $ftp_pw);

        update_post_meta($post_id, "_config_s3_name", $aws_name);
        update_post_meta($post_id, "_config_s3_bucket", $bucket);
        update_post_meta($post_id, "_config_s3_access_key", $access_key);
        update_post_meta($post_id, "_config_s3_secret_key", $secret_key);

        $dash_url = bitmovin_getParameter("config_src_dash");
        $hls_url = bitmovin_getParameter("config_src_hls");
        $prog_url = bitmovin_getParameter("config_src_prog");
        $poster_url = bitmovin_getParameter("config_src_poster");

        update_post_meta($post_id, "_config_src_dash", $dash_url);
        update_post_meta($post_id, "_config_src_hls", $hls_url);
        update_post_meta($post_id, "_config_src_prog", $prog_url);
        update_post_meta($post_id, "_config_src_poster", $poster_url);

        $player_channel = bitmovin_getParameter("config_player_channel");
        $player_version = bitmovin_getParameter("config_player_version");

        update_post_meta($post_id, "_config_player_channel", $player_channel);
        update_post_meta($post_id, "_config_player_version", $player_version);

        $version_link = bitmovin_getParameter("config_version_link");

        update_post_meta($post_id, "_config_version_link", $version_link);

        $widevine_la_url = bitmovin_getParameter("config_src_drm_widevine_la_url");
        $playready_la_url = bitmovin_getParameter("config_src_drm_playready_la_url");
        $playready_customData = bitmovin_getParameter("config_src_drm_playready_customData");
        $access_la_url = bitmovin_getParameter("config_src_drm_access_la_url");
        $access_authToken = bitmovin_getParameter("config_src_drm_access_authToken");
        $primetime_la_url = bitmovin_getParameter("config_src_drm_primetime_la_url");
        $primetime_indivURL = bitmovin_getParameter("config_src_drm_primetime_indivURL");
        $fairplay_la_url = bitmovin_getParameter("config_src_drm_fairplay_la_url");
        $fairplay_certificateURL = bitmovin_getParameter("config_src_drm_fairplay_certificateURL");

        update_post_meta($post_id, "_config_src_drm_widevine_la_url", $widevine_la_url);
        update_post_meta($post_id, "_config_src_drm_playready_la_url", $playready_la_url);
        update_post_meta($post_id, "_config_src_drm_playready_customData", $playready_customData);
        update_post_meta($post_id, "_config_src_drm_access_la_url", $access_la_url);
        update_post_meta($post_id, "_config_src_drm_access_authToken", $access_authToken);
        update_post_meta($post_id, "_config_src_drm_primetime_la_url", $primetime_la_url);
        update_post_meta($post_id, "_config_src_drm_primetime_indivURL", $primetime_indivURL);
        update_post_meta($post_id, "_config_src_drm_fairplay_la_url", $fairplay_la_url);
        update_post_meta($post_id, "_config_src_drm_fairplay_certificateURL", $fairplay_certificateURL);

        $client = bitmovin_getParameter("config_advertising_client");
        $admessage = bitmovin_getParameter("config_advertising_admessage");
        $schedule1Offset = bitmovin_getParameter("config_advertising_schedule1_offset");
        $schedule1Tag = bitmovin_getParameter("config_advertising_schedule1_tag");
        $schedule2Offset = bitmovin_getParameter("config_advertising_schedule2_offset");
        $schedule2Tag = bitmovin_getParameter("config_advertising_schedule2_tag");
        $schedule3Offset = bitmovin_getParameter("config_advertising_schedule3_offset");
        $schedule3Tag = bitmovin_getParameter("config_advertising_schedule3_tag");
        $schedule4Offset = bitmovin_getParameter("config_advertising_schedule4_offset");
        $schedule4Tag = bitmovin_getParameter("config_advertising_schedule4_tag");

        update_post_meta($post_id, "_config_advertising_client", $client);
        update_post_meta($post_id, "_config_advertising_admessage", $admessage);
        update_post_meta($post_id, "_config_advertising_schedule1_offset", $schedule1Offset);
        update_post_meta($post_id, "_config_advertising_schedule1_tag", $schedule1Tag);
        update_post_meta($post_id, "_config_advertising_schedule2_offset", $schedule2Offset);
        update_post_meta($post_id, "_config_advertising_schedule2_tag", $schedule2Tag);
        update_post_meta($post_id, "_config_advertising_schedule3_offset", $schedule3Offset);
        update_post_meta($post_id, "_config_advertising_schedule3_tag", $schedule3Tag);
        update_post_meta($post_id, "_config_advertising_schedule4_offset", $schedule4Offset);
        update_post_meta($post_id, "_config_advertising_schedule4_tag", $schedule4Tag);


        $startupMode = bitmovin_getParameter("config_src_vr_startupMode");
        $startPosition = bitmovin_getParameter("config_src_vr_startPosition");
        $initialRotation = bitmovin_getParameter("config_src_vr_initialRotation");
        $initialRotateRate = bitmovin_getParameter("config_src_vr_initialRotateRate");

        update_post_meta($post_id, "_config_src_vr_startupMode", $startupMode);
        update_post_meta($post_id, "_config_src_vr_startPosition", $startPosition);
        update_post_meta($post_id, "_config_src_vr_initialRotation", $initialRotation);
        update_post_meta($post_id, "_config_src_vr_initialRotateRate", $initialRotateRate);

        $width = bitmovin_getParameter("config_style_width");
        $height = bitmovin_getParameter("config_style_height");
        $aspectRatio = bitmovin_getParameter("config_style_aspectRatio");

        update_post_meta($post_id, "_config_style_width", $width);
        update_post_meta($post_id, "_config_style_height", $height);
        update_post_meta($post_id, "_config_style_aspectRatio", $aspectRatio);

        $customSource = bitmovin_getParameter("config_custom_source");
        $customConf = bitmovin_getParameter("config_custom_conf");

        update_post_meta($post_id, "_config_custom_source", $customSource);
        update_post_meta($post_id, "_config_custom_conf", $customConf);

    } else {
        return $post_id;
    }
}

function bitmovin_bitmovin_getParameter($param)
{
    $param = (isset($_POST[$param]) ? $_POST[$param] : '');
    return strip_tags(json_encode($param));
}

add_shortcode("bitmovin_player", "bitmovin_generate_player");
function bitmovin_generate_player($id)
{
    extract(shortcode_atts(array(
        'id' => ''
    ), $id));

    $playerKey = get_option('bitmovin_player_key');
    if($playerKey == "") {
        return "<pre>No correct api key set in Bitmovin Settings.</pre>";
    }

    /* use advanced config before using player config */
    $advancedConfig = bm_getAdvancedConfig($id);
    if ($advancedConfig == 0)
    {
        bm_getPlayerConfig($id);
    }

    $html = "<div id='bitmovin-player'></div>\n";

    $html .= "<script type='text/javascript'>\n";
    $html .= "window.onload = function() {\n";
    $html .= "var player = bitdash('bitmovin-player');\n";
    $html .= "var conf = {\n";
    $html .= "key: '" . $playerKey ."',\n";
    $html .= "source: {\n";
    $html .= bm_getVideoConfig($id);

    $drm = bm_getDrmConfig($id);
    if ($drm != "") {
        $html .= ",drm: " . $drm . "\n";
    }

    $vr = bm_getVrConfig($id);
    if ($vr != "") {
        $html .= ",vr: " . $vr . "\n";
    }

    $custom = json_decode(get_post_meta( $id, "_config_custom_source", true ));
    if ( $custom != "" ) {
        $html .= $custom;
    }

    $html .= "}\n";

    $ads = bm_getAdsConfig($id);
    if ($ads != "") {
        $html .= ",advertising: " . $ads . "\n";
    }

    $style = bm_getStyleConfig($id);
    if ($style != "") {
        $html .= ",style: " . $style . "\n";
    }

    $custom = json_decode(get_post_meta( $id, "_config_custom_conf", true ));
    if ( $custom != "" ) {
        $html .= $custom;
    }

    $html .= "};\n";

    $html .= "player.setup(conf).then(function(value) {\n";
    $html .= "console.log('Successfully created bitdash player instance'); console.log('Player Version: ' + player.getVersion());\n";
    $html .= "}, function(reason) {\n";
    $html .= "console.log('Error while creating bitdash player instance');\n";
    $html .= "});\n";

    $html .= "};";
    $html .= "</script>\n";

    return $html;
}

function bm_getVideoConfig($id) {
    $dash = json_decode(get_post_meta($id, "_config_src_dash", true));
    $hls = json_decode(get_post_meta($id, "_config_src_hls", true));
    $prog = json_decode(get_post_meta($id, "_config_src_prog", true));
    $poster = json_decode(get_post_meta($id, "_config_src_poster", true));

    $video = "";
    $hasElementBefore = false;

    if($dash != "") {
        $video .= "dash: '" . $dash . "'";
        $hasElementBefore = true;
    }
    if($hls != "") {
        if ($hasElementBefore) {
            $video .= ",";
        }
        $video .= "hls: '" . $hls . "'";
        $hasElementBefore = true;
    }
    if($prog != "") {
        if ($hasElementBefore) {
            $video .= ",";
        }
        $video .= "progressive: '" . $prog . "'";
        $hasElementBefore = true;
    }
    if($poster != "") {
        if ($hasElementBefore) {
            $video .= ",";
        }
        $video .= "poster: '" . $poster . "'";
    }

    return $video;
}

function bm_getPlayerConfig($id)
{
    $player_channel = json_decode(get_post_meta($id, "_config_player_channel", true));
    $player_version = json_decode(get_post_meta($id, "_config_player_version", true));

    $player_channel = strtolower($player_channel);

    if ($player_version == 'Latest Version 5')
    {
        $src = "https://bitmovin-a.akamaihd.net/bitmovin-player/{$player_channel}/5/bitdash.min.js";
    }
    else if ($player_version == 'Latest Version 4')
    {
        $src = "https://bitmovin-a.akamaihd.net/bitmovin-player/{$player_channel}/4/bitdash.min.js";
    }
    else {
        $src = "https://bitmovin-a.akamaihd.net/bitmovin-player/{$player_channel}/{$player_version}/bitdash.min.js";
    }
    wp_register_script('bitmovin_player_core', $src);
    wp_enqueue_script('bitmovin_player_core');
}

function bm_getAdvancedConfig($id)
{
    $version_link = json_decode(get_post_meta($id, "_config_version_link", true));
    if ($version_link != "")
    {
        wp_register_script('bitmovin_player_core', $version_link);
        wp_enqueue_script('bitmovin_player_core');
        return 1;
    }
    return 0;
}

function bm_getDrmConfig($id)
{
    $widevine_la_url = json_decode(get_post_meta($id, "_config_src_drm_widevine_la_url", true));
    $playready_la_url = json_decode(get_post_meta($id, "_config_src_drm_playready_la_url", true));
    $playready_customData = json_decode(get_post_meta($id, "_config_src_drm_playready_customData", true));
    $access_la_url = json_decode(get_post_meta($id, "_config_src_drm_access_la_url", true));
    $access_authToken = json_decode(get_post_meta($id, "_config_src_drm_access_authToken", true));
    $primetime_la_url = json_decode(get_post_meta($id, "_config_src_drm_primetime_la_url", true));
    $primetime_indivURL = json_decode(get_post_meta($id, "_config_src_drm_primetime_indivURL", true));
    $fairplay_la_url = json_decode(get_post_meta($id, "_config_src_drm_fairplay_la_url", true));
    $fairplay_certificateURL = json_decode(get_post_meta($id, "_config_src_drm_fairplay_certificateURL", true));

    $drm = "{";
    $hasElementBefore = false;

    if ($widevine_la_url != "") {
        $drm .= "widevine: {";
        $drm .= "LA_URL: '" . $widevine_la_url . "'";
        $drm .= "}";
        $hasElementBefore = true;
    }
    if ($playready_la_url != "") {
        if ($hasElementBefore) {
            $drm .= ",";
        }
        $drm .= "playready: {";
        $drm .= "LA_URL: '" . $playready_la_url . "',";
        $drm .= "customData: '" . $playready_customData . "'";
        $drm .= "}";
        $hasElementBefore = true;
    }
    if ($access_la_url != "" && $access_authToken != "") {
        if ($hasElementBefore) {
            $drm .= ",";
        }
        $drm .= "access: {";
        $drm .= "LA_URL: '" . $access_la_url . "',";
        $drm .= "authToken: '" . $access_authToken . "'";
        $drm .= "}";
        $hasElementBefore = true;
    }
    if ($primetime_la_url != "") {
        if ($hasElementBefore) {
            $drm .= ",";
        }
        $drm .= "primetime: {";
        $drm .= "LA_URL: '" . $primetime_la_url . "',";
        $drm .= "indivURL: '" . $primetime_indivURL . "'";
        $drm .= "}";
        $hasElementBefore = true;
    }
    if ($fairplay_la_url != "" && $fairplay_certificateURL != "") {
        if ($hasElementBefore) {
            $drm .= ",";
        }
        $drm .= "fairplay: {";
        $drm .= "LA_URL: '" . $fairplay_la_url . "',";
        $drm .= "certificateURL: '" . $fairplay_certificateURL . "'";
        $drm .= "}";
        $hasElementBefore = true;
    }

    $drm .= "}";

    if (!$hasElementBefore) {
        $drm = "";
    }

    return $drm;
}

function bm_getAdsConfig($id)
{
    $client = json_decode(get_post_meta($id, "_config_advertising_client", true));

    if ($client == "") {
        return "";
    }

    $admessage = json_decode(get_post_meta($id, "_config_advertising_admessage", true));
    $schedule1Offset = json_decode(get_post_meta($id, "_config_advertising_schedule1_offset", true));
    $schedule1Tag = json_decode(get_post_meta($id, "_config_advertising_schedule1_tag", true));
    $schedule2Offset = json_decode(get_post_meta($id, "_config_advertising_schedule2_offset", true));
    $schedule2Tag = json_decode(get_post_meta($id, "_config_advertising_schedule2_tag", true));
    $schedule3Offset = json_decode(get_post_meta($id, "_config_advertising_schedule3_offset", true));
    $schedule3Tag = json_decode(get_post_meta($id, "_config_advertising_schedule3_tag", true));
    $schedule4Offset = json_decode(get_post_meta($id, "_config_advertising_schedule4_offset", true));
    $schedule4Tag = json_decode(get_post_meta($id, "_config_advertising_schedule4_tag", true));

    if (!((($schedule1Offset != "") && ($schedule1Tag != "")) || (($schedule2Offset) && ($schedule2Tag != "")) || (($schedule3Offset != "") && ($schedule3Tag != "")) || (($schedule4Offset != "") && ($schedule4Tag != "")))) {
        return "";
    }

    $ads = "{";

    $ads .= "client: '" . $client . "',";

    if ($admessage != "") {
        $ads .= "admessage: '" . $admessage . "',";
    }

    $ads .= "schedule : {";
    $hasElementBefore = false;

    if (($schedule1Offset != "") && ($schedule1Tag != "")) {
        $ads .= "'schedule1' : {";
        $ads .= "offset: '" . $schedule1Offset . "',";
        $ads .= "tag: '" . $schedule1Tag . "'";
        $ads .= "}";
        $hasElementBefore = true;
    }
    if (($schedule2Offset != "") && ($schedule2Tag != "")) {
        if ($hasElementBefore) {
            $ads .= ",";
        }
        $ads .= "'schedule2' : {";
        $ads .= "offset: '" . $schedule2Offset . "',";
        $ads .= "tag: '" . $schedule2Tag . "'";
        $ads .= "}";
        $hasElementBefore = true;
    }
    if (($schedule3Offset != "") && ($schedule3Tag != "")) {
        if ($hasElementBefore) {
            $ads .= ",";
        }
        $ads .= "'schedule3' : {";
        $ads .= "offset: '" . $schedule3Offset . "',";
        $ads .= "tag: '" . $schedule3Tag . "'";
        $ads .= "}";
        $hasElementBefore = true;
    }
    if (($schedule4Offset != "") && ($schedule4Tag != "")) {
        if ($hasElementBefore) {
            $ads .= ",";
        }
        $ads .= "'schedule4' : {";
        $ads .= "offset: '" . $schedule4Offset . "',";
        $ads .= "tag: '" . $schedule4Tag . "'";
        $ads .= "}";
    }

    $ads .= "}";

    $ads .= "}";

    return $ads;
}

function bm_getVrConfig($id)
{
    $startupMode = json_decode(get_post_meta($id, "_config_src_vr_startupMode", true));
    $startPosition = json_decode(get_post_meta($id, "_config_src_vr_startPosition", true));
    $initialRotation = json_decode(get_post_meta($id, "_config_src_vr_initialRotation", true));
    $initialRotationRate = json_decode(get_post_meta($id, "_config_src_vr_initialRotateRate", true));

    if ($startupMode == "disabled" || $startupMode == "") {
        return "";
    }

    $vr = "{";
    $vr .= "startupMode: '" . $startupMode . "'";
    if($startPosition != "") {
        $vr .= ",startPosition: " . $startPosition;
    }

    if($initialRotation != "" && $initialRotation != "disabled") {
        $vr .= ",initialRotation: " . $initialRotation;
    }

    if($initialRotationRate != "") {
        $vr .= ",initialRotationRate: " . $initialRotationRate;
    }
    $vr .= "}";

    return $vr;
}

function bm_getStyleConfig($id)
{
    $width = json_decode(get_post_meta($id, "_config_style_width", true));
    $height = json_decode(get_post_meta($id, "_config_style_height", true));
    $aspectRatio = json_decode(get_post_meta($id, "_config_style_aspectRatio", true));

    $style = "{";
    $hasElementBefore = false;

    if($width != "") {
        $style .= "width: '" . $width . "'";
        $hasElementBefore = true;
    }

    if($height != "") {
        if ($hasElementBefore) {
            $style .= ",";
        }
        $style .= "height: '" . $height . "'";
        $hasElementBefore = true;
    }

    if($aspectRatio != "") {
        if ($hasElementBefore) {
            $style .= ",";
        }
        $style .= "aspectratio: '" . $aspectRatio . "'";
        $hasElementBefore = true;
    }
    $style .= "}";

    if(!$hasElementBefore) {
        $style = "";
    }

    return $style;
}


add_action('admin_menu', 'bitmovin_player_plugin_settings');
function bitmovin_player_plugin_settings()
{
    add_menu_page('bitmovin_player', 'Bitmovin Settings', 'administrator', 'bitmovin_settings', 'bitmovin_plugin_display_settings');
}

function bitmovin_plugin_display_settings()
{
    $apiKey = get_option('bitmovin_api_key');
    $playerKey = get_option('bitmovin_player_key');
    $image_url = plugins_url('images/info.png', __FILE__);

    $html = '<div class="wrap">
            <form id="bitmovinSettingsForm" method="post" name="options" action="options.php">

            <h2>Bitmovin Wordpress Plugin Settings</h2>'. wp_nonce_field('update-options') .'
            <table class="form-table">
                <tr><td class="tooltip">Bitmovin Api Key
                <img src="' . $image_url . '" alt="Info" height="15" weight="15">
                <span class="tooltiptext">Please insert Bitmovin API key here. <br> Do not confound it with your Player key.
                <br> You can find your API key in the settings section of your Bitmovin Account <a href="https://app.bitmovin.com/settings">here</a>.</span></td>
                <td><input id="apiKey" type="text" name="bitmovin_api_key" size="50" value="' . $apiKey. '"/></td>
                </tr>
            </table>
            <p id="messages"></p>
            <p class="submit">
                <input type="hidden" name="action" value="update" />
                <input id="playerKey" type="hidden" name="bitmovin_player_key" size="50" value="' . $playerKey. '"/>
                <input type="hidden" name="page_options" value="bitmovin_player_key,bitmovin_api_key" />
                <input type="button" value="Save" onclick="checkApiKey()"/>
            </p>
            </form>

        </div>';
    echo $html;
}
?>