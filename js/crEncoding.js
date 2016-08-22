/**
 * Created by Bitmovin on 10.08.2016.
 */

var audioConf_anz = 1;
var videoConf_anz = 1;
$j = jQuery.noConflict();
$j(document).ready(function() {
    video_bitrate();
    audio_bitrate();
});

function createEncodeProfile() {

    delete_response();

    var video_width;
    var video_height;
    var video_bitrate;
    var video_codec;

    var audio_codec;
    var audio_bitrate;

    var videoConfigs = [];
    var audioConfigs = [];


    var idCodec = "create-encoding-video-codec";
    var idWidth = "create-encoding-video-width";
    var idHeight = "create-encoding-video-height";
    var idBitrate = "create-encoding-video-bitrate";

    var profile = document.getElementById('create-encoding-profile').value;

    var index = 0;
    for (; index < videoConf_anz; index++) {

        if (index > 0) {

            idCodec = "create-encoding-video-codec" + index;
            idWidth = "create-encoding-video-width" + index;
            idHeight = "create-encoding-video-height" + index;
            idBitrate = "create-encoding-video-bitrate" + index;
        }
        /* Skip if dynamic video config was deleted */
        if (document.getElementById(idCodec) != null) {

            video_codec = document.getElementById(idCodec).value;
            video_width = document.getElementById(idWidth).value;
            video_height = document.getElementById(idHeight).value;
            video_bitrate = document.getElementById(idBitrate).value * 1000;

            if (profile != "" && video_width != "" && video_width <= 7680 && video_width >= 128 &&
                video_height != "" && video_height <= 4320 && video_height >= 96 &&
                video_bitrate != "" && video_bitrate <= 20000000 && video_bitrate >= 32000 && video_codec != "") {

                videoConfigs.push({width: video_width, height: video_height, bitrate: video_bitrate, codec: video_codec});
            }
            else if (video_bitrate > 20000000) {

                $j("#error-response").fadeIn("slow");
                $j('#error-response').html("<p>Max. video bitrate limited to 20 Mbps.</p>");
                return;
            }
            else if (video_bitrate < 32000) {

                $j("#error-response").fadeIn("slow");
                $j('#error-response').html("<p>Min. video bitrate limited to 32 kbps.</p>");
                return;
            }
            else if (video_width > 7680 || video_height > 4320) {

                $j("#error-response").fadeIn("slow");
                $j('#error-response').html("<p>Max. Resolution 7680 x 4320.</p>");
                return;
            }
            else if (video_width < 128 || video_height < 96) {

                $j("#error-response").fadeIn("slow");
                $j('#error-response').html("<p>Min. Resolution 128 x 96.</p>");
                return;
            }
            else {
                $j("#error-response").fadeIn("slow");
                $j('#error-response').html("<p>Please consider to fill out the whole form.</p>");
                return;
            }
        }
    }

    index = 0;
    idCodec = "create-encoding-audio-codec";
    idBitrate = "create-encoding-audio-bitrate";
    for (; index < audioConf_anz; index++) {

        if (index > 0) {

            idCodec = "create-encoding-audio-codec" + index;
            idBitrate = "create-encoding-audio-bitrate" + index;
        }
        /* Skip if dynamic audio config was deleted */
        if (document.getElementById(idCodec) != null) {

            audio_codec = document.getElementById(idCodec).value;
            audio_bitrate = document.getElementById(idBitrate).value * 1000;

            if (audio_bitrate != "" && audio_bitrate <= 256000 && audio_bitrate >= 8000 && audio_codec != "") {

                audioConfigs.push({bitrate: audio_bitrate, codec: audio_codec});
            }
            else if (audio_bitrate > 256000) {

                $j("#error-response").fadeIn("slow");
                $j('#error-response').html("<p>Maximum audio bitrate limited to 256 kbps.</p>");
                return;
            }
            else if (audio_bitrate < 8000) {

                $j("#error-response").fadeIn("slow");
                $j('#error-response').html("<p>Min. audio bitrate limited to 8 kbps.</p>");
                return;
            }
            else {
                $j("#error-response").fadeIn("slow");
                $j('#error-response').html("<p>Please consider to fill out the whole form.</p>");
                return;
            }
        }
    }

    var url = script.bitcodin_url;
    $j.ajax({
        type: "POST",
        url: url,
        data: {
            apiKey: script.apiKey,
            contentType: "application/json",
            method: "create_encoding_profile",
            profile:  profile,
            videoConfigs: JSON.stringify(videoConfigs),
            audioConfigs: JSON.stringify(audioConfigs)
        },
        beforeSend: function() {
            $j("#big-response").fadeIn("slow");
            $j('#big-response').html("<img src='" + script.loader + "' /><p id='big-response-text'>Creating Encoding Profile...</p>");
        },
        success: function (content) {
            delete_response();
            var error = content.toString().includes("error");
            if (!error) {
                $j("#response").fadeIn("slow");
                $j('#response').html("<p>Your Encoding Profile was created successfully</p>");
            }
            else {

                $j("#error-response").fadeIn("slow");
                $j('#error-response').html('<p>Some Error occured<br>Press F12 and switch to Console to see full error message.</p>');
                console.log(content);
            }
        },
        error: function(error) {
            delete_response();
            $j("#error-response").fadeIn("slow");
            $j('#error-response').html('<p>Some Error occured<br>Press F12 and switch to Console to see full error message.</p>');
            console.log(error.statusText);
            console.log(error.responseText);
        }
    });
    // no page refresh
    return false;

}

