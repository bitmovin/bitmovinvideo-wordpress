/**
 * Created by Bitmovin on 08.08.2016.
 */

var video_anz = 0;
var outputProfiles;
var encodingProfiles;
var videoUrl_anz = 1;
var rowClasses = [];
var media_uploader = null;

$j = jQuery.noConflict();
$j(document).ready(function() {

    if (bitcodin_script.apiKey === "") {
        $j("#error-response").fadeIn("slow");
        $j('#error-response').html("<p>No valid API Key found</p>");
    }
    else {
        getEncodingProfiles();
        getOutputProfiles();
    }
    /* disable table content */
    document.getElementById("check-overview").checked = false;
    overview();
    $j("#selected-encoding-table").find("input,button,textarea,select").attr("disabled","disabled");
    $j("#selected-output-table").find("input,button,textarea,select").attr("disabled","disabled");
});

/* function to schow or hide details */
function overview() {
    if (document.getElementById("check-overview").checked) {
        $j("#selected-encoding-table").fadeIn();
        $j("#selected-output-table").fadeIn();
    }
    else {
        $j("#selected-encoding-table").hide();
        $j("#selected-output-table").hide();
    }
}

function bitcodin() {

    delete_response();

    var videoSrc = [];
    
    var url = bitcodin_script.bitcodin_url;
    var encodingProfileID = document.getElementById("bitcodin_profile_id").value;
    var outputProfileID = document.getElementById("output_profile_id").value;

    var index = 0;
    var videoUrlID = "bitcodin_video_src";
    for (; index < videoUrl_anz; index++) {

        if (index > 0) {

            videoUrlID = "bitcodin_video_src" + index;
        }
        /* Skip if dynamic video url source was deleted */
        if (document.getElementById(videoUrlID) != null && document.getElementById(videoUrlID).value != "") {

            video_anz++;
            videoSrc.push(document.getElementById(videoUrlID).value);
        }
    }

    if (video_anz != 0 && encodingProfileID != "" && outputProfileID != "") {

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
                $j("#big-response").fadeIn("slow");
                $j('#big-response').html("<img src='" + bitcodin_script.loader + "'/>" +
                    "<p id='big-response-text'>Bitcodin in progress...<br><span id='small-response-text'><i>Encoding " + video_anz + " video(s) - <b>Please stand by until the encoding is finished</b>.</i></span></p>");
            },
            success: function (content) {

                video_anz = 0;
                delete_response();
                var error = content.toString().includes("error");
                if (!error) {

                    $j("#response").fadeIn("slow");
                    $j('#response').html("<p>Encoding finished successfully</p>");
                }
                else {
                    $j("#error-response").fadeIn("slow");
                    $j('#error-response').html('<p>Some error occured. <br> Press F12 and switch to console to see full error message.</p>');
                    console.log(content);
                }
            },
            error: function (error) {
                delete_response();
                $j("#error-response").fadeIn("slow");
                $j('#error-response').html('<p>Some error occured. <br> Press F12 and switch to console to see full error message.</p>');
                console.log(error.statusText);
            }
        });
        /* no page refresh */
        return false;
    }
    else if (encodingProfileID == "") {
        $j("#error-response").fadeIn("slow");
        $j('#error-response').html("<p>You have to create an encoding profile first.</p>");
    }
    else if (outputProfileID == "") {
        $j("#error-response").fadeIn("slow");
        $j('#error-response').html("<p>You have to create an output profile first.</p>");
    }
    else {
        $j("#error-response").fadeIn("slow");
        $j('#error-response').html("<p>Maybe forgot the video source?</p>");
    }
}

function delete_response() {
    $j("#response").fadeOut("slow");
    $j("#error-response").fadeOut("slow");
    $j("#big-response").fadeOut("slow");
}

function getEncodingProfiles() {

    sendAPIRequest("get_bitcodin_profiles", "Profiles are loading...", encodingProfiles, "bitcodin_profiles");
}

function getOutputProfiles() {

    sendAPIRequest("get_output_profiles", "Profiles are loading...", outputProfiles, "output_profiles");
}

