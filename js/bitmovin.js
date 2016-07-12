$j = jQuery.noConflict();
$j(document).ready(function() {
    var configSections = ["bitmovin_player_configuration_drm", "bitmovin_player_configuration_ads", "bitmovin_player_configuration_vr", "bitmovin_player_configuration_style", "bitmovin_player_configuration_custom"];
    for(var i=0;i<configSections.length;i++) {
        if(!hasContent(configSections[i]))
            $j("#"+configSections[i]).addClass("closed");
    }
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

function getVersions()
{
    removeAllOptions();

    var option = document.createElement('option');
    var channel = document.getElementById("config_player_channel");
    var select = document.getElementById("config_player_version");
    if (channel.options[channel.selectedIndex].value == "Beta")
    {
        var option = document.createElement('option');
        option.text = "Latest Version 5";
        select.add(option, 0);

        option = document.createElement('option');
        option.text = "5.1";
        select.add(option, 1);

        option = document.createElement('option');
        option.text = "5.0";
        select.add(option, 2);
    }
    else if (channel.options[channel.selectedIndex].value == "Staging")
    {
        var option = document.createElement('option');
        option.text = "Latest Version 5";
        select.add(option);

        option = document.createElement('option');
        option.text = "5.1.0-rc1";
        select.add(option);

        option = document.createElement('option');
        option.text = "5.1";
        select.add(option);
    }
    else
    {
        var option = document.createElement('option');
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

        option = document.createElement('option');
        option.text = "4.2";
        select.add(option);

        option = document.createElement('option');
        option.text = "4.1";
        select.add(option);

        option = document.createElement('option');
        option.text = "4.0";
        select.add(option);
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