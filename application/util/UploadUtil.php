<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 13/2/2016
 * Time: 1:02 PM
 *
 * Example At http://www.verot.net/php_class_upload_samples.htm
 *
 * upload img xample code
 * if (is_uploaded_file($_FILES['img_name']['tmp_name'])) {
 *     if(AppUtils::isImageFile($_FILES['img_name']['tmp_name'])){
 *        UploadUtils::uploadImgFiles($_FILES['img_name'], $products->getCreatedDate());
 *    }
 * }
 *
 * delete img file xample code
 * UploadUtils::delImgfileFromYearMonthFolder("1_1455355501_800ef2a9ac903cf4804bf8213985ce0c.jpg",$products->getCreatedDate());
 *
 * delete profile img file xample code
 * UploadUtils::delProfileImgfile("1_1455355465_692091b756c6db94f00df592b3e844e7.jpg",$products->getCreatedDate());
 *
 */

namespace application\util;

use application\util\AppUtil as AppUtils;
use application\util\ControllerUtil as ControllerUtils;
use application\util\DateUtils as DateUtil;
use application\util\MessageUtils as MessageUtil;
use application\util\upload\Upload;
use finfo;

class UploadUtil
{
    public static $UPLOAD_PATH = __UPLOAD_PATH;
    public static $UPLOAD_PATH_IMAGE = __UPLOAD_PATH_IMG;
    public static $DISPLAY_PATH_IMAGE = __DISPLAY_PATH_IMG;
    public static $UPLOAD_PATH_FILES = __UPLOAD_PATH_FILES;
    public static $DISPLAY_PATH_FILES = __DISPLAY_PATH_FILES;
    public static $XLSX = "xlsx";

    public static function uploadImgFiles($files = null, $yearMonthFolder = null, $limitDimention = 0, $nameCustom = null)
    {

        $uploadPath = self::$UPLOAD_PATH_IMAGE;

        //generate new img name with user id + time now + random string
        if ($nameCustom) {
            $imagNameGenerate = $nameCustom;
        } else {
            $imagNameGenerate = self::generateFileName();
        }


        if ($yearMonthFolder) {
            $yearMonthFolder = DateUtil::getYearAndMonthFromDate($yearMonthFolder);
            //create folder if not exit
            AppUtils::checkYearMonthFolder($uploadPath, $yearMonthFolder);
            $uploadPath = $uploadPath . "/" . $yearMonthFolder;
        }

        $upload = new Upload($files);
        if ($upload->uploaded) {

//            echoln('original file name > '.$upload->file_src_name);

            $upload->file_new_name_body = $imagNameGenerate;
            $upload->image_resize = true;
            $upload->image_convert = 'jpg';
            $upload->image_ratio = true;

            //height 960, width 960, keeping ratio
            if ($limitDimention > 0) {
                $upload->image_y = $limitDimention;
                $upload->image_x = $limitDimention;
            } else {
                $upload->image_y = MessageUtil::getConfig('upload_image.default_height');
                $upload->image_x = MessageUtil::getConfig('upload_image.default_width');
            }
            $upload->Process($uploadPath);
            if ($upload->processed) {
                ControllerUtils::setErrorMessage($upload->error);
            }

            if (MessageUtil::getConfig('upload_image.create_thumbnail')) {
                //thumnail create if true
                $upload->file_new_name_body = $imagNameGenerate . MessageUtil::getConfig('upload_image.create_thumbnail_exname');
                $upload->image_resize = true;
                $upload->image_convert = 'jpg';
                $upload->image_ratio_y = true;
                $upload->image_x = MessageUtil::getConfig('upload_image.create_thumbnail_width');
                $upload->Process($uploadPath);
                if (!$upload->processed) {
                    ControllerUtils::setErrorMessage($upload->error);
                }

            }

            //end thumnail create
            $upload->Clean();
            return $imagNameGenerate . '.jpg';
        }
        return false;
    }