function sendAPIRequest(method, message, profile, id) {

    delete_response();
    var url = bitcodin_script.bitcodin_url;
    $j.ajax({
        type: "POST",
        url: url,
        data: {
            apiKey: bitcodin_script.apiKey,
            method: method
        },
        beforeSend: function() {
            $j("#big-response").fadeIn("slow");
            $j('#big-response').html("<img src='" + bitcodin_script.loader + "'/><p id='big-response-text'>" + message + "</p>");
        },
        success: function(content) {

            delete_response();

            var error = content.toString().includes("error");
            if (!error) {
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
            }
            else {
                $j("#error-response").fadeIn("slow");
                $j('#error-response').html('<p>Some error occured. <br> Press F12 and switch to console to see full error message.</p>');
                console.log(content);
            }
        },
        error: function(error) {
            $j("#error-response").fadeIn("slow");
            $j('#error-response').html('<p>Some error occured. <br> Press F12 and switch to console to see full error message.</p>');
            console.log(error.statusText);
            console.log(error.responseText);
        }
    });
    /* no page refresh */
    return false;
}

function showEncodingProfile() {

    deleteDynamicRows();

    var i = 0;
    var object;
    var index = 0;
    var output = document.getElementById("bitcodin_profiles");
    for (; index < encodingProfiles.length; index++) {

        object = encodingProfiles[index];
        if (object.name == output.options[output.selectedIndex].value) {

            $j('#bitcodin_profile').val(object.name);
            $j('#bitcodin_quality').val(object.videoStreamConfigs[0].preset);

            for (; i < object.videoStreamConfigs.length; i++) {

                if (i == 0) {
                    $j('#bitcodin_video_height').val(object.videoStreamConfigs[i].height);
                    $j('#bitcodin_video_width').val(object.videoStreamConfigs[i].width);
                    $j('#bitcodin_video_bitrate').val(object.videoStreamConfigs[i].bitrate / 1000);
                    $j('#bitcodin_video_codec').val(object.videoStreamConfigs[i].codec);
                    video_bitrate();
                }
                else {
                    addVideoConfig(i);
                    $j('#bitcodin_video_height' + i).val(object.videoStreamConfigs[i].height);
                    $j('#bitcodin_video_width' + i).val(object.videoStreamConfigs[i].width);
                    $j('#bitcodin_video_bitrate' + i).val(object.videoStreamConfigs[i].bitrate / 1000);
                    $j('#bitcodin_video_bitrate' + i).trigger('change');
                    $j('#bitcodin_video_codec' + i).val(object.videoStreamConfigs[i].codec);
                }

            }

            i = 0;
            for (; i < object.audioStreamConfigs.length; i++) {

                if (i == 0) {
                    $j('#bitcodin_audio_bitrate').val(object.audioStreamConfigs[0].bitrate / 1000);
                    $j('#bitcodin_audio_codec').val(object.audioStreamConfigs[0].codec);
                    audio_bitrate();
                }
                else {
                    addAudioConfig(i);
                    $j('#bitcodin_audio_bitrate' + i).val(object.audioStreamConfigs[i].bitrate / 1000);
                    $j('#bitcodin_audio_bitrate' + i).trigger('change');
                    $j('#bitcodin_audio_codec' + i).val(object.audioStreamConfigs[i].codec);
                }

            }
            $j('#bitcodin_profile_id').val(object.encodingProfileId);
            break;
        }
    }
}

