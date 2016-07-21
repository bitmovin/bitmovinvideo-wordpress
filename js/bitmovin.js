var channels = [];
var versions = [];

$j = jQuery.noConflict();
$j(document).ready(function() {
    var configSections = ["bitmovin_player_configuration_drm", "bitmovin_player_configuration_ads", "bitmovin_player_configuration_vr", "bitmovin_player_configuration_style", "bitmovin_player_configuration_custom"];
    for(var i=0;i<configSections.length;i++) {
        if(!hasContent(configSections[i]))
            $j("#"+configSections[i]).addClass("closed");
    }

    var apiKey = bitmovin_script.apiKey;
    $j.ajax({
        url: "https://app.bitmovin.com/api/player-versions",
        type: "GET",
        beforeSend: function(xhr) {
            xhr.setRequestHeader('bitcodin-api-key', apiKey);
        },
        success: function(data) {
            var index = 0;
            for (; index < data.length; index++)
            {
                versions.push({CHANNEL: data[index].category, VERSION: data[index].version});
                var compare = channels.indexOf(data[index].category);
                if (compare == -1)
                {
                    channels.push(data[index].category);
                }
            }
            createChannels();
            getVersions();
            checkOutputChoice();
        },
        error: function(error) {
            console.log(error.responseJSON.message);
        }
    });
});
function hasContent(configSection) {
    var contentFound = false;
    var inputTypes = ["input[type='text']", "input[type='number']", "select", "textarea"];
    for(var i=0;i<inputTypes.length;i++) {
        $j("#" + configSection + " " + inputTypes[i]).each(function (index) {
            if(this.value != "" && this.value != "disabled") {
                contentFound = true;
                return false;
            }
        });
        if(contentFound)
            break;
    }
    return contentFound;
}
function checkApiKey() {
    var apiKey = $j("#apiKey").val();
    $j.ajax({
        url: "https://app.bitmovin.com/api/settings/player/key",
        type: "GET",
        beforeSend: function(xhr) {
            xhr.setRequestHeader('bitcodin-api-key', apiKey);
        },
        success: function(data) {
            $j("#playerKey").val(data.key);
            $j("#bitmovinSettingsForm").submit();
        },
        error: function(error) {
            $j("#messages").text(error.responseJSON.message);
        }
    });
}

/* Encoding Button Click */
$j(document).ready(function() {
    $j("button#bEncode").click(function () {

        var profile = document.getElementById('config_encoding_profile').value;
        var video_width = document.getElementById('config_encoding_width').value;
        var video_height = document.getElementById('config_encoding_height').value;
        var video_bitrate = document.getElementById('config_encoding_video_bitrate').value;
        var audio_bitrate = document.getElementById('config_encoding_audio_bitrate').value;

        var video_src = document.getElementById('config_encoding_video_src').value;

        /* Define variables for FTP output */
        var ftp_usr;
        var ftp_pw;
        var ftp_server;

        /* Define variables for S3 output */
        var access_key;
        var secret_key;
        var bucket;
        var aws_name;
        var region;

        /* Represents ftp or s3 */
        var output;

        if (document.getElementsByName("output")[0].checked)
        {
            output = "ftp";
            ftp_server = document.getElementById('config_ftp_server').value;
            ftp_usr = document.getElementById('config_ftp_usr').value;
            ftp_pw = document.getElementById('config_ftp_pw').value;
        }
        else if (document.getElementsByName("output")[1].checked)
        {
            output = "s3";
            aws_name = document.getElementById('config_s3_name').value;
            access_key = document.getElementById('config_s3_access_key').value;
            secret_key = document.getElementById('config_s3_secret_key').value;
            bucket = document.getElementById('config_s3_bucket').value;
            region = document.getElementById('config_s3_region').value;
        }
        else
        {
            alert("Please select either FTP or S3 output");
        }

        if (profile != "" && (video_width != "" || video_height != "") && video_bitrate != "" && audio_bitrate != "" &&
            video_src != "")
        {
            if ((output == "ftp" && ftp_server != "" && ftp_usr != "" && ftp_pw != "") || (output == "s3" && access_key != "" && secret_key != "" && bucket != "" && aws_name != "" && region != ""))
            {
                var url = bitmovin_script.plugin_url + "bitcoding.php";
                console.log(url);
                $j.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        apiKey: bitmovin_script.apiKey,
                        method: "bitmovin_encoding_service",
                        output:         output,
                        profile:        profile,
                        video_width:    video_width,
                        video_height:   video_height,
                        video_bitrate:  video_bitrate,
                        audio_bitrate:  audio_bitrate,
                        video_src:      video_src,
                        ftp_server:     ftp_server,
                        ftp_usr:        ftp_usr,
                        ftp_pw:         ftp_pw,
                        access_key:     access_key,
                        secret_key:     secret_key,
                        bucket:         bucket,
                        aws_name:       aws_name,
                        region:         region
                    },
                    beforeSend: function() {
                        $j('#response').html("<p>Encoding...</p><img src='images/loading.gif' />");
                    },
                    success: function (content) {
                        console.log(content);
                        $j('#response').html("<p>Encoding finished</p>");
                        $j('#config_src_hls').val(ftp_server + "/video_0_" + video_bitrate + "_hls.m3u8");
                    },
                    error: function(error) {
                        console.log(error.responseJSON.message);
                    }
                });
                /* Kein Neuladen der Website */
                return false;
            }
            else {
                alert("You have to fill out the selected Output Configuration to create an correct output.");
                return false;
            }
        }
        else
        {
            alert("You have to fill out the Uploads/Encoding form to encode your video.");
            return false;
        }
    });
});

