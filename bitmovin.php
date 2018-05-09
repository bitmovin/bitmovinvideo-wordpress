<?php
/*
  Plugin Name: Bitmovin
  Plugin URI: https://github.com/bitmovin/bitmovinvideo-wordpress
  Description: <strong>Bitmovin's</strong> HTML5 Adaptive Streaming Video Plugin for Wordpress.
  Version: 2.0.3
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
    add_meta_box("bitmovin_player_configuration_video", "Video Sources", 'bitmovin_player_configuration_video', "bitmovin_player", "normal", "high");
    add_meta_box("bitmovin_player_configuration_player", "Player Version", 'bitmovin_player_configuration_player', "bitmovin_player", "normal", "high");
    add_meta_box("bitmovin_player_configuration_custom", "Player Configuration", 'bitmovin_player_configuration_custom', "bitmovin_player", "normal", "high");

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

    $videoTable .= getTableRowInput("Dash URL", "config_src_dash", $dash_url, "http://path/to/mpd/file.mpd");
    $videoTable .= getTableRowInput("HLS URL", "config_src_hls", $hls_url, "http://path/to/hls/playlist/file.m3u8");
    $videoTable .= getTableRowInput("Progressive URL", "config_src_prog", $prog_url, "http://path/to/mp4");
    $videoTable .= getTableRowInput("Poster URL", "config_src_poster", $poster_url, "http://path/to/poster.jpg");

    $videoTable .= "</table>";

    $videoTable .= "<input id='apiKey' type='hidden' value='" . $apiKey = get_option('bitmovin_api_key') . "'/>";
    $videoTable .= "<input id='config_player_key_selected' name='config_player_key_selected' type='hidden' value='" . json_decode(get_post_meta($id, "_config_player_key", true)) . "'/>";

    return $videoTable;
}


function getFromApi($path)
{
    $apiKey = get_option("bitmovin_api_key");

    $opts = array(
        "http" => array(
            "method" => "GET",
            "header" => "X-Api-Key: " . $apiKey
        )
    );

    $context = stream_context_create($opts);

    $file = file_get_contents("https://api.bitmovin.com/v1" . $path, false, $context);

    return json_decode($file);
}

function getPlayerTable($id)
{
    $playerChannels = array(
        "stable" => "Stable",
        "staging" => "Staging",
        "beta" => "Beta"
    );

    $player_channel = get_post_meta($id, "_config_player_channel", true);
    $player_version = get_post_meta($id, "_config_player_version", true);
    $player_version_url = get_post_meta($id, "_config_player_version_url", true);
    $player_key = get_post_meta($id, "_config_player_key", true);

    $playerTable = '<table class="wp-list-table widefat fixed striped">';

    $playerTable .= getTableRowSelect("Channel", "config_player_channel", $player_channel, $playerChannels);
    $playerTable .= getTableRowSelect("Version", "config_player_version", $player_version, array());
    $playerTable .= getTableRowInput("Version Url", "config_player_version_url", $player_version_url);
    $playerTable .= getTableRowSelect("Player Key", "config_player_key", $player_key, array());
    $playerTable .= "</table>";

    return $playerTable;
}

function getCustomTable($id)
{
    $customConf = json_decode(get_post_meta($id, "_config_custom_conf", true));

    $customTable = "<table class='wp-list-table widefat fixed striped'>";

    $customTable .= "<tr><td>Custom configuration</td><td><pre>var conf = {<br><div class='intend1'><textarea id='config_custom' name='config_custom_conf' placeholder='Insert your custom configuration here, e.g.:\nplayback: { \n    autoplay: true\n}\n'>" . $customConf . "</textarea></div>};</pre></td></tr>";
    $customTable .= "<tr><td colspan='2' class='hint'>An Overview about all available configuration options can be found in our <a href='https://developer.bitmovin.com/hc/en-us/articles/115001689833' target='_blank'>documentation</a>. HINT: The configuration properties set above the custom configuration will override conflicts in this configuration.</td></tr>";

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
        $player_version_url = getParameter("config_player_version_url");
        $player_key = getParameter("config_player_key");

        update_post_meta($post_id, "_config_player_channel", $player_channel);
        update_post_meta($post_id, "_config_player_version", $player_version);
        update_post_meta($post_id, "_config_player_version_url", $player_version_url);
        update_post_meta($post_id, "_config_player_key", $player_key);

        $customConf = getParameter("config_custom_conf");

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

// Player generation

add_shortcode("bitmovin_player", "generate_player");
function generate_player($id)
{
    extract(shortcode_atts(array(
        'id' => ''
    ), $id));

    $playerKey = json_decode(get_post_meta($id, '_config_player_key', true));

    if ($playerKey == "")
    {
        return "<pre>No correct api key set in Bitmovin Settings.</pre>";
    }

    $player_version = json_decode(get_post_meta($id, "_config_player_version_url", true));

    wp_register_script('bitmovin_player_core', $player_version);
    wp_enqueue_script('bitmovin_player_core');

    $dash = json_decode(get_post_meta($id, "_config_src_dash", true));
    $hls = json_decode(get_post_meta($id, "_config_src_hls", true));
    $prog = json_decode(get_post_meta($id, "_config_src_prog", true));
    $poster = json_decode(get_post_meta($id, "_config_src_poster", true));


    $html = "<div id='bitmovin-player'></div>\n";
    $html .= "<script type='text/javascript'>\n";
    $html .= "window.onload = function() {\n";
    $html .= "var player = bitmovin.player(\"bitmovin-player\");\n";
    $html .= "var conf = {\n";
    $custom = json_decode(get_post_meta($id, "_config_custom_conf", true));
    if ($custom != "")
    {
        $html .= $custom;
    }
    $html .= "};\n";

    $html .= "conf.key = '" . $playerKey . "';\n";

    $html .= "conf.source = conf.source || {}\n";
    $html .= "conf.source.dash = '" . $dash . "';\n";
    $html .= "conf.source.hls = '" . $hls . "';\n";
    $html .= "conf.source.progressive = '" . $prog . "';\n";
    $html .= "conf.source.poster = '" . $poster . "';\n";

    $html .= "player.setup(conf).then(function(value) {\n";
    $html .= "console.log('Successfully created bitdash player instance');\n";
    $html .= "}, function(reason) {\n";
    $html .= "console.log('Error while creating bitdash player instance');\n";
    $html .= "});\n";

    $html .= "};";
    $html .= "</script>\n";

    return $html;
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

// Settings Page

add_action('admin_menu', 'bitmovin_player_plugin_settings');
function bitmovin_player_plugin_settings()
{
    add_menu_page('bitmovin_player', 'Bitmovin Settings', 'administrator', 'bitmovin_settings', 'bitmovin_plugin_display_settings');
}

function bitmovin_plugin_display_settings()
{
    $apiKey = get_option('bitmovin_api_key');

    $html = '<div class="wrap">
            <form id="bitmovinSettingsForm" method="post" name="options" action="options.php">

            <h2>Bitmovin Wordpress Plugin Settings</h2>' . wp_nonce_field('update-options') . '
            <table class="form-table">
                <tr>
                <td>Please insert your Bitmovin API key here. You can find your API key in your account details at <a href="https://dashboard.bitmovin.com/account" target="_blank" rel="nofollow">https://dashboard.bitmovin.com/account</a> in the "API Keys" Panel.</td>
                </tr>
                <tr>
                <td>Bitmovin Api Key: <input id="apiKey" type="text" name="bitmovin_api_key" size="36" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" value="' . $apiKey . '"/></td>
                </tr>
            </table>
            <p id="messages"></p>
            <p class="submit">
                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="page_options" value="bitmovin_api_key" />
                <input type="button" value="Save" onclick="checkApiKey()"/>
            </p>
            </form>

        </div>';
    echo $html;
}
