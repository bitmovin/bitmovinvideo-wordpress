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

register_activation_hook(__FILE__, 'bitmovin_plugin_activation');
function bitmovin_plugin_activation()
{

}

register_deactivation_hook(__FILE__, 'bitmovin_plugin_deactivation');
function bitmovin__plugin_deactivation()
{

}

add_action('admin_enqueue_scripts', 'bitmovin_admin_assets');
function bitmovin_admin_assets()
{
    wp_enqueue_media();

    wp_register_script('bitmovin_script', plugins_url('js/bitmovin.js', __FILE__));
    wp_enqueue_script('bitmovin_script');
    wp_localize_script( 'bitmovin_script', 'bitmovin_script', array( 'dest_encoding_script' => plugins_url( 'bitcodin.php', __FILE__),
        'apiKey' => get_option('bitmovin_api_key'), 'error_image' => plugins_url( 'images/error.png', __FILE__), 'load_image' => plugins_url('images/ajax-loader.gif', __FILE__)));

    wp_register_script('player_script', 'https://bitmovin-a.akamaihd.net/bitmovin-player/stable/5/bitdash.min.js');
    wp_enqueue_script('player_script');

    wp_register_style('bitmovin_style', plugins_url('css/bitstyle.css', __FILE__));
    wp_enqueue_style('bitmovin_style');
}

