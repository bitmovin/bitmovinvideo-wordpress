/**
 * Created by Bitmovin on 11.08.2016.
 */
$j = jQuery.noConflict();

function createFTPOutput() {

    delete_response();
    var profile = document.getElementById('config_ftp_name').value;
    var host = document.getElementById('config_ftp_host').value;
    var usr = document.getElementById('config_ftp_usr').value;
    var pw = document.getElementById('config_ftp_pw').value;
    var subdirectory = "false";
    if (document.getElementById('config_ftp_subdirectory').checked) {
        subdirectory = "true";
    }

    if (profile != "" && host != "" && usr != "" && pw != "") {

        var url = script.bitcodin_url;
        $j.ajax({
            type: "POST",
            url: url,
            data: {
                apiKey: script.apiKey,
                method: "create_ftp_output_profile",
                profile:        profile,
                host:           host,
                usr:            usr,
                pw:             pw,
                subdirectory:   subdirectory
            },
            beforeSend: function() {
                $j('.ftp-response').html("<img src='" + script.small_loader + "' /><p>Creating FTP Output Profile...</p>");
            },
            success: function (content) {

                var error = content.toString().includes("error");
                if (!error) {
                    $j('.ftp-response').html("<p>Your FTP Output Profile was created successfully</p>");
                    console.log(content);
                }
                else {
                    delete_response();
                    $j('.ftp-error-response').html('<p>Some Error occured<br>Press F12 and switch to Console to see full error message.</p>');
                    console.log(content);
                }
            },
            error: function(error) {
                delete_response();
                $j('.ftp-error-response').html(error);
            }
        });
        // no page refresh
        return false;
    }
    else {

        $j('.ftp-error-response').html("<p>Please consider to fill out the whole form.</p>");
    }
}

function createS3Output() {

    delete_response();
    var profile = document.getElementById('config_aws_name').value;
    var accessKey = document.getElementById('config_aws_access_key').value;
    var secretKey = document.getElementById('config_aws_secret_key').value;
    var bucket = document.getElementById('config_aws_bucket').value;
    var prefix = document.getElementById('config_aws_prefix').value;
    var region = document.getElementById('config_aws_region').value;
    var subdirectory = "false";
    if (document.getElementById('config_ftp_subdirectory').checked) {
        subdirectory = "true";
    }

    if (profile != "" && accessKey != "" && secretKey != "" && bucket != "" && prefix != "") {

        var url = script.bitcodin_url;
        $j.ajax({
            type: "POST",
            url: url,
            data: {
                apiKey: script.apiKey,
                method: "create_s3_output_profile",
                profile:        profile,
                accessKey:      accessKey,
                secretKey:      secretKey,
                bucket:         bucket,
                prefix:         prefix,
                region:         region,
                subdirectory:   subdirectory
            },
            beforeSend: function() {
                $j('.s3-response').html("<img src='" + script.small_loader + "' /><p>Creating S3 Output Profile...</p>");
            },
            success: function (content) {

                var error = content.toString().includes("error");
                if (!error) {
                    $j('.s3-response').html("<p>Your S3 Output Profile was created successfully</p>");
                    console.log(content);
                }
                else {
                    delete_response();
                    $j('.s3-error-response').html('<p>Some Error occured<br>Press F12 and switch to Console to see full error message.</p>');
                    console.log(content);
                }
            },
            error: function(error) {
                delete_response();
                $j('.s3-error-response').html(error);
            }
        });
        // no page refresh
        return false;
    }
    else {

        $j('.s3-error-response').html("<p>Please consider to fill out the whole form.</p>");
    }
}

function delete_response() {

    $j('.ftp-response').html("");
    $j('.ftp-error-response').html("");

    $j('.s3-response').html("");
    $j('.s3-error-response').html("");
}