/* delete all responses */
function delete_response() {

    $j("#response").fadeOut("slow");
    $j("#error-response").fadeOut("slow");
    $j("#big-response").fadeOut("slow");
}

/* check video bitrate */
function video_bitrate()
{
    var video_bitrate = document.getElementById("create-encoding-video-bitrate").value;
    var res = checkVideoBitrate(video_bitrate);
    if (res == 1)
    {
        document.getElementById("v-bitrate").innerHTML = video_bitrate + " kbps";
        $j("#v-bitrate").css("background-color","grey");
    }
    else if (res == 2)
    {
        document.getElementById("v-bitrate").innerHTML = video_bitrate/1000 + " Mbps";
        $j("#v-bitrate").css("background-color","grey");
    }
    else {
        document.getElementById("v-bitrate").innerHTML = "max. 20 Mbps allowed!";
        $j("#v-bitrate").css("background-color","red");
    }
}

/* check audio bitrate */
function audio_bitrate()
{
    var audio_bitrate = document.getElementById("create-encoding-audio-bitrate").value;
    if (audio_bitrate <= 256)
    {
        document.getElementById("a-bitrate").innerHTML = audio_bitrate + " kbps";
        $j("#a-bitrate").css("background-color","grey");
    }
    else {
        document.getElementById("a-bitrate").innerHTML = "max. 256 kbps allowed!";
        $j("#a-bitrate").css("background-color","red");
    }
}

/* validate bitrate (only for video bitrate) */
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

