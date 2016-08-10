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
use bitcodin\S3OutputConfig;

require_once __DIR__.'/vendor/autoload.php';

if (isset($_POST['method']) && $_POST['method'] != "")
{
    // CONFIGURATION
    Bitcodin::setApiToken($_POST['apiKey']);

    $method = $_POST['method'];
    if ($method == "bitmovin_encoding_service") {

        bitmovin_encoding_service();
    }

    else if ($method == "get_bitcodin_profiles") {

        get_bitcodin_profiles();
    }

    else if ($method == "get_output_profiles") {

        get_output_profiles();
    }

    else if ($method == 'create_encoding_profile') {

        create_encoding_profile();
    }
}

function bitmovin_encoding_service() {

    $inputConfig = new HttpInputConfig();
    $inputConfig->url = $_POST['videoSrc'];
    $input = Input::create($inputConfig);

    // CREATE VIDEO STREAM CONFIG
    /*$videoStreamConfig = new VideoStreamConfig();
    if (isset($_POST['video_height']) && $_POST['video_height'] != "")
    {
        $videoStreamConfig->height = (int)$_POST['video_height'];
    }
    if (isset($_POST['video_width']) && $_POST['video_width'] != "")
    {
        $videoStreamConfig->width = (int)$_POST['video_width'];
    }
    $videoStreamConfig->bitrate = (int)$_POST['video_bitrate'];

    // CREATE AUDIO STREAM CONFIGS
    $audioStreamConfig = new AudioStreamConfig();
    $audioStreamConfig->bitrate = (int)$_POST['audio_bitrate'];

    $encodingProfileConfig = new EncodingProfileConfig();
    $encodingProfileConfig->name = $_POST['profile'];
    $encodingProfileConfig->videoStreamConfigs[] = $videoStreamConfig;
    $encodingProfileConfig->audioStreamConfigs[] = $audioStreamConfig;

    // CREATE ENCODING PROFILE
    $encodingProfile = EncodingProfile::create($encodingProfileConfig);*/

    $encodingProfile = EncodingProfile::get($_POST['encodingProfileID']);

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

    // TRANSFER JOB OUTPUT
    $output = Output::get($_POST['outputProfileID']);
    $job->transfer($output);

    /*if ($_POST['output'] == "ftp")
    {
        $outputConfig = new FtpOutputConfig();
        $outputConfig->name = "My Wordpress FTP Output";
        $outputConfig->host = $_POST['ftp_server'];
        $outputConfig->username = $_POST['ftp_usr'];
        $outputConfig->password = $_POST['ftp_pw'];
        $outputConfig->createSubDirectory = false;

        $output = Output::create($outputConfig);

        // TRANSFER JOB OUTPUT
        $job->transfer($output);
        //echo $output->host + $output->path;
    }
    else    {
        $outputConfig = new S3OutputConfig();
        $outputConfig->name         = $_POST['aws_name'];
        $outputConfig->accessKey    = $_POST['access_key'];
        $outputConfig->secretKey    = $_POST['secret_key'];
        $outputConfig->bucket       = $_POST['bucket'];
        $outputConfig->region       = $_POST['region'];
        $outputConfig->prefix       = $_POST['prefix'];
        $outputConfig->createSubDirectory = false;
        $outputConfig->makePublic   = true;

        $output = Output::create($outputConfig);

        // TRANSFER JOB OUTPUT
        $job->transfer($output);
    }

    // send mpd and m3u8 data
    $response = new stdClass();
    $response->host =  $output->host;
    $response->path =  $output->path;
    $response->mpd  =  $job->manifestUrls->mpdUrl;
    $response->m3u8 =  $job->manifestUrls->m3u8Url;

    echo json_encode($response);*/
}

function get_bitcodin_profiles() {

    $encodingProfiles = EncodingProfile::getListAll();

    /* convert array into object array */
    $response = json_decode (json_encode($encodingProfiles), FALSE);
    echo json_encode($response);
}

function get_output_profiles() {

    $outputProfiles = Output::getListAll();

    /* convert array into object array */
    $response = json_decode (json_encode($outputProfiles), FALSE);
    echo json_encode($response);
}

function create_encoding_profile() {

    // CREATE VIDEO STREAM CONFIG
    $videoStreamConfig = new VideoStreamConfig();
    if (isset($_POST['video_height']) && $_POST['video_height'] != "")
    {
        $videoStreamConfig->height = (int)$_POST['video_height'];
    }
    if (isset($_POST['video_width']) && $_POST['video_width'] != "")
    {
        $videoStreamConfig->width = (int)$_POST['video_width'];
    }
    $videoStreamConfig->bitrate = (int)$_POST['video_bitrate'];
    $videoStreamConfig->codec = (int)$_POST['video_codec'];

    // CREATE AUDIO STREAM CONFIGS
    $audioStreamConfig = new AudioStreamConfig();
    $audioStreamConfig->bitrate = (int)$_POST['audio_bitrate'];
    $audioStreamConfig->codec = (int)$_POST['audio_codec'];

    $encodingProfileConfig = new EncodingProfileConfig();
    $encodingProfileConfig->name = $_POST['profile'];
    $encodingProfileConfig->videoStreamConfigs[] = $videoStreamConfig;
    $encodingProfileConfig->audioStreamConfigs[] = $audioStreamConfig;

    // CREATE ENCODING PROFILE
    EncodingProfile::create($encodingProfileConfig);
}