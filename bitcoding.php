<?php
/**
 * Created by PhpStorm.
 * User: Superuser
 * Date: 19.07.2016
 * Time: 09:19
 */

use bitcodin\Bitcodin;
use bitcodin\VideoStreamConfig;
use bitcodin\AudioStreamConfig;
use bitcodin\Job;
use bitcodin\JobConfig;
use bitcodin\Input;
use bitcodin\HttpInputConfig;
use bitcodin\EncodingProfile;
use bitcodin\EncodingProfileConfig;
use bitcodin\ManifestTypes;
use bitcodin\Output;
use bitcodin\FtpOutputConfig;

require_once __DIR__.'/vendor/autoload.php';

if (isset($_POST['method']) && $_POST['method'] != "")
{
    $method = $_POST['method'];
    if ($method == "bitmovin_encoding_service") {

        $apiKey = $_POST['apiKey'];
        $output = $_POST['output'];
        $profile = $_POST['profile'];
        $video_width = $_POST['video_width'];
        $video_height = $_POST['video_height'];
        $video_bitrate = $_POST['video_bitrate'];
        $audio_bitrate = $_POST['audio_bitrate'];
        $video_src = $_POST['video_src'];
        $ftp_server = $_POST['ftp_server'];
        $ftp_usr = $_POST['ftp_usr'];
        $ftp_pw = $_POST['ftp_pw'];
        $access_key = $_POST['access_key'];
        $secret_key = $_POST['secret_key'];
        $bucket = $_POST['bucket'];
        $prefix = $_POST['prefix'];
        bitmovin_encoding_service($apiKey, $profile);
    }
    else {
        phpConsole("Einfacher AJAX-Aufruf war erfolgreich!");
    }
}
else {
    echo "Einfacher AJAX-Aufruf war nicht erfolgreich!";
}

function phpConsole($msg) {
    echo '<script type="text/javascript">console.log("' . $msg . '")</script>';
}

function phpAlert($msg) {
    echo '<script type="text/javascript">alert("' . $msg . '")</script>';
}

function bitmovin_encoding_service($apiKey, $profile) {

    echo $apiKey;
    // CONFIGURATION
    /*Bitcodin::setApiToken($apiKey);

    $inputConfig = new HttpInputConfig();
    $inputConfig->url = 'https://www.dropbox.com/s/aaw7mj3k0iq953r/Erdbeermarmelade%21.mp4?dl=1';//'http://eu-storage.bitcodin.com/inputs/Sintel.2010.720p.mkv';
    $input = Input::create($inputConfig);

    // CREATE VIDEO STREAM CONFIG
    $videoStreamConfig = new VideoStreamConfig();
//$videoStreamConfig->height = 720; //if you omit either width or height, our service will use the aspect ratio of your input-file
    $videoStreamConfig->width = 1280;
    $videoStreamConfig->bitrate = 1024000;

    // CREATE AUDIO STREAM CONFIGS
    $audioStreamConfig = new AudioStreamConfig();
    $audioStreamConfig->bitrate = 256000;

    $encodingProfileConfig = new EncodingProfileConfig();
    $encodingProfileConfig->name = 'My first Encoding Profile';
    $encodingProfileConfig->videoStreamConfigs[] = $videoStreamConfig;
    $encodingProfileConfig->audioStreamConfigs[] = $audioStreamConfig;

    // CREATE ENCODING PROFILE
    $encodingProfile = EncodingProfile::create($encodingProfileConfig);

    $jobConfig = new JobConfig();
    $jobConfig->encodingProfile = $encodingProfile;
    $jobConfig->input = $input;
    $jobConfig->manifestTypes[] = ManifestTypes::M3U8;
    $jobConfig->manifestTypes[] = ManifestTypes::MPD;

    // CREATE JOB
    $job = Job::create($jobConfig);

    //WAIT TIL JOB IS FINISHED
    do{
        $job->update();
        sleep(1);
    } while($job->status != Job::STATUS_FINISHED);

    $outputConfig = new FtpOutputConfig();
    $outputConfig->name = "TestFtpOutput";
    $outputConfig->host = 'whocares.bplaced.net/live-access/wordpress/wordpress/wp-content/uploads';
    $outputConfig->username = 'whocares';
    $outputConfig->password = 'Royalflash93#';

    $output = Output::create($outputConfig);

    // TRANSFER JOB OUTPUT
    $job->transfer($output);
    echo "Video wurde nach " + $output->host + " übertragen.";*/
}

?>