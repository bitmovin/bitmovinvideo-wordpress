var apiBaseUrl = 'https://api.bitmovin.com/v1';

$j = jQuery.noConflict();

$j(document).ready(function() {
  collapseAdvancedPanels();

  populateVersions();
  setupChangeListeners();
  populatePlayerLicenses();
});

function collapseAdvancedPanels() {
  var configSections = [

    "bitmovin_player_configuration_player",
    "bitmovin_player_configuration_custom"
  ];
  for (var i = 0; i < configSections.length; i++) {
    if (!hasContent(configSections[i])) {
      $j("#" + configSections[i]).addClass("closed");
    }
  }
}

function populateVersions() {
  var apiKey = $j("#apiKey").val();

  var channelSelect = $j('#config_player_channel');
  var versionSelect = $j('#config_player_version');
  versionSelect.empty();

  var selectedChannel = channelSelect.val();

  callApi(apiKey, '/player/channels/' + selectedChannel + '/versions', function(data) {
    var versions = data.data.result.items;

    versions.sort(function(a, b) {
      return (new Date(a.createdAt)).getTime() - (new Date(b.createdAt)).getTime()
    });

    versions.reverse();

    var setPlayerVersionUrl = $j('#config_player_version_url').val();

    var knownVersionUrl = false;

    versions.forEach(function(item) {
      knownVersionUrl = item.cdnUrl === setPlayerVersionUrl;

      versionSelect.append($j('<option>', {
        value: item.cdnUrl,
        text : item.version,
        selected: knownVersionUrl
      }));
    });


    if (knownVersionUrl || !setPlayerVersionUrl) {
      updateSelectedVersionUrl();
    }
  })
}

function updateSelectedVersionUrl() {
  $j('#config_player_version_url').val($j('#config_player_version').val())
}

function setupChangeListeners() {
  $j('#config_player_channel').change(populateVersions);
  $j('#config_player_version').change(updateSelectedVersionUrl);
}

function populatePlayerLicenses() {
  var apiKey = $j("#apiKey").val();

  var keySelect = $j('#config_player_key');

  callApi(apiKey, '/player/licenses', function(data) {
    var licenses = data.data.result.items;

    licenses.forEach(function(item) {
      keySelect.append($j('<option>', {
        value: item.licenseKey,
        text : (item.hasOwnProperty("name") ? item.licenseKey + ' (' + item.name + ')': item.licenseKey),
        selected: item.licenseKey === $j('#config_player_key_selected').val()
      }));
    });

  })
}

function hasContent(configSection) {
  var contentFound = false;
  var inputTypes   = ["input[type='text']", "input[type='number']", "select", "textarea"];
  for (var i = 0; i < inputTypes.length; i++) {
    $j("#" + configSection + " " + inputTypes[i]).each(function(index) {
      if (this.value != "" && this.value != "disabled") {
        contentFound = true;
        return false;
      }
    });
    if (contentFound) {
      break;
    }
  }
  return contentFound;
}

function checkApiKey() {
  var apiKey = $j("#apiKey").val();

  callApi(apiKey, '/account/information', function() {
    $j("#bitmovinSettingsForm").submit();
  }, function(error) {
    $j("#messages").text(error.responseJSON.message);
  });
}


function callApi(apiKey, path, callback, errorCallback) {
  $j.ajax({
    url       : apiBaseUrl + path,
    type      : "GET",
    beforeSend: function(xhr) {
      xhr.setRequestHeader('X-Api-Key', apiKey);
    },
    success   : callback,
    error     : errorCallback
  });
}