<?php
/*
  Plugin Name: Bitmovin
  Plugin URI: https://github.com/bitmovin/bitmovinvideo-wordpress
  Description: <strong>Bitmovin's</strong> HTML5 Adaptive Streaming Video Plugin for Wordpress.
  Version: 0.6.1
  Author: Bitmovin
  Author URI: https://bitmovin.com
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
    wp_register_script('bitmovin_script', plugins_url('js/bitmovin.js', __FILE__));
    wp_enqueue_script('bitmovin_script');

    wp_register_style('bitmovin_style', plugins_url('css/bitstyle.css', __FILE__));
    wp_enqueue_style('bitmovin_style');
}

add_action('init', 'bitmovin_register');
function bitmovin_register()
{
    $labels = array(
        'name'          => __('Videos', 'bitmovin_player'),
        'singular_name' => __('Video', 'bitmovin_player'),
        'menu_name'     => __('Bitmovin', 'bitmovin_player'),
        'add_new'       => __('Add New Video', 'bitmovin_player'),
        'add_new_item'  => __('Add New Video', 'bitmovin_player'),
        'new_item'      => __('New Video', 'bitmovin_player'),
        'edit_item'     => __('Edit Video', 'bitmovin_player'),
        'view_item'     => __('View Video', 'bitmovin_player'),
        'all_items'     => __('All Videos', 'bitmovin_player'),
        'search_items'  => __('Search Videos', 'bitmovin_player')
    );

    $args = array(
        'labels'              => $labels,
        'hierarchical'        => true,
        'description'         => 'Video',
        'supports'            => array('title'),
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => false,
        'publicly_queryable'  => true,
        'exclude_from_search' => true,
        'has_archive'         => true,
        'query_var'           => true,
        'can_export'          => true,
        'rewrite'             => true,
        'capability_type'     => 'post',
        'menu_icon'           => plugins_url('images/bitlogo.png', __FILE__)
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
    switch ($column)
    {
        case 'bitmovin_player_shortcode':
            echo "[bitmovin_player id='$post_id']";
            break;
    }
}

add_action('add_meta_boxes', 'bitmovin_video_meta_box');
function bitmovin_video_meta_box()
{
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

function bitmovin_player_configuration_video()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="video">';
    $html .= getVideoTable($post->ID);
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_player_configuration_player()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="video">';
    $html .= getPlayerTable($post->ID);
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_player_configuration_advanced()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="video">';
    $html .= getAdvancedTable($post->ID);
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_player_configuration_drm()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="drm" class="configContent">';
    $html .= getDrmTable($post->ID);
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_player_configuration_ads()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="ads" class="configContent">';
    $html .= getAdsTable($post->ID);
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_player_configuration_vr()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="vr" class="configContent">';
    $html .= getVrTable($post->ID);
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_player_configuration_style()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="style" class="configContent">';
    $html .= getStyleTable($post->ID);
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function bitmovin_player_configuration_custom()
{
    global $post;

    $html = '<div class="configSection">';
    $html .= '<div id="custom" class="configContent">';
    $html .= getCustomTable($post->ID);
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
    $html .= generate_player(array("id" => $post->ID));
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

function getVideoTable($id)
{
    $dash_url = get_post_meta($id, "_config_src_dash", true);
    $hls_url = get_post_meta($id, "_config_src_hls", true);
    $prog_url = get_post_meta($id, "_config_src_prog", true);
    $poster_url = get_post_meta($id, "_config_src_poster", true);

    $videoTable = '<table class="wp-list-table widefat fixed striped">';
    $videoTable .= "<tr><td class='heading' colspan='2'>Video Configuration<a href='https://bitmovin.com/player-documentation/player-configuration/#Source' target='_blank'>Documentation</a></td></tr>";

    $videoTable .= getTableRowInput("Dash URL", "config_src_dash", $dash_url, "http://path/to/mpd/file.mpd");
    $videoTable .= getTableRowInput("HLS URL", "config_src_hls", $hls_url, "http://path/to/hls/playlist/file.m3u8");
    $videoTable .= getTableRowInput("Progressive URL", "config_src_prog", $prog_url, "http://path/to/mp4");
    $videoTable .= getTableRowInput("Poster URL", "config_src_poster", $poster_url, "http://path/to/poster.jpg");

    $videoTable .= "</table>";

    return $videoTable;
}

function getSupportedPlayerVersions()
{
    $versions = array(
        "7" => "Latest Version 7",
        "6" => "Latest Version 6",
        "5" => "Latest Version 5"
    );

    return $versions;
}

function getPlayerTable($id)
{
    $player_channel = get_post_meta($id, "_config_player_channel", true);
    $player_version = get_post_meta($id, "_config_player_version", true);

    $playerTable = '<table class="wp-list-table widefat fixed striped">';
    $playerTable .= "<tr><td class='heading' colspan='2'>Player Channels/Versions</td></tr>";
    $playerTable .= getTableRowSelect("Channel", "config_player_channel", $player_channel, array(
        "stable"  => "Stable",
        "staging" => "Staging",
        "beta"    => "Beta"
    ));
    $playerTable .= getTableRowSelect("Version", "config_player_version", $player_version, getSupportedPlayerVersions());
    $playerTable .= "</table>";

    return $playerTable;
}


function getAdvancedTable($id)
{
    $version_link = get_post_meta($id, "_config_version_link", true);

    $advancedTable = "<table class='wp-list-table widefat fixed striped'>";
    $advancedTable .= "<tr><td class='heading' colspan='2'>Custom Player Version</td></tr><tr><td colspan='2'>To provide our users the right version of our player, we have four public player channels available.
    In order of latest stable to most stable, we offer the Developer Channel, the Beta Channel, the Staging Channel, and finally the Stable Channel (default for every account).
    More information about the different channels and their meaning can be found in our <a href='https://bitmovin.com/player-documentation/release-channels/'>support section</a>.</td></tr>";
    $advancedTable .= "<tr><td colspan='2'>" . getInputField("config_version_link", $version_link, "https://bitmovin-a.akamaihd.net/bitmovin-player/CHANNEL/VERSION/bitmovinplayer.js") . "</td></tr>";
    $advancedTable .= "</table>";

    return $advancedTable;
}

function getDrmTable($id)
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
    $drmTable .= "<tr><td class='heading' colspan='2'>DRM Configuration<a href='https://bitmovin.com/player-documentation/player-configuration/#DRM' target='_blank'>Documentation</a></td></tr>";

    $drmTable .= "<tr><td class='heading' colspan='2'>Widevine</td></tr>";
    $drmTable .= getTableRowInput("LA_URL", "config_src_drm_widevine_la_url", $widevine_la_url, "https://mywidevine.licenseserver.com/");

    $drmTable .= "<tr><td class='heading' colspan='2'>Playready</td></tr>";
    $drmTable .= getTableRowInput("LA_URL", "config_src_drm_playready_la_url", $playready_la_url, "https://myplayready.licenseserver.com/");
    $drmTable .= getTableRowInput("customData", "config_src_drm_playready_customData", $playready_customData);

    $drmTable .= "<tr><td class='heading' colspan='2'>Access</td></tr>";
    $drmTable .= getTableRowInput("LA_URL", "config_src_drm_access_la_url", $access_la_url, "https://myaccess.licenseserver.com/");
    $drmTable .= getTableRowInput("authToken", "config_src_drm_access_authToken", $access_authToken, "YOUR-BASE64-ENCODED-AUTH-TOKEN");

    $drmTable .= "<tr><td class='heading' colspan='2'>Primetime</td></tr>";
    $drmTable .= getTableRowInput("LA_URL", "config_src_drm_primetime_la_url", $primetime_la_url, "https://myprimetime.licenseserver.com/");
    $drmTable .= getTableRowInput("indivURL", "config_src_drm_primetime_indivURL", $primetime_indivURL);

    $drmTable .= "<tr><td class='heading' colspan='2'>Fairplay</td></tr>";
    $drmTable .= getTableRowInput("LA_URL", "config_src_drm_fairplay_la_url", $fairplay_la_url, "https://fairplay.licenseserver.com/");
    $drmTable .= getTableRowInput("certificateUrl", "config_src_drm_fairplay_certificateURL", $fairplay_certificateURL, "https://fairplay.licenseserver.com/certificate-url");

    $drmTable .= "</table>";
    return $drmTable;
}

function getAdsTable($id)
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
    $adsTable .= "<tr><td class='heading' colspan='2'>Ads Configuration<a href='https://bitmovin.com/player-documentation/player-configuration/#Advertising_8211_VAST' target='_blank'>Documentation</a></td></tr>";

    $adsTable .= getTableRowInput("Client", "config_advertising_client", $client);
    $adsTable .= getTableRowInput("Ad message", "config_advertising_admessage", $admessage);

    $adsTable .= "<tr><td class='heading' colspan='2'>Schedule 1</td></tr>";
    $adsTable .= getTableRowInput("Offset", "config_advertising_schedule1_offset", $schedule1Offset);
    $adsTable .= getTableRowInput("Tag", "config_advertising_schedule1_tag", $schedule1Tag);
    $adsTable .= "<tr><td class='heading' colspan='2'>Schedule 2</td></tr>";
    $adsTable .= getTableRowInput("Offset", "config_advertising_schedule2_offset", $schedule2Offset);
    $adsTable .= getTableRowInput("Tag", "config_advertising_schedule2_tag", $schedule2Tag);
    $adsTable .= "<tr><td class='heading' colspan='2'>Schedule 3</td></tr>";
    $adsTable .= getTableRowInput("Offset", "config_advertising_schedule3_offset", $schedule3Offset);
    $adsTable .= getTableRowInput("Tag", "config_advertising_schedule3_tag", $schedule3Tag);
    $adsTable .= "<tr><td class='heading' colspan='2'>Schedule 4</td></tr>";
    $adsTable .= getTableRowInput("Offset", "config_advertising_schedule4_offset", $schedule4Offset);
    $adsTable .= getTableRowInput("Tag", "config_advertising_schedule4_tag", $schedule4Tag);

    $adsTable .= "</table>";

    return $adsTable;
}

function getVrTable($id)
{
    $startupMode = get_post_meta($id, "_config_src_vr_startupMode", true);
    $startPosition = get_post_meta($id, "_config_src_vr_startPosition", true);
    $initialRotation = get_post_meta($id, "_config_src_vr_initialRotation", true);
    $initialRotateRate = get_post_meta($id, "_config_src_vr_initialRotateRate", true);

    $vrTable = "<table class='wp-list-table widefat fixed striped'>";
    $vrTable .= "<tr><td class='heading' colspan='2'>VR/360° Configuration<a href='https://bitmovin.com/player-documentation/player-configuration/#VR_and_360_Video' target='_blank'>Documentation</a></td></tr>";

    $vrTable .= getTableRowSelect("Startup mode", "config_src_vr_startupMode", $startupMode, array("disabled", "2d", "stereo-2d", "3d", "stereo-3d", "no-vr"));
    $vrTable .= getTableRowInputNumber("Start position", "config_src_vr_startPosition", $startPosition, 180);
    $vrTable .= getTableRowSelect("Initial rotation", "config_src_vr_initialRotation", $initialRotation, array("disabled", "true"));
    $vrTable .= getTableRowInputNumber("Initial rotation rate", "config_src_vr_initialRotateRate", $initialRotateRate, 0.025);

    $vrTable .= "</table>";

    return $vrTable;
}

function getStyleTable($id)
{
    $width = get_post_meta($id, "_config_style_width", true);
    $height = get_post_meta($id, "_config_style_height", true);
    $aspectRatio = get_post_meta($id, "_config_style_aspectRatio", true);

    $styleTable = "<table class='wp-list-table widefat fixed striped'>";
    $styleTable .= "<tr><td class='heading' colspan='2'>Style Configuration<a href='https://bitmovin.com/player-documentation/player-configuration/#Style' target='_blank'>Documentation</a></td></tr>";

    $styleTable .= getTableRowInput("Width", "config_style_width", $width, "100%");
    $styleTable .= getTableRowInput("Height", "config_style_height", $height, "100%");
    $styleTable .= getTableRowInput("Aspect ratio", "config_style_aspectRatio", $aspectRatio, "16:9");

    $styleTable .= "</table>";

    return $styleTable;
}

function getCustomTable($id)
{
    $customConf = json_decode(get_post_meta($id, "_config_custom_conf", true));
    $customSource = json_decode(get_post_meta($id, "_config_custom_source", true));

    $customTable = "<table class='wp-list-table widefat fixed striped'>";
    $customTable .= "<tr><td class='heading' colspan='2'>Custom Configuration</td></tr>";

    $customTable .= "<tr><td>Appended to configuration</td><td><pre>var conf = {<br><div class='intend1'>...<br>...<br><textarea id='config_custom' name='config_custom_conf'>" . $customConf . "</textarea></div>};</pre></td></tr>";
    $customTable .= "<tr><td>Appended to configuration -> source</td><td><pre>var conf = {<br><div class='intend1'>source: {<div class='intend1'>...<br>...<br><textarea id='config_custom_source' name='config_custom_source'>" . $customSource . "</textarea></div>},<br>...<br>...</div>};</pre></td></tr>";
    $customTable .= "<tr><td colspan='2' class='hint'>Make sure you start your custom configuration with an ','</td></tr>";

    $customTable .= "</table>";

    return $customTable;
}

function getInputField($propertyName, $propertyValue, $placeHolder)
{
    return "<input id='" . $propertyName . "' name='" . $propertyName . "' type='text' value='" . json_decode($propertyValue) . "' placeholder='" . $placeHolder . "'/>";
}

function getTableRowInput($propertyDisplayName, $propertyName, $propertyValue, $placeHolder = "")
{
    return "<tr><th>" . $propertyDisplayName . "</th><td>" . getInputField($propertyName, $propertyValue, $placeHolder) . "</td></tr>";
}

function getTableRowInputNumber($propertyDisplayName, $propertyName, $propertyValue, $placeHolder = "")
{
    return "<tr>
        <th>" . $propertyDisplayName . "</th>
        <td><input id='" . $propertyName . "' name='" . $propertyName . "' type='number' value='" . json_decode($propertyValue) . "' placeholder='" . $placeHolder . "' step='any'/></td></tr>";
}

function getTableRowSelect($propertyDisplayName, $propertyName, $selectedOption, $options)
{
    $selectedOption = json_decode($selectedOption);
    if (in_array($selectedOption, array_values($options)))
    {
        $selectedOption = array_search($selectedOption, $options);
    }

    $tableRowSelect = "<tr><th>" . $propertyDisplayName . "</th><td><select id='" . $propertyName . "' name='" . $propertyName . "'>";
    foreach ($options as $key => $option)
    {
        $value = is_int($key) ? $option : $key;
        $tableRowSelect .= "<option value='" . $value . "'" . (($key == $selectedOption) ? "selected=\"selected\"" : "") . ">" . $option . "</option>";
    }

    $tableRowSelect .= "</select></td></tr>";

    return $tableRowSelect;
}

add_action('save_post', 'bitmovin_player_save_configuration');
function bitmovin_player_save_configuration($post_id)
{
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    {
        return $post_id;
    }

    // check permissions

    if (array_key_exists('post_type', $_POST) && 'bitmovin_player' == $_POST['post_type'] && current_user_can('edit_post', $post_id))
    {

        $dash_url = getParameter("config_src_dash");
        $hls_url = getParameter("config_src_hls");
        $prog_url = getParameter("config_src_prog");
        $poster_url = getParameter("config_src_poster");

        update_post_meta($post_id, "_config_src_dash", $dash_url);
        update_post_meta($post_id, "_config_src_hls", $hls_url);
        update_post_meta($post_id, "_config_src_prog", $prog_url);
        update_post_meta($post_id, "_config_src_poster", $poster_url);

        $player_channel = getParameter("config_player_channel");
        $player_version = getParameter("config_player_version");

        update_post_meta($post_id, "_config_player_channel", $player_channel);
        update_post_meta($post_id, "_config_player_version", $player_version);

        $version_link = getParameter("config_version_link");

        update_post_meta($post_id, "_config_version_link", $version_link);

        $widevine_la_url = getParameter("config_src_drm_widevine_la_url");
        $playready_la_url = getParameter("config_src_drm_playready_la_url");
        $playready_customData = getParameter("config_src_drm_playready_customData");
        $access_la_url = getParameter("config_src_drm_access_la_url");
        $access_authToken = getParameter("config_src_drm_access_authToken");
        $primetime_la_url = getParameter("config_src_drm_primetime_la_url");
        $primetime_indivURL = getParameter("config_src_drm_primetime_indivURL");
        $fairplay_la_url = getParameter("config_src_drm_fairplay_la_url");
        $fairplay_certificateURL = getParameter("config_src_drm_fairplay_certificateURL");

        update_post_meta($post_id, "_config_src_drm_widevine_la_url", $widevine_la_url);
        update_post_meta($post_id, "_config_src_drm_playready_la_url", $playready_la_url);
        update_post_meta($post_id, "_config_src_drm_playready_customData", $playready_customData);
        update_post_meta($post_id, "_config_src_drm_access_la_url", $access_la_url);
        update_post_meta($post_id, "_config_src_drm_access_authToken", $access_authToken);
        update_post_meta($post_id, "_config_src_drm_primetime_la_url", $primetime_la_url);
        update_post_meta($post_id, "_config_src_drm_primetime_indivURL", $primetime_indivURL);
        update_post_meta($post_id, "_config_src_drm_fairplay_la_url", $fairplay_la_url);
        update_post_meta($post_id, "_config_src_drm_fairplay_certificateURL", $fairplay_certificateURL);

        $client = getParameter("config_advertising_client");
        $admessage = getParameter("config_advertising_admessage");
        $schedule1Offset = getParameter("config_advertising_schedule1_offset");
        $schedule1Tag = getParameter("config_advertising_schedule1_tag");
        $schedule2Offset = getParameter("config_advertising_schedule2_offset");
        $schedule2Tag = getParameter("config_advertising_schedule2_tag");
        $schedule3Offset = getParameter("config_advertising_schedule3_offset");
        $schedule3Tag = getParameter("config_advertising_schedule3_tag");
        $schedule4Offset = getParameter("config_advertising_schedule4_offset");
        $schedule4Tag = getParameter("config_advertising_schedule4_tag");

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


        $startupMode = getParameter("config_src_vr_startupMode");
        $startPosition = getParameter("config_src_vr_startPosition");
        $initialRotation = getParameter("config_src_vr_initialRotation");
        $initialRotateRate = getParameter("config_src_vr_initialRotateRate");

        update_post_meta($post_id, "_config_src_vr_startupMode", $startupMode);
        update_post_meta($post_id, "_config_src_vr_startPosition", $startPosition);
        update_post_meta($post_id, "_config_src_vr_initialRotation", $initialRotation);
        update_post_meta($post_id, "_config_src_vr_initialRotateRate", $initialRotateRate);

        $width = getParameter("config_style_width");
        $height = getParameter("config_style_height");
        $aspectRatio = getParameter("config_style_aspectRatio");

        update_post_meta($post_id, "_config_style_width", $width);
        update_post_meta($post_id, "_config_style_height", $height);
        update_post_meta($post_id, "_config_style_aspectRatio", $aspectRatio);

        $customSource = getParameter("config_custom_source");
        $customConf = getParameter("config_custom_conf");

        update_post_meta($post_id, "_config_custom_source", $customSource);
        update_post_meta($post_id, "_config_custom_conf", $customConf);

    }
    else
    {
        return $post_id;
    }
}

function getParameter($param)
{
    $param = (isset($_POST[$param]) ? $_POST[$param] : '');
    return strip_tags(json_encode($param));
}

function parsePlayerVersion($versionString)
{
    $matches = null;
    if (preg_match('/^(?<=Latest\\sVersion\\s)?\\d$/', $versionString, $matches) == 1)
    {
        return $matches[0];
    }
    else if (preg_match('/\\d(\\.\\d(\\.\\d)?)?/', $versionString, $matches) == 1)
    {
        return $matches[0];
    }
    else
    {
        return null;
    }
}

add_shortcode("bitmovin_player", "generate_player");
function generate_player($id)
{
    extract(shortcode_atts(array(
        'id' => ''
    ), $id));

    $playerKey = get_option('bitmovin_player_key');
    if ($playerKey == "")
    {
        return "<pre>No correct api key set in Bitmovin Settings.</pre>";
    }

    $wpPlayerVersion = json_decode(get_post_meta($id, "_config_player_version", true));
    $player_version = parsePlayerVersion($wpPlayerVersion);
    if ($player_version === false)
    {
        return "<pre>Invalid player version: " . htmlspecialchars($player_version) . "</pre>";
    }

    $advancedConfig = getAdvancedConfig($id);
    $player_init_cmd = "typeof bitmovin !== \"undefined\" ? bitmovin.player(\"bitmovin-player\") : bitdash(\"bitmovin-player\");";
    if ($advancedConfig == 0)
    {
        getPlayerConfig($id);
    }


    $html = "<div id='bitmovin-player'></div>\n";
    $html .= "<script type='text/javascript'>\n";
    $html .= "window.onload = function() {\n";
    $html .= "var player = " . $player_init_cmd . ";\n";
    $html .= "var conf = {\n";
    $html .= "key: '" . $playerKey . "',\n";
    $html .= "source: {\n";
    $html .= getVideoConfig($id);

    $drm = getDrmConfig($id);
    if ($drm != "")
    {
        $html .= ",drm: " . $drm . "\n";
    }

    $vr = getVrConfig($id);
    if ($vr != "")
    {
        $html .= ",vr: " . $vr . "\n";
    }

    $custom = json_decode(get_post_meta($id, "_config_custom_source", true));
    if ($custom != "")
    {
        $html .= $custom;
    }

    $html .= "}\n";

    $ads = getAdsConfig($id);
    if ($ads != "")
    {
        $html .= ",advertising: " . $ads . "\n";
    }

    $style = getStyleConfig($id);
    if ($style != "")
    {
        $html .= ",style: " . $style . "\n";
    }

    $custom = json_decode(get_post_meta($id, "_config_custom_conf", true));
    if ($custom != "")
    {
        $html .= $custom;
    }

    $html .= "};\n";

    $html .= "player.setup(conf).then(function(value) {\n";
    $html .= "console.log('Successfully created bitdash player instance');\n";
    $html .= "}, function(reason) {\n";
    $html .= "console.log('Error while creating bitdash player instance');\n";
    $html .= "});\n";

    $html .= "};";
    $html .= "</script>\n";

    return $html;
}

function getVideoConfig($id)
{
    $dash = json_decode(get_post_meta($id, "_config_src_dash", true));
    $hls = json_decode(get_post_meta($id, "_config_src_hls", true));
    $prog = json_decode(get_post_meta($id, "_config_src_prog", true));
    $poster = json_decode(get_post_meta($id, "_config_src_poster", true));

    $video = "";
    $hasElementBefore = false;

    if ($dash != "")
    {
        $video .= "dash: '" . $dash . "'";
        $hasElementBefore = true;
    }
    if ($hls != "")
    {
        if ($hasElementBefore)
        {
            $video .= ",";
        }
        $video .= "hls: '" . $hls . "'";
        $hasElementBefore = true;
    }
    if ($prog != "")
    {
        if ($hasElementBefore)
        {
            $video .= ",";
        }
        $video .= "progressive: '" . $prog . "'";
        $hasElementBefore = true;
    }
    if ($poster != "")
    {
        if ($hasElementBefore)
        {
            $video .= ",";
        }
        $video .= "poster: '" . $poster . "'";
    }

    return $video;
}

function getPlayerConfig($id)
{
    $player_channel = json_decode(get_post_meta($id, "_config_player_channel", true));
    $player_channel = strtolower($player_channel);

    $player_version = json_decode(get_post_meta($id, "_config_player_version", true));
    $parsedPlayerVersion = parsePlayerVersion($player_version);

    if (is_null($parsedPlayerVersion))
    {
        $parsedPlayerVersion = 7;
    }

    $srcRoot = "https://bitmovin-a.akamaihd.net/bitmovin-player/" . $player_channel . "/";
    $src = $srcRoot . $parsedPlayerVersion . "/bitmovinplayer.js";

    if (intval($parsedPlayerVersion) == 6)
    {
        $src = $srcRoot . $parsedPlayerVersion . "/bitmovinplayer.min.js";
    }
    else if (intval($parsedPlayerVersion) <= 5)
    {
        $src = $srcRoot . $parsedPlayerVersion . "/bitdash.min.js";
    }

    wp_register_script('bitmovin_player_core', $src);
    wp_enqueue_script('bitmovin_player_core');
}

function getAdvancedConfig($id)
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

function getDrmConfig($id)
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

    if ($widevine_la_url != "")
    {
        $drm .= "widevine: {";
        $drm .= "LA_URL: '" . $widevine_la_url . "'";
        $drm .= "}";
        $hasElementBefore = true;
    }
    if ($playready_la_url != "")
    {
        if ($hasElementBefore)
        {
            $drm .= ",";
        }
        $drm .= "playready: {";
        $drm .= "LA_URL: '" . $playready_la_url . "',";
        $drm .= "customData: '" . $playready_customData . "'";
        $drm .= "}";
        $hasElementBefore = true;
    }
    if ($access_la_url != "" && $access_authToken != "")
    {
        if ($hasElementBefore)
        {
            $drm .= ",";
        }
        $drm .= "access: {";
        $drm .= "LA_URL: '" . $access_la_url . "',";
        $drm .= "authToken: '" . $access_authToken . "'";
        $drm .= "}";
        $hasElementBefore = true;
    }
    if ($primetime_la_url != "")
    {
        if ($hasElementBefore)
        {
            $drm .= ",";
        }
        $drm .= "primetime: {";
        $drm .= "LA_URL: '" . $primetime_la_url . "',";
        $drm .= "indivURL: '" . $primetime_indivURL . "'";
        $drm .= "}";
        $hasElementBefore = true;
    }
    if ($fairplay_la_url != "" && $fairplay_certificateURL != "")
    {
        if ($hasElementBefore)
        {
            $drm .= ",";
        }
        $drm .= "fairplay: {";
        $drm .= "LA_URL: '" . $fairplay_la_url . "',";
        $drm .= "certificateURL: '" . $fairplay_certificateURL . "'";
        $drm .= "}";
        $hasElementBefore = true;
    }

    $drm .= "}";

    if (!$hasElementBefore)
    {
        $drm = "";
    }

    return $drm;
}

function getAdsConfig($id)
{
    $client = json_decode(get_post_meta($id, "_config_advertising_client", true));

    if ($client == "")
    {
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

    if (!((($schedule1Offset != "") && ($schedule1Tag != "")) || (($schedule2Offset) && ($schedule2Tag != "")) || (($schedule3Offset != "") && ($schedule3Tag != "")) || (($schedule4Offset != "") && ($schedule4Tag != ""))))
    {
        return "";
    }

    $ads = "{";

    $ads .= "client: '" . $client . "',";

    if ($admessage != "")
    {
        $ads .= "admessage: '" . $admessage . "',";
    }

    $ads .= "schedule : {";
    $hasElementBefore = false;

    if (($schedule1Offset != "") && ($schedule1Tag != ""))
    {
        $ads .= "'schedule1' : {";
        $ads .= "offset: '" . $schedule1Offset . "',";
        $ads .= "tag: '" . $schedule1Tag . "'";
        $ads .= "}";
        $hasElementBefore = true;
    }
    if (($schedule2Offset != "") && ($schedule2Tag != ""))
    {
        if ($hasElementBefore)
        {
            $ads .= ",";
        }
        $ads .= "'schedule2' : {";
        $ads .= "offset: '" . $schedule2Offset . "',";
        $ads .= "tag: '" . $schedule2Tag . "'";
        $ads .= "}";
        $hasElementBefore = true;
    }
    if (($schedule3Offset != "") && ($schedule3Tag != ""))
    {
        if ($hasElementBefore)
        {
            $ads .= ",";
        }
        $ads .= "'schedule3' : {";
        $ads .= "offset: '" . $schedule3Offset . "',";
        $ads .= "tag: '" . $schedule3Tag . "'";
        $ads .= "}";
        $hasElementBefore = true;
    }
    if (($schedule4Offset != "") && ($schedule4Tag != ""))
    {
        if ($hasElementBefore)
        {
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

function getVrConfig($id)
{
    $startupMode = json_decode(get_post_meta($id, "_config_src_vr_startupMode", true));
    $startPosition = json_decode(get_post_meta($id, "_config_src_vr_startPosition", true));
    $initialRotation = json_decode(get_post_meta($id, "_config_src_vr_initialRotation", true));
    $initialRotationRate = json_decode(get_post_meta($id, "_config_src_vr_initialRotateRate", true));

    if ($startupMode == "disabled" || $startupMode == "")
    {
        return "";
    }

    $vr = "{";
    $vr .= "startupMode: '" . $startupMode . "'";
    if ($startPosition != "")
    {
        $vr .= ",startPosition: " . $startPosition;
    }

    if ($initialRotation != "" && $initialRotation != "disabled")
    {
        $vr .= ",initialRotation: " . $initialRotation;
    }

    if ($initialRotationRate != "")
    {
        $vr .= ",initialRotationRate: " . $initialRotationRate;
    }
    $vr .= "}";

    return $vr;
}

function getStyleConfig($id)
{
    $width = json_decode(get_post_meta($id, "_config_style_width", true));
    $height = json_decode(get_post_meta($id, "_config_style_height", true));
    $aspectRatio = json_decode(get_post_meta($id, "_config_style_aspectRatio", true));

    $style = "{";
    $hasElementBefore = false;

    if ($width != "")
    {
        $style .= "width: '" . $width . "'";
        $hasElementBefore = true;
    }

    if ($height != "")
    {
        if ($hasElementBefore)
        {
            $style .= ",";
        }
        $style .= "height: '" . $height . "'";
        $hasElementBefore = true;
    }

    if ($aspectRatio != "")
    {
        if ($hasElementBefore)
        {
            $style .= ",";
        }
        $style .= "aspectratio: '" . $aspectRatio . "'";
        $hasElementBefore = true;
    }
    $style .= "}";

    if (!$hasElementBefore)
    {
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

            <h2>Bitmovin Wordpress Plugin Settings</h2>' . wp_nonce_field('update-options') . '
            <table class="form-table">
                <tr><td class="tooltip">Bitmovin Api Key
                <img src="' . $image_url . '" alt="Info" height="15" width="15">
                <span class="tooltiptext">Please insert Bitmovin API key here. <br> Do not confound it with your Player key.
                <br> You can find your API key in the settings section of your Bitmovin Account <a href="https://app.bitmovin.com/settings">here</a>.</span></td>
                <td><input id="apiKey" type="text" name="bitmovin_api_key" size="50" value="' . $apiKey . '"/></td>
                </tr>
            </table>
            <p id="messages"></p>
            <p class="submit">
                <input type="hidden" name="action" value="update" />
                <input id="playerKey" type="hidden" name="bitmovin_player_key" size="50" value="' . $playerKey . '"/>
                <input type="hidden" name="page_options" value="bitmovin_player_key,bitmovin_api_key" />
                <input type="button" value="Save" onclick="checkApiKey()"/>
            </p>
            </form>

        </div>';
    echo $html;
}
