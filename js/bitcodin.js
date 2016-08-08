/**
 * Created by Bitmovin on 08.08.2016.
 */
$j = jQuery.noConflict();
$j(document).ready(function() {

    if (bitcodin_script.apiKey == "") {
        $j('#response').html("<p id='response'>No valid API Key found</p>");
    }

});

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
        $j('#config_video_src').val(attachment.url);
    });

    media_uploader.open();
}