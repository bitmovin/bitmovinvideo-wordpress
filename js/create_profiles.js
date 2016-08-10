/**
 * Created by Bitmovin on 10.08.2016.
 */

$j = jQuery.noConflict();

function createEncodeProfile() {

    var profile = document.getElementById('create-encoding-profile').value;
    var video_width = document.getElementById('create-encoding-video-width').value;
    var video_height = document.getElementById('create-encoding-video-height').value;
    var video_bitrate = document.getElementById('create-encoding-video-bitrate').value * 1000;
    var audio_bitrate = document.getElementById('create-encoding-audio-bitrate').value * 1000;
    var video_codec = document.getElementById('create-encoding-video-codec').value;
    var audio_codec = document.getElementById('create-encoding-audio-codec').value;

    if (profile != "" && video_width != "" && video_height != "" && video_bitrate != "" && audio_bitrate != "" && video_codec != "" && audio_codec != "") {

        var url = script.bitcodin_url;
        $j.ajax({
            type: "POST",
            url: url,
            data: {
                apiKey: script.apiKey,
                method: "create_encoding_profile",
                profile:  profile,
                video_width: video_width,
                video_height: video_height,
                video_bitrate: video_bitrate,
                audio_bitrate: audio_bitrate,
                video_codec: video_codec,
                audio_codec: audio_codec
            },
            beforeSend: function() {
                $j('#response').html("<img src='" + script.small_loader + "' /><p>Creating Encoding Profile...</p>");
            },
            success: function (content) {

                var error = content.toString().includes("error");
                if (!error) {
                    $j('#response').html("<p>Your Encoding Profile was created successfully</p>");
                }
                else {
                    $j('#error-response').html(content);
                }
            },
            error: function(error) {
                $j('#error-response').html(error);
            }
        });
        /* no page refresh */
        return false;
    }
    else {

        $j('#error-response').html('Please consider to fill out the whole form.');
    }
}

function video_bitrate()
{
    var video_bitrate = document.getElementById("create-encoding-video-bitrate").value;
    var res = checkVideoBitrate(video_bitrate);
    if (res == 1)
    {
        document.getElementById("vbitrate").innerHTML = video_bitrate + " kbps";
        $j("#vbitrate").css("background-color","#31b0d5");
    }
    else if (res == 2)
    {
        document.getElementById("vbitrate").innerHTML = video_bitrate/1000 + " Mbps";
        $j("#vbitrate").css("background-color","#31b0d5");
    }
    else {
        document.getElementById("vbitrate").innerHTML = "max. 20 Mbps allowed!";
        $j("#vbitrate").css("background-color","red");
    }
}

function audio_bitrate()
{
    var audio_bitrate = document.getElementById("create-encoding-audio-bitrate").value;
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
        return 0;
    }
}