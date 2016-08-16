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
use bitcodin\Thumbnail;
use bitcodin\ThumbnailConfig;

require_once __DIR__.'/vendor/autoload.php';

$scriptPath = dirname(__FILE__);
$path = realpath($scriptPath . '/./');
$filepath = explode("wp-content",$path);

require(''.$filepath[0].'/wp-load.php');

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

    $videoSrc = $_POST['videoSrc'];

    foreach ($videoSrc as $src) {

        $inputConfig = new HttpInputConfig();
        $inputConfig->url = $src;
        $input = Input::create($inputConfig);

        $encodingProfile = EncodingProfile::get($_POST['encodingProfileID']);

        $jobConfig = new JobConfig();
        $jobConfig->encodingProfile = $encodingProfile;
        $jobConfig->input = $input;
        $jobConfig->manifestTypes[] = ManifestTypes::M3U8;
        $jobConfig->manifestTypes[] = ManifestTypes::MPD;

        // CREATE JOB
        $job = Job::create($jobConfig);

        // CREATE THUMBNAIL
        $thumbnailConfig = new ThumbnailConfig();
        $thumbnailConfig->jobId = $job->jobId;
        $thumbnailConfig->height = 320;
        $thumbnailConfig->position = 1;
        $thumbnailConfig->async = true;
        $thumbnail = Thumbnail::create($thumbnailConfig);

        // WAIT TIL JOB IS FINISHED
        while($job->status != Job::STATUS_FINISHED) {

            $job->update();
            sleep(1);
        }

        // TRANSFER JOB OUTPUT
        $output = Output::get($_POST['outputProfileID']);
        $job->transfer($output);

        // send mpd and m3u8 data
        $response = new stdClass();
        $response->host     = $output->host;
        $response->path     = $output->path;
        $response->folder   = $job->jobFolder;
        $response->mpd      = $job->manifestUrls->mpdUrl;
        $response->m3u8     = $job->manifestUrls->m3u8Url;

        addToLibrary($response, $thumbnail);
    }
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

    Output::create($outputConfig);
}

function addToLibrary($data, $thumbnail) {

    // PARSING OUTPUT URLS FOR MPD AND M3U8 MANIFEST

    $mpd = "https://" . $data->host . "/" . $data->path . "/" . $data->folder;
    $m3u8 = "https://" . $data->host . "/" . $data->path . "/" . $data->folder;
    if (preg_match('/\/(([A-Za-z]+)?|([0-9]+)?)*((\.mpd)|(\.m3u8))/', $data->mpd, $matches)) {

        $mpd = $mpd . $matches[0];
    }
    if (preg_match('/\/(([A-Za-z]+)?|([0-9]+)?)*((\.mpd)|(\.m3u8))/', $data->m3u8, $matches)) {

        $m3u8 = $m3u8 . $matches[0];
    }

    // CREATING BITMOVIN WATERMARK

    $file = $thumbnail->thumbnailUrl;
    $file_path = wp_upload_dir()['path'] . "/" . basename($file);

    $watermark = imagecreatefrompng(plugins_url('images/watermark.png', __FILE__));
    if (!$watermark) {
        echo "error creating watermark";
    }

    $watermark_width = imagesx($watermark);
    $watermark_height = imagesy($watermark);

    $image_path = $file;
    $image = imagecreatefromjpeg($image_path);
    if (!$image) {
        echo "error creating thumbnail";
    }

    $size = getimagesize($image_path);

    $dest_x = 0;
    $dest_y = $size[1] - $watermark_height;

    imagealphablending($image, true);
    imagealphablending($watermark, true);

    imagecopy($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height);
    imagejpeg($image, $file_path);

    imagedestroy($image);
    imagedestroy($watermark);

    // UPLOAD TO MEDIA LIBRARY

    $filename = basename($file_path);
    $upload_file = wp_upload_bits($filename, null, file_get_contents($file_path));
    if (!$upload_file['error']) {
        $wp_filetype = wp_check_filetype($filename, null);
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_parent' => null,
            'post_title' => "Bitcoded" . $matches[0] . "/mpd",
            'post_content' => (string)$mpd,
            'post_excerpt' => (string)$m3u8,
            'post_status' => 'inherit'
        );
        $attachment_id = wp_insert_attachment($attachment, $upload_file['file'], null);
        if (!is_wp_error($attachment_id)) {
            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file['file']);
            wp_update_attachment_metadata($attachment_id, $attachment_data);
        }
    }
}