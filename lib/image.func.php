<?php
require_once 'string.func.php';
//通过GD库做验证码
/**
 * Created by PhpStorm.
 * User: melonydi
 * Date: 2016/7/12
 * Time: 22:19
 */
function verifyImage($type = 1, $length = 4, $pixel = 0, $line = 0, $sess_name = "verify")
{
//创建画布
    $width = 80;
    $height = 30;
    $image = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
//用填充举行填充画布
    imagefilledrectangle($image, 1, 1, $width - 2, $height - 2, $white);
    $chars = buildRandomString($type, $length);
    $_SESSION[$sess_name] = $chars;
    $fontfiles = array("msyh.ttc", "msyhbd.ttc", "msyhl.ttc", "SIMLI.TTF", "simsun.ttc", "SIMYOU.TTF", "STZHONGS.TTF");
    for ($i = 0; $i < $length; $i++) {
        $size = mt_rand(14, 18);
        $angle = mt_rand(-15, 15);
        $x = 5 + $i * $size;
        $y = mt_rand(20, 26);
        $fontfile = "../fonts/" . $fontfiles[mt_rand(0, count($fontfiles) - 1)];
        $color = imagecolorallocate($image, mt_rand(50, 90), mt_rand(80, 200), mt_rand(90, 180));
        $text = substr($chars, $i, 1);
        imagettftext($image, $size, $angle, $x, $y, $color, $fontfile, $text);
    }
//干扰点
    if ($pixel) {
        for ($i = 0; $i < $pixel; $i++) {
            imagesetpixel($image, mt_rand(0, $width - 1), mt_rand(0, $height - 1), $black);
        }
    }
//干扰线
    if ($line) {
        for ($i = 1; $i < $line; $i++) {
            $color = imagecolorallocate($image, mt_rand(50, 90), mt_rand(80, 200), mt_rand(90, 180));
            imageline($image, mt_rand(0, $width - 1), mt_rand(0, $height - 1), mt_rand(0, $width - 1), mt_rand(0, $height - 1), $color);
        }
    }

//告诉浏览器我们显示的是什么资源
    header("content-type:image/gif");
    imagegif($image);
    imagedestroy($image);
}

/**
 * 生成缩略图
 * @param string $filename
 * @param string $destination
 * @param int $dst_w
 * @param int $dst_h
 * @param bool $isReservedSource
 * @param number $scale
 * @return string
 */
function thumb($filename,$destination=null,$dst_w=null,$dst_h=null,$isReservedSource=true,$scale=0.5){
    list($src_w,$src_h,$imagetype)=getimagesize($filename);
    if(is_null($dst_w)||is_null($dst_h)){
        $dst_w=ceil($src_w*$scale);
        $dst_h=ceil($src_h*$scale);
    }
    $mime=image_type_to_mime_type($imagetype);
    $createFun=str_replace("/", "createfrom", $mime);
    $outFun=str_replace("/", null, $mime);
    $src_image=$createFun($filename);
    $dst_image=imagecreatetruecolor($dst_w, $dst_h);
    imagecopyresampled($dst_image, $src_image, 0,0,0,0, $dst_w, $dst_h, $src_w, $src_h);
    if($destination&&!file_exists(dirname($destination))){
        mkdir(dirname($destination),0777,true);
    }
    $dstFilename=$destination==null?getUniName().".".getExt($filename):$destination;
    $outFun($dst_image,$dstFilename);
    imagedestroy($src_image);
    imagedestroy($dst_image);
    if(!$isReservedSource){
        unlink($filename);
    }
    return $dstFilename;
}

/**
 *添加文字水印
 * @param string $filename
 * @param string $text
 * @param string  $fontfile
 */
function waterText($filename,$text="imooc.com",$fontfile="msyh.ttc"){
    $fileInfo = getimagesize ( $filename );
    $mime = $fileInfo ['mime'];
    $createFun = str_replace ( "/", "createfrom", $mime );
    $outFun = str_replace ( "/", null, $mime );
    $image = $createFun ( $filename );
    $color = imagecolorallocatealpha ( $image, 255, 0, 0, 50 );
    $fontfile = "../fonts/{$fontfile}";
    imagettftext ( $image, 14, 0, 0, 14, $color, $fontfile, $text );
    $outFun ( $image, $filename );
    imagedestroy ( $image );
}

/**
 *添加图片水印
 * @param string $dstFile
 * @param string $srcFile
 * @param int $pct
 */
function waterPic($dstFile,$srcFile="../images/logo.jpg",$pct=30){
    $srcFileInfo = getimagesize ( $srcFile );
    $src_w = $srcFileInfo [0];
    $src_h = $srcFileInfo [1];
    $dstFileInfo = getimagesize ( $dstFile );
    $srcMime = $srcFileInfo ['mime'];
    $dstMime = $dstFileInfo ['mime'];
    $createSrcFun = str_replace ( "/", "createfrom", $srcMime );
    $createDstFun = str_replace ( "/", "createfrom", $dstMime );
    $outDstFun = str_replace ( "/", null, $dstMime );
    $dst_im = $createDstFun ( $dstFile );
    $src_im = $createSrcFun ( $srcFile );
    imagecopymerge ( $dst_im, $src_im, 0, 0, 0, 0, $src_w, $src_h, $pct );
//	header ( "content-type:" . $dstMime );
    $outDstFun ( $dst_im, $dstFile );
    imagedestroy ( $src_im );
    imagedestroy ( $dst_im );
}