/* add dynamic audio config rows */
function addAudioConfig() {

    delete_response();
    if (audioConf_anz < 10) {

        var idSpan = 'a-bitrate' + audioConf_anz;
        var value = 'Audio Configuration' + audioConf_anz;
        var idBitrate = 'create-encoding-audio-bitrate' + audioConf_anz;
        var idCodec = 'create-encoding-audio-codec' + audioConf_anz;

        var removeID = 'remove-audio-tag' + audioConf_anz;
        var rowClass = 'audio-row' + audioConf_anz;

        var wrapper = $j("#audio-table");
        $j(wrapper).append('<tr class="' + rowClass + '"><th colspan="2"><h4>' + value + '<a id="' + removeID + '" class="remove-tag">Remove</a></h4></th></tr>');
        $j(wrapper).append('<tr class="' + rowClass + '"><th>Audio Bitrate</th><td><input type="text" id="' + idBitrate + '" name="' + idBitrate + '"/><span id="' + idSpan + '" class="bitrate">kbps</span></td></tr>');
        $j(wrapper).append('<tr class="' + rowClass + '"><th>Audio Codec</th><td><select id="' + idCodec + '" name="' + idCodec + '"><option value="aac">AAC</option></select></td></tr>');

        $j("#" + removeID).click(function(){
            $j("." + rowClass).remove();
        });

        $j("#" + idBitrate).on('keyup', function() {

            var audio_bitrate = document.getElementById(idBitrate).value;
            if (audio_bitrate <= 256)
            {
                document.getElementById(idSpan).innerHTML = audio_bitrate + " kbps";
                $j("#" + idSpan).css("background-color","grey");
            }
            else {
                document.getElementById(idSpan).innerHTML = "max. 256 kbps allowed!";
                $j("#" + idSpan).css("background-color","red");
            }
        });

        audioConf_anz++;
    }
    else {
        $j("#error-response").fadeIn("slow");
        $j('#error-response').html('<p>Max. audio configurations are limited to 10.</p>');
    }
}

/* add dynamic video config rows */
function addVideoConfig() {

    delete_response();
    if (videoConf_anz < 15) {

        var value = 'Video Representation' + videoConf_anz;
        var idBitrate = 'create-encoding-video-bitrate' + videoConf_anz;
        var idCodec = 'create-encoding-video-codec' + videoConf_anz;
        var idWidth = 'create-encoding-video-width' + videoConf_anz;
        var idHeight = 'create-encoding-video-height' + videoConf_anz;
        var idSpan = 'v-bitrate' + videoConf_anz;

        var removeID = 'remove-video-tag' + videoConf_anz;
        var rowClass = 'video-row' + videoConf_anz;

        var wrapper = $j("#video-table");
        $j(wrapper).append('<tr class="' + rowClass + '"><th colspan="2"><h4>' + value + '<a id="' + removeID + '" class="remove-tag">Remove</a></h4></th></tr>');
        $j(wrapper).append('<tr class="' + rowClass + '"><th>Resolution</th><td><input type="number" id="' + idWidth + '" name="' + idWidth + '" size="20" required/> X <input type="number" id="' + idHeight + '" name="' + idHeight + '" size="20" required/></td></tr>');
        $j(wrapper).append('<tr class="' + rowClass + '"><th>Video Bitrate</th><td><input type="text" id="' + idBitrate + '" name="' + idBitrate + '"/><span id="' + idSpan + '" class="bitrate">kbps</span></td></tr>');
        $j(wrapper).append('<tr class="' + rowClass + '"><th>Video Codec</th><td><select id="' + idCodec + '" name="' + idCodec + '"><option value="h264">H264</option><option value="hevc">HEVC</option></select></td></tr>');

        $j("#" + removeID).click(function(){
            $j("." + rowClass).remove();
        });

        $j("#" + idBitrate).on('keyup', function() {

            var video_bitrate = document.getElementById(idBitrate).value;
            var res = checkVideoBitrate(video_bitrate);
            if (res == 1)
            {
                document.getElementById(idSpan).innerHTML = video_bitrate + " kbps";
                $j("#" + idSpan).css("background-color","grey");
            }
            else if (res == 2) {
                document.getElementById(idSpan).innerHTML = video_bitrate/1000 + " Mbps";
                $j("#" + idSpan).css("background-color","grey");
            }
            else {
                document.getElementById(idSpan).innerHTML = "max. 20 Mbps allowed!";
                $j("#" + idSpan).css("background-color","red");
            }
        });

        videoConf_anz++;
    }
    else {
        $j("#error-response").fadeIn("slow");
        $j('#error-response').html('<p>Max. video configurations are limited to 15.</p>');
    }
}