function checkOutputChoice()
{
    if (document.getElementsByName("output")[0].checked)
    {
        document.getElementById('config_ftp_server').disabled = false;
        document.getElementById('config_ftp_usr').disabled = false;
        document.getElementById('config_ftp_pw').disabled = false;

        document.getElementById('config_s3_access_key').disabled = true;
        document.getElementById('config_s3_secret_key').disabled = true;
        document.getElementById('config_s3_bucket').disabled = true;
        document.getElementById('config_s3_name').disabled = true;
        document.getElementById('config_s3_region').disabled = true;
    }
    else
    {
        document.getElementById('config_ftp_server').disabled = true;
        document.getElementById('config_ftp_usr').disabled = true;
        document.getElementById('config_ftp_pw').disabled = true;

        document.getElementById('config_s3_access_key').disabled = false;
        document.getElementById('config_s3_secret_key').disabled = false;
        document.getElementById('config_s3_bucket').disabled = false;
        document.getElementById('config_s3_name').disabled = false;
        document.getElementById('config_s3_region').disabled = false;
    }
}

function createChannels()
{
    var index = 0;
    var channel = document.getElementById("config_player_channel");
    for (; index < channels.length; index++)
    {
        var option = document.createElement('option');
        option.text = channels[index];
        channel.add(option, index);
    }
    $j("#config_player_channel").val(channels[0]);
}

function getVersions()
{
    removeAllOptions();

    var index;
    var cindex;
    var channel = document.getElementById("config_player_channel");
    var select = document.getElementById("config_player_version");
    for (cindex = 0; cindex < channels.length; cindex++)
    {
        if (channel.options[channel.selectedIndex].value == channels[cindex])
        {
            for (index = 0; index < versions.length; index++)
            {
                if (versions[index].CHANNEL == channels[cindex])
                {
                    var option = document.createElement('option');
                    option.text = versions[index].VERSION;
                    select.add(option, index);
                }
            }
        }
    }
}

function removeAllOptions()
{
    var select = document.getElementById("config_player_version");
    while(select.firstChild)
    {
        select.removeChild(select.firstChild);
    }
}

var media_uploader = null;

function open_media_encoding_video()
{
    media_uploader = wp.media({
        title: "Select Video for Encoding",
        button: {
            text: "Select Video"
        },
        //library: { type: "video"},
        multiple: false
    });

    media_uploader.on("select", function(){

        /* get video url and insert into video src input */
        var attachment = media_uploader.state().get('selection').first().toJSON();
        $j('#config_encoding_video_src').val(attachment.url);
    });

    media_uploader.open();
}

function open_media_progressive_video()
{
    media_uploader = wp.media({
        title: "Select Video for Embedding",
        button: {
            text: "Select Video"
        },
        multiple: false
    });

    media_uploader.on("select", function(){

        /* get video url and insert into right video src input */
        var attachment = media_uploader.state().get('selection').first().toJSON();
        if (attachment.subtype == "mpd")
        {
            $j('#config_src_dash').val(attachment.url);
        }
        else if (attachment.subtype == "m3u8")
        {
            $j('#config_src_hls').val(attachment.url);
        }
        else if (attachment.subtype == "mp4")
        {
            $j('#config_src_prog').val(attachment.url);
        }
        /* leave me here for getting additional video properties */
        //for ( var image_property in attachment ) {

           // console.log(image_property + ': ' + image_data[image_property]);
        //}
    });

    media_uploader.open();
}