function addVideoConfig(key) {

    var idSpan = 'vbitrate' + key;
    var value = 'Video Representation' + key;
    var idBitrate = 'bitcodin_video_bitrate' + key;
    var idCodec = 'bitcodin_video_codec' + key;
    var idWidth = 'bitcodin_video_width' + key;
    var idHeight = 'bitcodin_video_height' + key;

    var rowClass = "bitcodin-video-row" + key;

    var wrapper = $j("#encoding-profile-video-representation");
    $j(wrapper).append('<tr class="' + rowClass + '"><th colspan="2"><h4>' + value + '</h4></th></tr>');
    $j(wrapper).append('<tr class="' + rowClass + '"><th>Resolution</th><td><input type="number" id="' + idWidth + '" name="' + idWidth + '" size="20"/> X <input type="number" id="' + idHeight + '" name="' + idHeight + '" size="20"/></td></tr>');
    $j(wrapper).append('<tr class="' + rowClass + '"><th>Video Bitrate</th><td><input type="text" id="' + idBitrate + '" name="' + idBitrate + '"/><span id="' + idSpan + '" class="bitrate">kbps</span></td></tr>');
    $j(wrapper).append('<tr class="' + rowClass + '"><th>Video Codec</th><td><select id="' + idCodec + '" name="' + idCodec + '"><option value="h264">h264</option><option value="hevc">hevc</option></select></td></tr>');

    $j("#" + idBitrate).on('change', function() {

        var video_bitrate = document.getElementById(idBitrate).value;
        var res = checkVideoBitrate(video_bitrate);
        if (res == 1)
        {
            document.getElementById(idSpan).innerHTML = video_bitrate + " kbps";
        }
        else {
            document.getElementById(idSpan).innerHTML = video_bitrate/1000 + " Mbps";
        }
    });

    rowClasses.push(rowClass);
    $j("." + rowClass).find("input,button,textarea,select").attr("disabled","disabled");
}

function addAudioConfig(key) {

    var idSpan = 'abitrate' + key;
    var value = 'Audio Representation' + key;
    var idCodec = 'bitcodin_audio_codec' + key;
    var idBitrate = 'bitcodin_audio_bitrate' + key;

    var rowClass = "bitcodin-audio-row" + key;

    var wrapper = $j("#encoding-profile-audio-representation");
    $j(wrapper).append('<tr class="' + rowClass + '"><th colspan="2"><h4>' + value + '</h4></th></tr>');
    $j(wrapper).append('<tr class="' + rowClass + '"><th>Aideo Bitrate</th><td><input type="text" id="' + idBitrate + '" name="' + idBitrate + '"/><span id="' + idSpan + '" class="bitrate">kbps</span></td></tr>');
    $j(wrapper).append('<tr class="' + rowClass + '"><th>Aideo Codec</th><td><select id="' + idCodec + '" name="' + idCodec + '"><option value="aac">aac</option></select></td></tr>');

    $j("#" + idBitrate).on('change', function() {

        var audio_bitrate = document.getElementById(idBitrate).value;
        document.getElementById(idSpan).innerHTML = audio_bitrate + " kbps";
    });

    rowClasses.push(rowClass);
    $j("." + rowClass).find("input,button,textarea,select").attr("disabled","disabled");
}

function deleteDynamicRows() {
    var index = 0;
    for (; index < rowClasses.length; index++) {
        $j("." + rowClasses[index]).remove();
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
    }
}

function addVideoUrl() {

    var rowID = 'rowID' + videoUrl_anz;
    var value = 'Video URL ' + videoUrl_anz;
    var id = 'bitcodin_video_src' + videoUrl_anz;
    var removeID = 'remove-video-src-tag' + videoUrl_anz;
    var buttonID = 'upload-progressive' + videoUrl_anz;

    var wrapper = $j("#bitcodin-table");
    $j(wrapper).append('<tr id="' + rowID + '"><th>' + value + '</th><td><input type="text" id="' + id + '" name="' + id + '" size="50" placeholder="path/to/your/video"/>' +
        '<input type="button" id="' + buttonID + '" class="button" value="...">' +
        '<a id="' + removeID + '" class="remove-tag">X</a>' +
        '</td></tr>');

    $j("#" + buttonID).click(function(){
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
            $j("#" + id).val(attachment.url);
        });

        media_uploader.open();
    });

    $j("#" + removeID).click(function(){
        $j("#" + rowID).remove();
    });
    videoUrl_anz++;
}

/* function to skip redundant encoding and output profiles */
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
        $j("#vbitrate").css("background-color","grey");
    }
    else if (res == 2)
    {
        document.getElementById("vbitrate").innerHTML = video_bitrate/1000 + " Mbps";
        $j("#vbitrate").css("background-color","grey");
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
        $j("#abitrate").css("background-color","grey");
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
        $j("#bitcodin_video_src").val(attachment.url);
    });

    media_uploader.open();
}