    public static function uploadProfilePic($files, $yearMonthFolder = null, $limitDimention = 0, $nameCustom = null)
    {

        $uploadPath = self::$UPLOAD_PATH_IMAGE;
        //generate new img name with user id + time now + random string
        if ($nameCustom) {
            $imagNameGenerate = $nameCustom;
        } else {
            $imagNameGenerate = self::generateFileName();
        }
        if ($yearMonthFolder) {
            $yearMonthFolder = DateUtil::getYearAndMonthFromDate($yearMonthFolder);
            //create folder if not exit
            AppUtils::checkYearMonthFolder($uploadPath, $yearMonthFolder);
            $uploadPath = $uploadPath . "/" . $yearMonthFolder;
        }
        //generate new img name with user id + time now + random string

        $upload = new Upload($files);
        if ($upload->uploaded) {

            //full image upload with height 960, width 960, keeping ratio
            $upload->file_new_name_body = $imagNameGenerate;
            $upload->image_resize = true;
            $upload->image_convert = 'jpg';
            $upload->image_ratio = true;

            if ($limitDimention > 0) {
                $upload->image_y = $limitDimention;
                $upload->image_x = $limitDimention;
            } else {
                $upload->image_y = MessageUtil::getConfig('upload_image.default_height');
                $upload->image_x = MessageUtil::getConfig('upload_image.default_width');
            }
            $upload->Process($uploadPath);
            if (!$upload->processed) {
                ControllerUtils::setErrorMessage($upload->error);
            }
            //end full image

            //thumnail avatar_thumnail_main image upload with width 160, height auto
            $upload->file_new_name_body = $imagNameGenerate . '_x';
            $upload->image_resize = true;
            $upload->image_convert = 'jpg';
            $upload->image_ratio_y = true;
            $upload->image_x = MessageUtil::getConfig('upload_image.avatar_thumnail_main');
            $upload->Process($uploadPath);
            if (!$upload->processed) {
                ControllerUtils::setErrorMessage($upload->error);
            }
            //end thumnail avatar_thumnail_main

            //thumnail avatar_thumnail_second image upload with width 40, height auto
            $upload->file_new_name_body = $imagNameGenerate . '_xx';
            $upload->image_resize = true;
            $upload->image_convert = 'jpg';
            $upload->image_ratio_y = true;
            $upload->image_x = MessageUtil::getConfig('upload_image.avatar_thumnail_second');
            $upload->Process($uploadPath);
            if (!$upload->processed) {
                ControllerUtils::setErrorMessage($upload->error);
            }
            //end thumnail avatar_thumnail_second

            //thumnail avatar_thumnail_third image upload with width 32, height auto
            $upload->file_new_name_body = $imagNameGenerate . '_xxx';
            $upload->image_resize = true;
            $upload->image_convert = 'jpg';
            $upload->image_ratio_y = true;
            $upload->image_x = MessageUtil::getConfig('upload_image.avatar_thumnail_third');
            $upload->Process($uploadPath);
            if ($upload->processed) {
                $upload->Clean();
                return $imagNameGenerate . '.jpg';
            } else {
                ControllerUtils::setErrorMessage($upload->error);
            }
            //end thumnail avatar_thumnail_third
        }
        return false;
    }

