var channels = [];
var versions = [];
media_uploader = null;

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

            if (document.getElementById("config_player_channel") != null) {
                createChannels();
                getVersions();
            }
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
            $j("#playerKey").val(data.key);
            $j("#bitmovinSettingsForm").submit();
            $j("#messages").text(error.responseJSON.message);
        }
    });
}

function addSchedule() {
    alert("TODO");
}

function createChannels() {
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

function getVersions() {

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

function open_media_encoded_video()
{
    media_uploader = wp.media({
        title:  "Select encoded Video for Embedding",
        frame:  "select",
        button: {
            text: "Select Encoded Video"
        },
        library: { type: "image/jpeg"},
        multiple: false
    });

    media_uploader.on("select", function(){

        /* get video url and insert into right video src input */
        var attachment = media_uploader.state().get('selection').first().toJSON();
        $j('#config_src_dash').val(attachment.description);
        $j('#config_src_hls').val(attachment.caption);
        $j('#config_src_poster').val(attachment.url);
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
        library: { type: "video"},
        multiple: false
    });

    media_uploader.on("select", function(){

        /* get video url and insert into right video src input */
        var attachment = media_uploader.state().get('selection').first().toJSON();
        $j('#config_src_prog').val(attachment.url);
        /* leave me here for getting additional video properties */
        //for ( var image_property in attachment ) {
            //console.log(image_property + ': ' + attachment[image_property]);
        //}
    });

    media_uploader.open();
}