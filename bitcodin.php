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

    else if ($method == 'create_ftp_output_profile') {

        create_ftp_output_profile();
    }

    else if ($method == 'create_s3_output_profile') {

        create_s3_output_profile();
    }
}

function bitmovin_encoding_service() {

    $inputConfig = new HttpInputConfig();
    $inputConfig->url = $_POST['videoSrc'];
    $input = Input::create($inputConfig);

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

    /*

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

    $encodingProfileConfig = new EncodingProfileConfig();
    $encodingProfileConfig->name = $_POST['profile'];

    $videoConfigs = json_decode($_POST['videoConfigs']);
    $audioConfigs = json_decode($_POST['audioConfigs']);

    // CREATE VIDEO STREAM CONFIG
    foreach ($videoConfigs as $config) {

        $videoStreamConfig = new VideoStreamConfig();
        $videoStreamConfig->height = (int)$config->height;
        $videoStreamConfig->width = (int)$config->width;
        $videoStreamConfig->bitrate = (int)$config->bitrate;
        $videoStreamConfig->codec = (string)$config->codec;

        $encodingProfileConfig->videoStreamConfigs[] = $videoStreamConfig;
    }

    // CREATE AUDIO STREAM CONFIGS
    foreach ($audioConfigs as $config) {

        $audioStreamConfig = new AudioStreamConfig();
        $audioStreamConfig->bitrate = (int)$config->bitrate;
        $audioStreamConfig->codec = (string)$config->codec;

        $encodingProfileConfig->audioStreamConfigs[] = $audioStreamConfig;
    }

    // CREATE ENCODING PROFILE
    EncodingProfile::create($encodingProfileConfig);
}

function create_ftp_output_profile() {

    $outputConfig = new FtpOutputConfig();
    $outputConfig->name                 = $_POST['profile'];
    $outputConfig->host                 = $_POST['host'];
    $outputConfig->username             = $_POST['usr'];
    $outputConfig->password             = $_POST['pw'];

    if ($_POST['subdirectory'] == 'true') {

        $outputConfig->createSubDirectory = true;
    }
    else {

        $outputConfig->createSubDirectory = true;
    }

    Output::create($outputConfig);
}

function create_s3_output_profile() {

    $outputConfig = new S3OutputConfig();
    $outputConfig->name         = $_POST['profile'];
    $outputConfig->accessKey    = $_POST['accessKey'];
    $outputConfig->secretKey    = $_POST['secretKey'];
    $outputConfig->bucket       = $_POST['bucket'];
    $outputConfig->region       = $_POST['region'];
    $outputConfig->prefix       = $_POST['prefix'];

    if ($_POST['subdirectory'] == 'true') {

        $outputConfig->createSubDirectory = true;
    }
    else {

        $outputConfig->createSubDirectory = true;
    }

    Output::create($outputConfig);
}