    public static function getProfilePicApi($imgName, $dateCreate, $isUpload = false)
    {
        $displayPath = AppUtils::getServerIp() . MessageUtil::getConfig('base_data_display');
        $uploadPath = MessageUtil::getConfig('base_data_path');

        if ($imgName) {
            $splitFileName = explode(".", $imgName);
            $filePreName = $splitFileName[0];

            $x = $filePreName . '_x.jpg';
            $xx = $filePreName . '_xx.jpg';
            $xxx = $filePreName . '_xxx.jpg';

            $imgFolder = $displayPath . "/img";
            $checkFolder = $uploadPath . "/img";
            $filePath = "";
            $checkPath = "";
            if ($dateCreate) {
                $filePath .= $imgFolder . "/" . DateUtil::getYearAndMonthFromDate($dateCreate) . "/";
                $checkPath .= $checkFolder . "/" . DateUtil::getYearAndMonthFromDate($dateCreate) . "/";
            }
            return [
                'path' => self::isFileExist($checkPath . $imgName) ? ($isUpload ? $checkPath . $imgName : $filePath . $imgName) : self::defaultProfilePic($displayPath, $isUpload)['path'],
                'x' => self::isFileExist($checkPath . $x) ? ($isUpload ? $checkPath . $x : $filePath . $x) : self::defaultProfilePic($displayPath, $isUpload)['x'],
                'xx' => self::isFileExist($checkPath . $xx) ? ($isUpload ? $checkPath . $xx : $filePath . $xx) : self::defaultProfilePic($displayPath, $isUpload)['xx'],
                'xxx' => self::isFileExist($checkPath . $xxx) ? ($isUpload ? $checkPath . $xxx : $filePath . $xxx) : self::defaultProfilePic($displayPath, $isUpload)['xxx'],
            ];
        } else {
            return self::defaultProfilePic($displayPath, $isUpload);
        }
    }

    public static function defaultProfilePic($displayPath, $isUpload)
    {
        return [
            'path' => $isUpload ? null : $displayPath . "/img/default.jpg",
            'x' => $isUpload ? null : $displayPath . "/img/default_x.jpg",
            'xx' => $isUpload ? null : $displayPath . "/img/default_xx.jpg",
            'xxx' => $isUpload ? null : $displayPath . "/img/default_xxx.jpg",
        ];
    }

    public static function isFileExist($filePath)
    {
        return file_exists($filePath);
    }

    public static function generateFileName()
    {
        return (ControllerUtils::getUserIdSession() ? ControllerUtils::getUserIdSession() : 0) . '_' . DateUtil::getTimeNow() . '_' . AppUtils::generateRandID();
    }

    public static function getImageApi($imgName, $dateCreate, $isUpload = false)
    {
        $displayPath = AppUtils::getServerIp() . MessageUtil::getConfig('base_data_display');
        $uploadPath = MessageUtil::getConfig('base_data_path');

        if ($imgName) {
            $splitFileName = explode(".", $imgName);
            $filePreName = $splitFileName[0];
            $thumbName = $filePreName . MessageUtil::getConfig('upload_image.create_thumbnail_exname') . '.jpg';

            $imgFolder = $displayPath . "/img";
            $checkFolder = $uploadPath . "/img";
            $filePath = "";
            $checkPath = "";
            if ($dateCreate) {
                $filePath .= $imgFolder . "/" . DateUtil::getYearAndMonthFromDate($dateCreate) . "/";
                $checkPath .= $checkFolder . "/" . DateUtil::getYearAndMonthFromDate($dateCreate) . "/";
            }
            return [
                'path' => self::isFileExist($checkPath . $imgName) ? ($isUpload ? $checkPath . $imgName : $filePath . $imgName) : self::defaultImage($displayPath, $isUpload)['path'],
                'thumbnail' => self::isFileExist($checkPath . $thumbName) ? ($isUpload ? $checkPath . $thumbName : $filePath . $thumbName) : self::defaultImage($displayPath, $isUpload)['thumbnail'],
            ];
        } else {
            return self::defaultImage($displayPath, $isUpload);
        }
    }

    public static function defaultImage($displayPath, $isUpload)
    {
        return [
            'path' => $isUpload ? null : $displayPath . "/img/no_picture.jpg",
            'thumbnail' => $isUpload ? null : $displayPath . "/img/no_picture_thumb.jpg",
        ];
    }

