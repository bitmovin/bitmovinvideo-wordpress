/**
 * Created by Bitmovin on 08.08.2016.
 */

var outputProfiles;
var encodingProfiles;
var media_uploader = null;

$j = jQuery.noConflict();
$j(document).ready(function() {

    if (bitcodin_script.apiKey == "") {
        $j('#response').html("<p id='response'>No valid API Key found</p>");
    }
    /* disable table content */
    $j("#selected-encoding-table").find("input,button,textarea,select").attr("disabled","disabled");
    $j("#selected-output-table").find("input,button,textarea,select").attr("disabled","disabled");

    getEncodingProfiles();
    getOutputProfiles();
});

function bitcodin() {

    delete_response();
    var url = bitcodin_script.bitcodin_url;
    var videoSrc = document.getElementById("bitcodin_video_src").value;
    var encodingProfileID = document.getElementById("bitcodin_profile_id").value;
    var outputProfileID = document.getElementById("output_profile_id").value;

    if (videoSrc != "") {
        $j.ajax({
            type: "POST",
            url: url,
            data: {
                apiKey: bitcodin_script.apiKey,
                method: "bitmovin_encoding_service",
                videoSrc: videoSrc,
                encodingProfileID: encodingProfileID,
                outputProfileID: outputProfileID
            },
            beforeSend: function () {
                $j('#response').html("<img src='" + bitcodin_script.loader + "'/><p>Encoding in progress...</p>");
            },
            success: function (content) {

                var error = content.toString().includes("error");
                if (!error) {
                    $j('#response').html("<p>Encoding finished successfully</p>");
                }
                else {
                    delete_response();
                    $j('#error-response').html(content);
                }
            },
            error: function (error) {
                $j('#error-response').html(error);
            }
        });
        /* no page refresh */
        return false;
    }
    else {
        $j("#error-response").css("visibility", "visible");
        $j('#error-response').html("<p>Maybe forgot the video source?</p>");
    }
}

function delete_response() {

    $j('#response').html("");
    $j('#error-response').html("");
    $j("#error-response").css("visibility", "hidden");
}

function getEncodingProfiles() {

    sendAPIRequest("get_bitcodin_profiles", "Encoding Profiles are loading...", encodingProfiles, "bitcodin_profiles");
}

function getOutputProfiles() {

    sendAPIRequest("get_output_profiles", "Output Profiles are loading...", outputProfiles, "output_profiles");
}

function sendAPIRequest(method, message, profile, id) {

    var url = bitcodin_script.bitcodin_url;
    $j.ajax({
        type: "POST",
        url: url,
        data: {
            apiKey: bitcodin_script.apiKey,
            method: method
        },
        beforeSend: function() {
            $j('#response').html("<img src='" + bitcodin_script.small_loader + "'/><p>" + message + "</p>");
        },
        success: function (content) {

            var index = 0;
            profile = $j.parseJSON(content);
            profile = removeDuplicates(profile, "name");
            var select = document.getElementById(id);

            for (; index < profile.length; index++) {

                var option = document.createElement('option');
                option.text = profile[index].name;
                select.add(option, index);
            }
            if (id == 'bitcodin_profiles') {

                encodingProfiles = profile;
                showEncodingProfile();
            }
            else {

                outputProfiles = profile;
                showOutputProfile();
            }

            delete_response();
        },
        error: function(error) {
            $j('#error-response').html(error);
        }
    });
    /* no page refresh */
    return false;
}

function showEncodingProfile() {

    var object;
    var index = 0;
    var output = document.getElementById("bitcodin_profiles");
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
    }
}

function showOutputProfile() {

    var object;
    var index = 0;
    var output = document.getElementById("output_profiles");
    for (; index < outputProfiles.length; index++) {

        object = outputProfiles[index];
        if (object.name == output.options[output.selectedIndex].value) {

            $j('#output-profile').val(object.name);
            $j('#output-type').val(object.type);
            $j('#output-host').val(object.host);
            $j('#output-path').val(object.path);

            $j('#output_profile_id').val(object.outputId);
            break;
        }
        else if (output.options[output.selectedIndex].value == "default") {

            //initEncodingProfile();
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
        $j('#bitcodin_video_src').val(attachment.url);
    });

    media_uploader.open();
}