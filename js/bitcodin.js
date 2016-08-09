/**
 * Created by Bitmovin on 08.08.2016.
 */

var encodingProfiles;
var media_uploader = null;

$j = jQuery.noConflict();
$j(document).ready(function() {

    if (bitcodin_script.apiKey == "") {
        $j('#response').html("<p id='response'>No valid API Key found</p>");
    }

    getOutputProfiles();
    initOutputProfile();
});

function bitcodin() {

    var url = bitcodin_script.bitcodin_url;
    var videoSrc = document.getElementById("bitcodin_video_src").value;
    var profileID = document.getElementById("bitcodin_profile_id").value;

    $j.ajax({
        type: "POST",
        url: url,
        data: {
            apiKey: bitcodin_script.apiKey,
            method: "bitmovin_encoding_service",
            videoSrc:  videoSrc,
            profileID: profileID
        },
        beforeSend: function() {
            $j('#response').html("<img src='" + bitcodin_script.loader + "' /><p>Encoding in progress...</p>");
        },
        success: function (content) {

            var error = content.toString().includes("error");
            if (!error) {
                $j('#response').html("<p>Encoding finished successfully</p>");
            }
            else {
                $j('#response').html(content);
            }
        },
        error: function(error) {
            console.log(error);
        }
    });
    /* no page refresh */
    return false;
}

function getOutputProfiles() {

    var url = bitcodin_script.bitcodin_url;
    $j.ajax({
        type: "POST",
        url: url,
        data: {
            apiKey: bitcodin_script.apiKey,
            method: "get_bitcodin_profiles"
        },
        beforeSend: function() {
            $j('#response').html("<img src='" + bitcodin_script.small_loader + "' /><p>Encoding Profiles are loading...</p>");
        },
        success: function (content) {

            var index = 0;
            encodingProfiles = $j.parseJSON(content);
            encodingProfiles = removeDuplicates(encodingProfiles, "name");
            var select = document.getElementById("bicodin_profiles");

            for (; index < encodingProfiles.length; index++) {

                var option = document.createElement('option');
                option.text = encodingProfiles[index].name;
                select.add(option, index);
            }
            $j('#response').html("");
        },
        error: function(error) {

        }
    });
    /* no page refresh */
    return false;
}

function initOutputProfile() {

    $j('#bitcodin_profile').val("Default");
    $j('#bitcodin_quality').val("Premium");
    $j('#bitcodin_video_height').val("720");
    $j('#bitcodin_video_width').val("1280");
    $j('#bitcodin_video_bitrate').val("2400");
    $j('#bitcodin_audio_bitrate').val("128");
    $j('#bitcodin_video_codec').val("h264");
    $j('#bitcodin_audio_codec').val("aac");
    $j('#bitcodin_profile_id').val("86262");
    video_bitrate();
    audio_bitrate();
}

function showOutputProfile() {

    var object;
    var index = 0;
    var output = document.getElementById("bicodin_profiles");
    for (; index < encodingProfiles.length; index++) {

        object = encodingProfiles[index];
        if (object.name == output.options[output.selectedIndex].value) {

            $j('#bitcodin_profile').val(object.name);
            $j('#bitcodin_quality').val(object.videoStreamConfigs[0].preset);
            $j('#bitcodin_video_height').val(object.videoStreamConfigs[0].height);
            $j('#bitcodin_video_width').val(object.videoStreamConfigs[0].width);
            $j('#bitcodin_video_bitrate').val(object.videoStreamConfigs[0].bitrate / 1000);
            $j('#bitcodin_audio_bitrate').val(object.audioStreamConfigs[0].bitrate / 1000);
            $j('#bitcodin_video_codec').val(object.videoStreamConfigs[0].codec);
            $j('#bitcodin_audio_codec').val(object.audioStreamConfigs[0].codec);
            $j('#bitcodin_profile_id').val(object.encodingProfileId);
            video_bitrate();
            audio_bitrate();
            break;
        }
        else if (output.options[output.selectedIndex].value == "default") {

            initOutputProfile();
            break;
        }
    }
}

function removeDuplicates(arr, prop) {
    var new_arr = [];
    var lookup  = {};

    for (var i in arr) {
        lookup[arr[i][prop]] = arr[i];
    }

    for (i in lookup) {
        new_arr.push(lookup[i]);
    }

    return new_arr;
}

function video_bitrate()
{
    var video_bitrate = document.getElementById("bitcodin_video_bitrate").value;
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
    var audio_bitrate = document.getElementById("bitcodin_audio_bitrate").value;
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
        $j('#config_video_src').val(attachment.url);
    });

    media_uploader.open();
}