    public static function delImgfileFromYearMonthFolder($file_name, $yearMonthFolder = null)
    {
        $splitFileName = explode(".", $file_name);
        $filePreName = $splitFileName[0];
        $imgThumbName = $filePreName . MessageUtil::getConfig('upload_image.create_thumbnail_exname') . '.jpg';


        $path = self::$UPLOAD_PATH_IMAGE;
        if ($yearMonthFolder) {
            $yearMonthFolder = DateUtil::getYearAndMonthFromDate($yearMonthFolder);
            $path = $path . "/" . $yearMonthFolder;
        }
        $pathDelete = "";
        $pathThumbDelete = "";
        if ($file_name) {
            $pathDelete = $path . "/" . $file_name;
            $pathThumbDelete = $path . "/" . $imgThumbName;
        }
        AppUtils::doDelfileFromPath($pathThumbDelete);
        return AppUtils::doDelfileFromPath($pathDelete);
    }

    public static function delProfileImagefile($file_name, $yearMonthFolder = null)
    {

        $splitFileName = explode(".", $file_name);
        $filePreName = $splitFileName[0];
        $fileExtentionName = $splitFileName[1];


        $path = self::$UPLOAD_PATH_IMAGE;
        if ($yearMonthFolder) {
            $yearMonthFolder = DateUtil::getYearAndMonthFromDate($yearMonthFolder);
            $path = $path . "/" . $yearMonthFolder;
        }
        //delete full img
        AppUtils::doDelfileFromPath($path . "/" . $file_name);
        //delete thumb x
        AppUtils::doDelfileFromPath($path . "/" . $filePreName . '_' . 'x.' . $fileExtentionName);
        //delete thumb xx
        AppUtils::doDelfileFromPath($path . "/" . $filePreName . '_' . 'xx.' . $fileExtentionName);
        //delete thumb xxx
        AppUtils::doDelfileFromPath($path . "/" . $filePreName . '_' . 'xxx.' . $fileExtentionName);

        return true;
    }

    public static function uploadFiles($files, $yearMonthFolder = null)
    {

        $uploadPath = self::$UPLOAD_PATH_FILES;

        //generate new img name with user id + time now + random string
        $imagNameGenerate = self::generateFileName() . "." . self::getFileExtension($files);

        if ($yearMonthFolder) {
            $yearMonthFolder = DateUtil::getYearAndMonthFromDate($yearMonthFolder);
            //create folder if not exit
            AppUtils::checkYearMonthFolder($uploadPath, $yearMonthFolder);
            $uploadPath = $uploadPath . "/" . $yearMonthFolder;
        }

        $target_file = $uploadPath . '/' . $imagNameGenerate;
        if (move_uploaded_file($files['tmp_name'], $target_file)) {
            return $imagNameGenerate;
        } else {
            echo "Sorry, there was an error uploading your file.";
        }

        return false;
    }

    public static function delfileFromYearMonthFolder($file_name, $yearMonthFolder = null)
    {

        $path = self::$UPLOAD_PATH_FILES;
        if ($yearMonthFolder) {
            $yearMonthFolder = DateUtil::getYearAndMonthFromDate($yearMonthFolder);
            $path = $path . "/" . $yearMonthFolder;
        }
        $pathDelete = "";
        if ($file_name) {
            $pathDelete = $path . "/" . $file_name;
        }
        return AppUtils::doDelfileFromPath($pathDelete);
    }

    public static function getFileExtension($fileName)
    {//param $_FILES["file"]
        $path_parts = pathinfo($fileName["name"]);
        return $path_parts['extension'];
    }

    public static function displayFilePathUpload($fileName, $yearMonthFolder = null)
    {

        $uploadFileFolder = self::$UPLOAD_PATH_FILES . "/";
        if ($yearMonthFolder) {
            $uploadFileFolder .= DateUtil::getYearAndMonthFromDate($yearMonthFolder) . "/";
        }
        if ($fileName) {
            $imgPath = $uploadFileFolder . $fileName;
            return $imgPath;
        } else {
            return false;
        }

    }

