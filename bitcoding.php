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
use bitcodin\AwsRegion;

require_once __DIR__.'/vendor/autoload.php';

if (isset($_POST['method']) && $_POST['method'] != "")
{
    $method = $_POST['method'];
    if ($method == "bitmovin_encoding_service") {

        $apiKey = $_POST['apiKey'];
        bitmovin_encoding_service($apiKey);
    }
}

function bitmovin_encoding_service($apiKey) {

    // CONFIGURATION
    Bitcodin::setApiToken($apiKey);

    $inputConfig = new HttpInputConfig();
    $inputConfig->url = $_POST['video_src'];
    $input = Input::create($inputConfig);

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

    // CREATE AUDIO STREAM CONFIGS
    $audioStreamConfig = new AudioStreamConfig();
    $audioStreamConfig->bitrate = (int)$_POST['audio_bitrate'];

    $encodingProfileConfig = new EncodingProfileConfig();
    $encodingProfileConfig->name = $_POST['profile'];
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

    if ($_POST['output'] == "ftp")
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
    }
    else    {
        $outputConfig = new S3OutputConfig();
        $outputConfig->name         = $_POST['aws_name'];
        $outputConfig->accessKey    = $_POST['access_key'];
        $outputConfig->secretKey    = $_POST['secret_key'];
        $outputConfig->bucket       = $_POST['bucket'];
        $outputConfig->region       = $_POST['region'];
        $outputConfig->makePublic   = false;

        $output = Output::create($outputConfig);

        // TRANSFER JOB OUTPUT
        $job->transfer($output);
    }
}