add_action('init', 'bitmovin_register');
function bitmovin_register()
{
    $labels = array(
        'name' => __('Videos', 'bitmovin_player'),
        'singular_name' => __('Videos', 'bitmovin_player'),
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
    add_meta_box("bitmovin_player_preview", "Player Preview", 'bitmovin_player_preview', "bitmovin_player", "normal", "high");

    add_meta_box("bitmovin_player_configuration_video", "Video", 'bitmovin_player_configuration_video', "bitmovin_player", "normal", "high");
    add_meta_box("bitmovin_player_configuration_player", "Version", 'bitmovin_player_configuration_player', "bitmovin_player", "normal", "high");
    add_meta_box("bitmovin_player_configuration_drm", "DRM", 'bitmovin_player_configuration_drm', "bitmovin_player", "normal", "high");
    add_meta_box("bitmovin_player_configuration_ads", "Ads", 'bitmovin_player_configuration_ads', "bitmovin_player", "normal", "high");
    add_meta_box("bitmovin_player_configuration_vr", "VR", 'bitmovin_player_configuration_vr', "bitmovin_player", "normal", "high");
    add_meta_box("bitmovin_player_configuration_style", "Style", 'bitmovin_player_configuration_style', "bitmovin_player", "normal", "high");
    add_meta_box("bitmovin_player_configuration_custom", "Custom", 'bitmovin_player_configuration_custom', "bitmovin_player", "normal", "high");
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
    $html .= bitmovin_getVersionTable($post->ID);
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

function bitmovin_getOutputTable()
{
    $ftp_server = get_option('bitmovin_ftp_server');
    $ftp_usr = get_option('bitmovin_ftp_usr');
    $ftp_pw = get_option('bitmovin_ftp_pw');

    $aws_name = get_option('bitmovin_aws_name');
    $aws_access_key = get_option('bitmovin_aws_access_key');
    $aws_secret_key = get_option('bitmovin_aws_secret_key');
    $aws_bucket = get_option('bitmovin_aws_bucket');
    $aws_prefix = get_option('bitmovin_aws_prefix');
    $aws_region = get_option('bitmovin_aws_region');

    $outputTable  = getInputHidden("config_ftp_server", $ftp_server);
    $outputTable .= getInputHidden("config_ftp_usr", $ftp_usr);
    $outputTable .= getInputHidden("config_ftp_pw", $ftp_pw);

    $outputTable .= getInputHidden("config_s3_name", $aws_name);
    $outputTable .= getInputHidden("config_s3_access_key", $aws_access_key);
    $outputTable .= getInputHidden("config_s3_secret_key", $aws_secret_key);
    $outputTable .= getInputHidden("config_s3_bucket", $aws_bucket);
    $outputTable .= getInputHidden("config_s3_prefix", $aws_prefix);
    $outputTable .= getInputHidden("config_s3_region", $aws_region);

    return $outputTable;
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
    $videoTable .= '<tr><th>Progressive URL</th><td><input type="text" id="config_src_prog" value="'. $prog_url .'" placeholder="http://path/to/mp4"/><input type="button" id="prog-button" class="button" onclick="open_media_progressive_video()" value="..." /></td></tr>';
    $videoTable .= getTableRowInput("Poster URL", "config_src_poster", $poster_url, "http://path/to/poster.jpg");

    $videoTable .= "</table>";

    return $videoTable;
}

function bitmovin_getVersionTable($id)
{
    $player_channel = get_post_meta($id, "_config_player_channel", true);
    $player_version = get_post_meta($id, "_config_player_version", true);
    $version_link = get_post_meta($id, "_config_version_link", true);

    $playerTable = '<table class="wp-list-table widefat fixed striped">';
    $playerTable .= "<tr><td colspan='2'>Player Version Configuration</td></tr>";
    $playerTable .= getTableRowSelect("Channel", "config_player_channel", $player_channel, array(""));
    $playerTable .= getTableRowSelect("Version", "config_player_version", $player_version, array(""));

    $playerTable .= "<tr><td colspan='2'>Advanced</td></tr>";
    $playerTable .= "<tr><td colspan='2'><p>To provide our users the right version of our player, we have four public player channels available.<br>
    In order of latest stable to most stable, we offer the Developer Channel, the Beta Channel, the Staging Channel, and finally the Stable Channel (default for every account).
    More information about the different channels and their meaning can be found in our <a href='https://bitmovin.com/player-documentation/release-channels/'>support section</a>.</p></td></tr>";
    $playerTable .= '<tr><td colspan="2"><input type="text" id="config_version_link" value="'. $version_link .'" placeholder="https://bitmovin-a.akamaihd.net/bitmovin-player/channel/version/bitdash.min.js"/></td></tr>';

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

    $adsTable .= '<tr><th></th><td><button id="AddSchedule" class="button" type="button" onclick="addSchedule()" data-editor="content">+ Add another schedule</button></td></tr>';

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

function getTableRowInput($propertyDisplayName, $propertyName, $propertyValue, $placeHolder = "")
{
    return "<tr><th>" . $propertyDisplayName . "</th><td><input id='" . $propertyName . "' name='" . $propertyName . "' type='text' value='" . json_decode($propertyValue) . "' placeholder='" . $placeHolder . "'/></td></tr>";
}

function getInputHidden($propertyName, $propertyValue)
{
    return "<input id='" . $propertyName . "' name='" . $propertyName . "' type='hidden' value='" . $propertyValue . "'/>";
}

function getTableRowPWInput($propertyDisplayName, $propertyName, $propertyValue, $placeHolder = "")
{
    return "<tr><th>" . $propertyDisplayName . "</th><td><input id='" . $propertyName . "' name='" . $propertyName . "' type='password' value='" . json_decode($propertyValue) . "' placeholder='" . $placeHolder . "'/></td></tr>";
}

function getTableRowInputNumber($propertyDisplayName, $propertyName, $propertyValue, $placeHolder = "")
{
    return "<tr><th>" . $propertyDisplayName . "</th><td><input id='" . $propertyName . "' name='" . $propertyName . "' type='number' value='" . json_decode($propertyValue) . "' placeholder='". $placeHolder . "' step='any'/></td></tr>";
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
    return $post_id;
}

function bitmovin_getParameter($param)
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
        return "<pre id='ApiKeyError'>No correct api key set in Bitmovin Settings.</pre>";
    }

    /* use advanced config before using player config */
    $advancedConfig = bm_getAdvancedConfig($id);
    if ($advancedConfig == 0)
    {
        bm_getVersionConfig($id);
    }

    $html  = '<input type="button" id="upload-encoded-video" class="button" onclick="open_media_encoded_video()" value="Insert Video from Library">';
    $html .= "<div id='bitmovin-player'></div>\n";
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

function bm_getVersionConfig($id)
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

add_action('admin_menu', 'bitmovin_player_plugin_encoding');
function bitmovin_player_plugin_encoding()
{
    add_submenu_page('edit.php?post_type=bitmovin_player', 'Encoding', 'Encode Video', 'manage_options', 'bitmovin_encoding', 'submenu_encoding_output_tabs');
}

function submenu_encoding_output_tabs( $current = 'encoding_profiles') {

    global $pagenow;
    $tabs = array( 'encoding_configuration' => 'Encoding Configuration', 'create_encoding_profile' => 'Create Encoding Profile', 'create_output_profile' => 'Create Output Profile');

    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?post_type=bitmovin_player&page=bitmovin_encoding&tab=$tab'>$name</a>";

    }
    echo '</h2>';

    if ($pagenow == 'edit.php' && $_GET['page'] == 'bitmovin_encoding') {

        if (isset ( $_GET['tab'])) {
            $tab = $_GET['tab'];
        }
        else {
            $tab = 'encoding_configuration';
        }
        switch ($tab) {
            case 'encoding_configuration':
                bitmovin_plugin_display_encoding();
                break;

            case 'create_encoding_profile':
                bitmovin_plugin_display_create_encoding_profile();
                break;

            case 'create_output_profile':
                bitmovin_plugin_display_create_output_profile();
                break;
        }
    }
}

function bitmovin_plugin_display_encoding()
{
    $apiKey = get_option('bitmovin_api_key');

    if (!wp_script_is('bitcodin_script', 'enqueued')) {

        wp_register_script('bitcodin_script', plugins_url('js/bitcodin.js', __FILE__));
        wp_enqueue_script('bitcodin_script');
        wp_localize_script('bitcodin_script', 'bitcodin_script', array('apiKey' => $apiKey, 'bitcodin_url' => plugins_url('bitcodin.php', __FILE__), 'small_loader' => plugins_url('images/loader-small.gif', __FILE__), 'loader' => plugins_url('images/loader.gif', __FILE__)));
    }

    $html = '<div class="wrap">
                <h1>Bitmovin Encoding Configuration</h1><br>
                <table class="wp-list-table widefat fixed striped">
                    <tr><th>Select Encoding Profile</th>
                    <td><select id="bitcodin_profiles" name="bitcodin_profiles" onchange="showEncodingProfile()">
                        
                    </select></td></tr>
                    <tr><th>Select Output Profile</th>
                    <td><select id="output_profiles" name="output_profiles" onchange="showOutputProfile()">
                        
                    </select></td></tr> 
                    <tr><th>Video URL</th><td><input type="text" id="bitcodin_video_src" name="bitcodin_video_src" size="50" value="" placeholder="path/to/your/video"/><input type="button" id="upload-progressive" class="button" onclick="open_media_encoding_video()" value="..."></td></tr>
                </table>
                
                <br><br>
                
                <table id="selected-encoding-table" class="wp-list-table widefat fixed striped">
                    <tr><th colspan="2"><h4>Selected Encoding Configuration</h4></th></tr> 
                    <tr><th>Profile</th><td><input type="text" id="bitcodin_profile" name="bitcodin_profile" size="50"/></td></tr>
                    <tr><th>Quality</th><td><select id="bitcodin_quality" name="bitcodin_quality">
                        <option value="Standard">Standard</option>
                        <option value="Professional">Professional</option>
                        <option value="Premium">Premium</option>
                    </select></td></tr>
                    <tr><th>Resolution</th><td><input type="text" id="bitcodin_video_width" name="bitcodin_video_width" size="20"/> X <input type="text" id="bitcodin_video_height" name="bitcodin_video_height" size="20"/></td></tr>
                    <tr><th>Video Bitrate</th><td><input type="text" id="bitcodin_video_bitrate" name="bitcodin_video_bitrate" size="50" onkeyup="video_bitrate()"/><span id="vbitrate" class="bitrate">kbps</span></td></tr>
                    <tr><th>Audio Bitrate</th><td><input type="text" id="bitcodin_audio_bitrate" name="bitcodin_audio_bitrate" size="50" onkeyup="audio_bitrate()"/><span id="abitrate" class="bitrate">kbps</span></td></tr>
                    <tr><th>Video Codec</th><td><select id="bitcodin_video_codec" name="bitcodin_video_codec">
                        <option value="h264">h264</option>
                        <option value="hevc">hevc</option>
                    </select></td></tr>
                    <tr><th>Audio Codec</th><td><select id="bitcodin_audio_codec" name="bitcodin_audio_codec">
                        <option value="aac">aac</option>
                    </select></td></tr>
                    
                    <tr><th></th><td><input type="hidden" id="bitcodin_profile_id" name="bitcodin_profile_id"/></td></tr>
                </table>
                
                <br><br>
                
                <table id="selected-output-table" class="wp-list-table widefat fixed striped">
                    <tr><th colspan="2"><h4>Selected Output Configuration</h4></th></tr>
                    <tr><th>Type</th><td><select id="output-type" name="output-type">
                        <option value="ftp">FTP</option>
                        <option value="s3">S3</option> 
                    </select></td></tr>
                    <tr><th>Profile</th><td><input type="text" id="output-profile" name="output-profile" size="50"/></td></tr>
                    <tr><th>Host</th><td><input type="text" id="output-host" name="output-host" size="50"/></td></tr>
                    <tr><th>Path</th><td><input type="text" id="output-path" name="output-path" size="50"/></td></tr>
                    
                    <tr><th></th><td><input type="hidden" id="output_profile_id" name="output_profile_id"/></td></tr>
                </table>
                
                <p class="submit">
                    <input id="apiKey" type="hidden" name="bitmovin_api_key" size="50" value="' . $apiKey. '"/>
                    <input type="button" id="bEncode" class="button" value="Encode Selected Video" onclick="bitcodin()"/>
                </p>
            </div>
            <div id="response"></div>
            <div id="error-response"></div>';
    echo $html;
}

function bitmovin_plugin_display_create_encoding_profile() {

    $apiKey = get_option('bitmovin_api_key');

    if (!wp_script_is('encoding_profile_script', 'enqueued')) {

        wp_register_script('encoding_profile_script', plugins_url('js/crEncoding.js', __FILE__));
        wp_enqueue_script('encoding_profile_script');
        wp_localize_script('encoding_profile_script', 'script', array('apiKey' => $apiKey, 'bitcodin_url' => plugins_url('bitcodin.php', __FILE__), 'small_loader' => plugins_url('images/loader-small.gif', __FILE__), 'loader' => plugins_url('images/loader.gif', __FILE__)));
    }

    $html = '<div class="wrap">
                <h1>Create Encoding Profile</h1><br>
                <table id="encoding-table" class="wp-list-table widefat fixed striped">
                    <tr><th colspan="2"><h4>Encoding Profile</h4></th></tr> 
                    <tr><th>Profile</th><td><input type="text" id="create-encoding-profile" name="create-encoding-profile" size="50" required/></td></tr>
                </table>
                <br><br>
                <table id="video-table" class="wp-list-table widefat fixed striped">
                    <tr><th colspan="2"><h4>Video Configuration</h4></th></tr> 
                    <tr><th>Resolution</th><td><input type="number" id="create-encoding-video-width" name="create-encoding-video-width" size="20" required/> X <input type="number" id="create-encoding-video-height" name="create-encoding-video-height" size="20" required/></td></tr>
                    <tr><th>Video Bitrate</th><td><input type="number" id="create-encoding-video-bitrate" name="create-encoding-video-bitrate" size="50" onkeyup="video_bitrate()" required/><span id="vbitrate" class="bitrate">kbps</span></td></tr>
                    <tr><th>Video Codec</th><td><select id="create-encoding-video-codec" name="create-encoding-video-codec">
                        <option value="h264">H264</option>
                        <option value="hevc">HEVC</option>
                    </select></td></tr>
                </table>
                <a class="add-config" onclick="addVideoConfig()">+ Add Video Configuration</a>
                <br><br>
                <table id="audio-table" class="wp-list-table widefat fixed striped">
                    <th colspan="2"><h4>Audio Configuration</h4></th></tr>
                    <tr><th>Audio Bitrate</th><td><input type="number" id="create-encoding-audio-bitrate" name="create-encoding-audio-bitrate" size="50" onkeyup="audio_bitrate()" required/><span id="abitrate" class="bitrate">kbps</span></td></tr>
                    <tr><th>Audio Codec</th><td><select id="create-encoding-audio-codec" name="create-encoding-audio-codec">
                        <option value="aac">AAC</option>
                    </select></td></tr>
                </table> 
                <a class="add-config" onclick="addAudioConfig()">+ Add Audio Configuration</a>
                <br>

                <p class="submit">
                    <input id="apiKey" type="hidden" name="bitmovin_api_key" size="50" value="' . $apiKey. '"/>
                    <input type="button" id="button-create-encoding-profile" class="button" value="Create Encoding Profile" onclick="createEncodeProfile()"/>
                </p>
            </div>
            <div id="response"></div>
            <div id="error-response"></div>';
    echo $html;
}

function bitmovin_plugin_display_create_output_profile() {

    $apiKey = get_option('bitmovin_api_key');

    if (!wp_script_is('output_script', 'enqueued')) {

        wp_register_script('output_script', plugins_url('js/crOutput.js', __FILE__));
        wp_enqueue_script('output_script');
        wp_localize_script('output_script', 'script', array('apiKey' => $apiKey, 'bitcodin_url' => plugins_url('bitcodin.php', __FILE__), 'small_loader' => plugins_url('images/loader-small.gif', __FILE__), 'loader' => plugins_url('images/loader.gif', __FILE__)));
    }

    $html = '<div class="wrap">
                
                <h2>Create Output Profile</h2><br>
                <table class="wp-list-table widefat fixed striped">
                    <th colspan="2"><h4>Create FTP Output Profile</h4></th></tr>
                    <tr><th>Profile</th><td><input type="text" id="config_ftp_name" name="bitmovin_ftp_name" size="70" placeholder="your profile name"/></td></tr>
                    <tr><th>FTP Host</th><td><input type="text" id="config_ftp_host" name="bitmovin_ftp_host" size="70" placeholder="ftp://path/to/upload/directory/myEncodedVideo"/></td></tr>
                    <tr><th>FTP Username</th><td><input type="text" id="config_ftp_usr" name="bitmovin_ftp_usr" size="70" placeholder="FTP Username"/></td></tr>
                    <tr><th>FTP Password</th><td><input type="password" id="config_ftp_pw" name="bitmovin_ftp_pw" size="70" placeholder="FTP Password"/></td></tr>
                    <tr><th>Create Subdirectory</th><td><input type="checkbox" id="config_ftp_subdirectory" name="bitmovin_ftp_subdirectory"/></td></tr>
                </table>
                <p class="submit">
                    <input id="apiKey" type="hidden" name="bitmovin_api_key" size="50" value="' . $apiKey. '"/>
                    <input type="button" id="button-create-ftp-profile" class="button" value="Create FTP Profile" onclick="createFTPOutput()"/>
                </p>
              
                <table class="wp-list-table widefat fixed striped">
                    <th colspan="2"><h4>Create AWS Output Profile</h4></th></tr>
                    <tr><th>Profile</th><td><input type="text" id="config_aws_name" name="bitmovin_aws_name" size="70" placeholder="your profile name"/></td></tr>
                    <tr><th>Access Key</th><td><input type="text" id="config_aws_access_key" name="bitmovin_aws_access_key" size="70" placeholder="Your AWS Access Key"/></td></tr>
                    <tr><th>Secret Key</th><td><input type="password" id="config_aws_secret_key" name="bitmovin_aws_secret_key" size="70" placeholder="Your AWS Secret Key"/></td></tr>
                    <tr><th>Bucket</th><td><input type="text" id="config_aws_bucket" name="bitmovin_aws_bucket" size="70" placeholder="Your Bucket Name"/></td></tr>
                    <tr><th>Prefix</th><td><input type="text" id="config_aws_prefix" name="bitmovin_aws_prefix" size="70" placeholder="Folder name created for output"/></td></tr>
                    <tr><th>Region</th><td>
                        <select id="config_aws_region" name="bitmovin_aws_region">
                            <option>us-east-1</option>
                            <option>us-west-1</option>
                            <option>us-west-2</option>
                            <option>eu-west-1</option>
                            <option>eu-central-1</option>
                            <option>ap-southeast-1</option>
                            <option>ap-southeast-2</option>
                            <option>ap-northeast-1</option>
                            <option>sa-east-1</option>
                            <option>cn-north-1</option>
                            <option>us-gov-west-1</option>
                        </select>            
                    </td></tr>
                    <tr><th>Create Subdirectory</th><td><input type="checkbox" id="config_s3_subdirectory" name="bitmovin_s3_subdirectory"/></td></tr>
                </table>
                <p class="submit">
                    <input id="apiKey" type="hidden" name="bitmovin_api_key" size="50" value="' . $apiKey. '"/>
                    <input type="button" id="button-create-s3-profile" class="button" value="Create AWS Profile" onclick="createS3Output()"/>
                </p>
             </div>
             <div id="response"></div>
             <div id="error-response"></div>';
    echo $html;
}

add_action('admin_menu', 'bitmovin_player_plugin_settings');
function bitmovin_player_plugin_settings()
{
    add_submenu_page('edit.php?post_type=bitmovin_player', 'Settings', 'Settings', 'manage_options', 'bitmovin_settings', 'bitmovin_plugin_display_settings');
}

function bitmovin_plugin_display_settings()
{
    if (!wp_script_is('tooltip-script', 'enqueued')) {

        wp_register_script('tooltip-script', 'http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
        wp_enqueue_script('tooltip-script');
    }

    $apiKey = get_option('bitmovin_api_key');
    $playerKey = get_option('bitmovin_player_key');

    $image_url = plugins_url('images/info.png', __FILE__);

    $html = '<div class="wrap">
            <h2>Bitmovin Wordpress Plugin Settings</h2><br>
            <form id="bitmovinSettingsForm" method="post" name="options" action="options.php">'. wp_nonce_field('update-options') .'
            <table class="wp-list-table widefat fixed striped">
                <tr><td>Bitmovin API Key
                
                <div class="tooltip">
                    <img src="' . $image_url . '" alt="Info" height="15" width="15">
                    <span class="tooltiptext">
                        Please insert Bitmovin API key here. <br> Do not confound it with your Player key.
                        <br> You can find your API key in the settings section of your Bitmovin Account
                        <a class="api-link" href="https://app.bitmovin.com/settings">here</a>.
                    </span>
                </div>
                
                </td>
                <td><input id="apiKey" type="text" name="bitmovin_api_key" size="50" value="' . $apiKey. '"/></td>
                </tr>
            </table>
            <p class="submit">
                <input type="hidden" name="action" value="update" />
                <input id="playerKey" type="hidden" name="bitmovin_player_key" size="50" value="' . $playerKey. '"/>
                <input type="hidden" name="page_options" value="bitmovin_player_key,bitmovin_api_key" />
                <input type="button" class="button" value="Save API Key" onclick="checkApiKey()"/>
            </p>
            </form>
            <div id="response"></div>
            <div id="error-response"></div>
        </div>';
    echo $html;
}