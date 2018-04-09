var apiBaseUrl = 'https://api.bitmovin.com/v1';

$j = jQuery.noConflict();

$j(document).ready(function() {
    var configSections = [
               "bitmovin_player_configuration_custom"
    ];
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
        url: apiBaseUrl + '/account/information',
        type: "GET",
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-Api-Key', apiKey);
        },
        success: function(data) {
            $j("#bitmovinSettingsForm").submit();
        },
        error: function(error) {
            $j("#messages").text(error.responseJSON.message);
        }
    });
}