    //not work in ubuntu
    public static function isFileExitFromUrl_1($url)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // don't download content
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (curl_exec($ch) !== FALSE) {
            curl_close($ch);
            return true;
        } else {
            curl_close($ch);
            return false;
        }
    }

    public static function isFileExitFromUrl($url)
    {
//        $headers=@get_headers($url);
//        return @stripos($headers[0],"200 OK") ? true : false;


//        $isExits = @file_get_contents($url,0,null,0,1);


        $isExits = (bool)@preg_match('~HTTP/1\.\d\s+200\s+OK~', @current(@get_headers($url)));

//        $isExits = true;

//        $isExits = @strstr(@current(@get_headers($url)), "200");
//
//        if($isExits){
//            echoln($url.' TRUE');
//        }else{
//            echoln($url.' FASLE');
//        }
        return $isExits;
    }

    public static function isFileExitFromUrl_OK($url)
    {
//        return strpos(@get_headers($url)[0],'200') === false ? false : true; //error in php 5.3
        return false;
    }

    public static function downloadFileFromPath($fullPath)
    {

        ignore_user_abort(true);
        set_time_limit(0); // disable the time limit for this script
        if ($fd = fopen($fullPath, "r")) {
            $fsize = filesize($fullPath);
            $path_parts = pathinfo($fullPath);
            $ext = strtolower($path_parts["extension"]);
            switch ($ext) {
                case "pdf":
                    header("Content-type: application/pdf");
                    header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] . "\""); // use 'attachment' to force a file download
                    break;
                case "xlsx":
                    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
                    header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] . "\""); // use 'attachment' to force a file download
                    break;
                case "xls":
                    header("Content-type: application/vnd.ms-excel");
                    header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] . "\""); // use 'attachment' to force a file download
                    break;
                // add more headers for other content types here
                default;
                    header("Content-type: application/octet-stream");
                    header("Content-Disposition: filename=\"" . $path_parts["basename"] . "\"");
                    break;
            }
            header("Content-length: $fsize");
            header("Cache-control: private"); //use this to open files directly
            while (!feof($fd)) {
                $buffer = fread($fd, 2048);
                echo $buffer;
            }
        }
        fclose($fd);

        exit;
    }

    public static function downloadFileFromFile($file)
    {
        $readableStream = fopen($file, 'rb');
        $writableStream = fopen('php://output', 'wb');

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="test.zip"');
        stream_copy_to_stream($readableStream, $writableStream);
        ob_flush();
        flush();
    }
    //Thailand Post
    public static function uploadApiFiles($files, $yearMonthFolder = null, $fileName = null)
    {

        $uploadPath = self::$UPLOAD_PATH_FILES;
        //generate new img name with user id + time now + random string
        $imagNameGenerate = $fileName ? $fileName : (self::generateFileName() . "." . self::getFileInfoMimeType($files));
        if ($yearMonthFolder) {
            $yearMonthFolder = DateUtil::getYearAndMonthFromDate($yearMonthFolder);
            //create folder if not exit
            AppUtils::checkYearMonthFolder($uploadPath, $yearMonthFolder);
            $uploadPath = $uploadPath . "/" . $yearMonthFolder;
        }

        $target_file = $uploadPath . '/' . $imagNameGenerate;
        if (file_put_contents($target_file, $files)) {
            $ymFolder = $yearMonthFolder ? $yearMonthFolder : '';
            $displayPath = AppUtil::getServerIp() . MessageUtils::getConfig('base_data_display');
            return $displayPath . "/files/" . $ymFolder . "/" . $imagNameGenerate;
        } else {
            ControllerUtils::displayError("Sorry, there was an error uploading your file.");
        }

        return null;
    }

    private static function getFileInfoMimeType($files)
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $info = $finfo->buffer($files);
        $getExtension = explode("/", $info);
        return count($getExtension) > 0 ? $getExtension[1] : null;
    }

    public static function getUploadFileName($userId, $randomId = true)
    {
        return $userId . SystemConstant::UNDER_SCORE . DateUtils::getTimeNow() . ($randomId ? SystemConstant::UNDER_SCORE . AppUtil::generateRandStr(5) : '');
    }
}