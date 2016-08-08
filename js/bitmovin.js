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
            audio_bitrate();
            video_bitrate();
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

<<<<<<< HEAD
    var option = document.createElement('option');
    var channel = document.getElementById("config_player_channel");
    var select = document.getElementById("config_player_version");
    if (channel.options[channel.selectedIndex].value == "Beta")
    {
        option.text = "Latest Version 5";
        select.add(option, 0);
=======
        var profile = document.getElementById('config_encoding_profile').value;
        var video_width = document.getElementById('config_encoding_width').value;
        var video_height = document.getElementById('config_encoding_height').value;
        var video_bitrate = document.getElementById('config_encoding_video_bitrate').value * 1000;
        var audio_bitrate = document.getElementById('config_encoding_audio_bitrate').value * 1000;

        var video_src = document.getElementById('config_encoding_video_src').value;

        /* Define variables for FTP output */
        var ftp_usr;
        var ftp_pw;
        var ftp_server;

        /* Define variables for S3 output */
        var access_key;
        var secret_key;
        var bucket;
        var prefix;
        var aws_name;
        var region;
>>>>>>> origin/feature/encoding

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
            prefix = document.getElementById('config_s3_prefix').value;
            region = document.getElementById('config_s3_region').value;
        }
        else
        {
            alert("Please select either FTP or S3 output");
        }

        if (profile != "" && (video_width != "" || video_height != "") && video_bitrate != "" && video_bitrate <= 20000000 && audio_bitrate != "" && audio_bitrate <= 256000 &&
            video_src != "")
        {
            if ((output == "ftp" && ftp_server != "" && ftp_usr != "" && ftp_pw != "") || (output == "s3" && access_key != "" && secret_key != "" && bucket != "" && aws_name != "" && region != "" && prefix != ""))
            {
                var url = bitmovin_script.dest_encoding_script;
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
                        prefix:         prefix,
                        region:         region
                    },
                    beforeSend: function() {
                        $j('#response').html("<p>Encoding...</p><img src='" + bitmovin_script.load_image + "' />");
                    },
                    success: function (content) {
                        var error = content.toString().includes("error");
                        if (!error)
                        {
                            var myObj = $j.parseJSON(content);
                            var regEx = new RegExp('\/(([A-Za-z]+)?|([0-9]+)?)*((\.mpd)|(\.m3u8))','g')
                            var mpd = myObj.mpd.match(regEx);
                            var m3u8 = myObj.m3u8.match(regEx);

                            var mpdOutput = myObj.host + "/" + myObj.path + mpd;
                            var m3u8Output = myObj.host + "/" + myObj.path + m3u8;

                            if (output == "ftp") {
                                $j('#config_src_dash').val("http://" + mpdOutput);
                                $j('#config_src_hls').val("http://" + m3u8Output);
                            }
                            if (output == "s3") {
                                $j('#config_src_dash').val("https://" + mpdOutput);
                                $j('#config_src_hls').val("https://" + m3u8Output);
                            }
                            $j('#response').html("<p>Encoding finished<br>Finally just click the <b>Update button</b> to implement the encoded video in the player.</p>");
                        }
                        else {
                            console.log(content);
                            $j('#response').html("<img src='" + bitmovin_script.error_image + "' width='30' height='30'/><p>Some error occured. <br>Press F12 and switch to Console to see full error message.</p>");
                        }
                    },
                    error: function(error) {
                        $j('#response').html("<img src='" + bitmovin_script.error_image + "' /><p>Some error occured. <br>Press F12 and switch to Console to see full error message.</p>");
                        //console.log(error.responseJSON.message);
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

function video_bitrate()
{
    var video_bitrate = document.getElementById("config_encoding_video_bitrate").value;
    var res = checkVideoBitrate(video_bitrate);
    if (res == 1)
    {
        document.getElementById("vbitrate").innerHTML = video_bitrate + " kbps";
        $j("#vbitrate").css("background-color","#31b0d5");
    }
    else if (res == 2)
    {
<<<<<<< HEAD
        option.text = "Latest Version 5";
        select.add(option);
=======
        document.getElementById("vbitrate").innerHTML = video_bitrate/1000 + " Mbps";
        $j("#vbitrate").css("background-color","#31b0d5");
    }
    else {
        document.getElementById("vbitrate").innerHTML = "max. 20 Mbps allowed!";
        $j("#vbitrate").css("background-color","red");
    }
}
>>>>>>> origin/feature/encoding

function audio_bitrate()
{
    var audio_bitrate = document.getElementById("config_encoding_audio_bitrate").value;
    if (audio_bitrate <= 256)
    {
        document.getElementById("abitrate").innerHTML = audio_bitrate + " kbps";
        $j("#abitrate").css("background-color","#31b0d5");
    }
    else {
        document.getElementById("abitrate").innerHTML = "max. 256 kbps allowed!";
        $j("#abitrate").css("background-color","red");
    }
}

function checkVideoBitrate(bitrate)
{
    if (bitrate < 1000)
    {
        return 1;
    }
    else if (bitrate >= 1000 && bitrate <= 20000)
    {
        return 2;
    }
    else
    {
<<<<<<< HEAD
        option.text = "Latest Version 5";
        select.add(option);

        option = document.createElement('option');
        option.text = "Latest Version 4";
        select.add(option);

        option = document.createElement('option');
        option.text = "5.0";
        select.add(option);

        option = document.createElement('option');
        option.text = "4.4";
        select.add(option);

        option = document.createElement('option');
        option.text = "4.3";
        select.add(option);
=======
        return 0;
    }
}
>>>>>>> origin/feature/encoding

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
    /* Custom Uploader only showing video files */
    media_uploader = wp.media({
        title:  "Select Video for Encoding",
        frame:  "select",
        button: {
            text: "Select Video for Encoding"
        },
        library: { type: "video"},
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
        title:  "Select Video for Embedding",
        frame:  "select",
        button: {
            text: "Select Video for Embedding"
        },
        library: { type: "video" },
        multiple: false
    });

    media_uploader.on("select", function(){

        /* get video url and insert into right video src input */
        var attachment = media_uploader.state().get('selection').first().toJSON();
        $j('#config_src_prog').val(attachment.url);

        /* leave me here for getting additional video properties */
        //for ( var image_property in attachment ) {

           // console.log(image_property + ': ' + image_data[image_property]);
        //}
    });

    media_uploader.open();
}