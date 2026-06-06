<?php
/*		Freewheeling Easy Mapping Application
 *		A collection of routines for display of trail maps and amenities
 *		copyright Roy R Weil 2019 - https://royweil.com
 *
 */
class uploadProcessDire
{
    /*
     *   nameToBottom( $sourceFile, $photographer )
     *   resizeImage( $fullFileNameIn, $fullFileNameNameOut, $w_max, $h_max ) {
     */
    public static function upload($attr)
    {
        global $eol, $errorBeg, $errorEnd;
        global $uploadPath;
        // looks for files in the upload directory
        //      creates database record if not already there
        //      creates the _cr version with bottom line credit
        //      creates the thumbnail version
        //      extracts the exif data to the database
        //      moves to the high_resolution directory
        //
        $msg = "";
        $debug = rrwUtil::setDebug("upload");
        try {
            if ($debug) $msg .= "uploadProcessDire ($uploadPath) $eol";
            $uploadShortName = rrwParam::String("uploadShortName", $attr);
            if ($debug) $msg .= "found $uploadShortName in the calling parameters $eol";
            if (!empty($uploadShortName)) {
                $msg .= self::ProcessOneFile($uploadShortName);
                $photoName = strToLower($uploadShortName);
                $cntUploaded = 1;
            } else {
                // not a single file request. walk the directory
                $handle = opendir($uploadPath);
                if (!is_resource($handle))
                    throw new Exception("$errorBeg E#207 failed to
                                    open $uploadPath $errorEnd");
                if ($debug) $msg .= "Entries:$eol";
                $cnt = 0;
                $cntUploaded = 0;
                while (false !== ($uploadShortName = readdir($handle))) {
                    $cnt++;
                    if ($cnt > 600)
                        break;
                    if (is_dir("$uploadPath/$uploadShortName"))
                        continue; // ignore directories
                    if ($debug) $msg .= "found $uploadShortName in the directory search $eol";
                    $msg .= self::ProcessOneFile($uploadShortName);
                    $photoName = strToLower($uploadShortName);
                    $cntUploaded++;
                    break;
                } // end while
            } // end  if (! empty($uploadShortName))
            if (1 == $cntUploaded) {
                $photoName = substr($photoName, 0, -4);
                if ($debug) $msg .= "DisplayOne( array( \"photoName\" => $photoName ) ) $eol";
                $msg .= freeWheeling_DisplayOne::DisplayOne(array("photoName" => $photoName));
            } else {
                $msg .= "$eol uploaded $cntUploaded files $eol";
            }
        } // end try
        catch (Exception $ex) {
            $msg .= $errorBeg . $ex->getMessage() . $errorEnd;
        }
        return $msg;
    } // end function uploadProcessDire::upload
    private static function processOneFile($entry)
    {
        global $eol, $errorBeg, $errorEnd;
        global $uploadPath;
        global $wpdbExtra, $rrw_photos;
        $msg = "";
        $debug = rrwUtil::setDebug("onefile");
        if ($debug) $msg .= "$entry, ";
        $sourceFile = "$uploadPath/$entry"; // in upload dire
        // ------new ----------------------------  validate photoname
        if (!file_exists($sourceFile))
            throw new Exception("$msg $errorBeg E#166 processOneFile( $entry )
                     file not found in upload $errorEnd $sourceFile $eol");
        $mime_type = mime_content_type($sourceFile);
        switch ($mime_type) {
            case 'image/jpeg':
                //    case 'image/png':
                //    case 'image/gif':
                break; // is good
            default:
                throw new RuntimeException("file '$sourceFile'
                        minetype is $mime_type,
                        it should .jpg, " /*.png or .gif"*/);
        }
        $fileExif = exif_read_data($sourceFile); // used to get time
        $photoName = substr($entry, 0, strlen($entry) - 4);
        $photoName = strtolower($photoName);
        if ($debug) $msg .= "photo name just after create $photoName $eol";
        $pregResults = preg_match(
            "/[-a-zA-z0-9 _]*/",
            $photoName,
            $matches
        );
        if (1 != count($matches))
            throw new RuntimeException("file name can consist of only
                letters, numbers, and spaces");
        // only logged n users can get here
        $current_user = wp_get_current_user();
        $userLogin = $current_user->get("user_login");
        // --------------------------- deal with database entry
        $Data = array(
            "highresShortname" => $entry,
            "uploaddate" => date("Y-m-d H:i"),
            /* all others default to blank */
        );
        $remotefile = rrwParam::String("remotefile");
        if (!empty($remotefile))
            $Data["Direonp"] = $remotefile;
        $sqlRec = "select * from $rrw_photos
                        where photoname = '$photoName'";
        $recs = $wpdbExtra->get_resultsA($sqlRec);
        if (1 == $wpdbExtra->num_rows) {
            // have meta data, update it
            if ($recs[0]["owner"] != $userLogin) {
                $updator = $recs[0]["owner"];
                $iiComma = strrpos($updator, ",");
                if (
                    false === $iiComma ||
                    substr($updator, $iiComma + 1) != $userLogin
                )
                    $Data["owner"] = "$updator,$userLogin";
                if ($debug) $msg .= rrwUtil::print_r(
                    $Data,
                    true,
                    "updating $photoName $eol"
                );
            }
            $key = array("photoname" => $photoName);
            $cnt = $wpdbExtra->update($rrw_photos, $Data, $key);
            if (1 != $cnt) {
                $err = "$errorBeg E#167 update no change $errorEnd";
                $msg .= rrwUtil::print_r($Data, true, $err);
            }
        } elseif (0 == $wpdbExtra->num_rows) {
            // no meta data
            $Data["photoname"] = $photoName;
            $Data["photographer"] = "Mary Shaw";
            $Data["owner"] = $userLogin;
            if ($debug) $msg .= rrwUtil::print_r($Data, true,  "inserting $photoName $eol");
            $cnt = $wpdbExtra->insert($rrw_photos, $Data);
            if (1 != $cnt) {
                $err = "$errorBeg E#168 insert fails $errorEnd";
                $msg .= rrwUtil::print_r($Data, true, $err);
            }
        } else {
            $msg .= "$errorBeg E#161 found " . $wpdbExtra->num_rows . " of data
                    for $errorEnd $sqlRec $eol";
            throw new Exception($msg);
        }
        $sqlRec = "select * from $rrw_photos
                        where photoname = '$photoName'";
        $recs = $wpdbExtra->get_resultsA($sqlRec);
        $recOld = $recs[0];
        $photographer = $recOld["photographer"];
        $msg .= freewheeling_fixit::sourceReject($photoName, "use");
        $msg .= self::makeImages($sourceFile, $photographer);
        // meta date exists make it consistant with the EXIF
        $msg .= freewheeling_fixit::fixAssumeDatabaseCorrect($recOld);
        if ($debug) $msg .= "getting date $eol";
        $photoDate = freewheeling_fixit::getPhotoDateTime($fileExif);
        if (!empty($photoDate)) {
            if ($debug) $msg .= "photoDate now $photoDate";
            $sqlTimeUpdate = "update $rrw_photos set photoDate = '$photoDate'
                                where photoName = '$photoName'";
            $wpdbExtra->query($sqlTimeUpdate);
        }
        return $msg;
    } // end function processOneFile
    private static function makeImages($sourceFile, $photographer)
    {
        // assume the file is a temp location - gone when done
        global $eol, $errorBeg, $errorEnd;
        global $photoUrl, $photoPath, $thumbUrl, $thumbPath, $highresUrl, $highresPath;
        //      creates the _cr version with bottom line credit of photographer
        //      creates the thumbnail version
        //      moves to the high_resolution directory
        $msg = "";
        $debug = rrwParam::Boolean("makeimage");
        $debugImageWork = rrwParam::Boolean("imagework");
        try {
            if ($debug) $msg = "makeImages( $sourceFile, $photographer ) $eol";
            $widthThumb = 200; #	force thumbnail width to this number
            $heightMax = 768; // limit display mage to yhis number
            $widthMax = 1024;
            $fileSplit = pathinfo($sourceFile);
            if ($debug) $msg .= rrwUtil::print_r($fileSplit, true, "the file split");
            $extension = $fileSplit['extension'];
            $basename = $fileSplit['basename'];
            $photoName = $fileSplit['filename'];
            $photoName = strToLower($photoName);
            $fullFileNameHighRes = "$highresPath/$basename";
            $fullFileNameThumb = "$thumbPath/$photoName" . "_tmb.jpg";
            $fullFileNamePhoto = "$photoPath/$photoName" . "_cr.jpg";
            if ($debug) {
                $msg .= "base name : " . $fileSplit['basename'] . $eol;
                $msg .= "extension : " . $fileSplit['extension'] . $eol;
                $msg .= "fullFileNameHighRes : $fullFileNameHighRes $eol";
                $msg .= "fullFileNameThumb : $fullFileNameThumb $eol";
                $msg .= "fullFileNamePhoto : $fullFileNamePhoto $eol";
            }
            //  -------------------------- setup done, now process
            $msg .= self::resizeImage( /* do thumbnail */
                $sourceFile,
                $fullFileNameThumb,
                $widthThumb,
                0
            );
            // source exits or resize would have thrown error
            $msg .= self::resizeImage( /* do dispay image */
                $sourceFile,
                $fullFileNamePhoto,
                $widthMax,
                $heightMax
            );
            if (!empty($photographer))
                $msg .= self::nameToBottom($fullFileNamePhoto, $photographer);
            // ----------------- move input image to save location
            if (!rename($sourceFile, $fullFileNameHighRes)) {
                throw new Exception(" $errorBeg $msg E#126 while attempting
                move ($sourceFile, $fullFileNameHighRes) $errorEnd");
            }
            if (!file_exists($fullFileNameHighRes)) {
                $msg .= "errorBeg Full Resolution did not get moved $errorEnd $eol
                $fullFileNameHighRes $eol";
                return $msg;
            }
            if ($debug) $msg .= "saved the source file
                                    in $fullFileNameHighRes $eol";
            return $msg;
        } // end try
        catch (Exception $ex) {
            $msg .= $ex->getMessage() . "$errorBeg  E#145 main upload $errorEnd";
        }
        return $msg;
    } // end makeFile
    //
    public static function nameToBottom(string $sourceFile, string $photographer)
    {
        global $eol, $errorBeg, $errorEnd;
        $msg = "";
        $debug = rrwParam::Boolean("nameToBottom");
        if ($debug) $msg .= "nameToBottom( $sourceFile, $photographer ) $eol";
        // $fontFile = "arial.ttf";
        $fontDire = "/home/pillowan/www-shaw-weil-pictures/wp-content/plugins/roys-picture-processing";
        $fontFile = "$fontDire/mvboli.ttf";
        if (!file_exists($fontFile))
            throw new Exception("$errorBeg E#127 nameToBottom:font file '$fontFile'  Not found $errorEnd");
        if (!file_exists($sourceFile)) {
            throw new Exception("$errorBeg E#132 nameToBottom:file: '$sourceFile' Not found $errorEnd");
        }

        $im_src = new Imagick();
        $draw = new ImagickDraw();
        // set some of the draw parameters for the text to be added
        $draw->setFillColor("Black");
        $draw->setStrokeWidth(0);
        $draw->setTextAntialias(false);
        $draw->setTextInterlineSpacing(0);
        $draw->setFont($fontFile);
        $draw->setGravity(Imagick::GRAVITY_SOUTHWEST);

        $im_src->readimage($sourceFile);
        $imageWidth = $im_src->getImageWidth();
        $desiredTextWidth = $imageWidth / 3; // want text to be 1/3 of the image width\

        /*
        $draw->setTextEncoding("UTF-8");
        $draw->setTextInterlineSpacing(0);
        */
        for ($fontSize = 10; $fontSize < 100; $fontSize++) {
            $draw->setFontSize($fontSize);
            $metrics = $im_src->queryFontMetrics($draw, "Photo by $photographer");
            // $msg .= "font size $fontSize text width " . $metrics["textWidth"] . " desired text width $desiredTextWidth $eol";
            if ($metrics["textWidth"] > $desiredTextWidth) {
                break;
            }
        }
        $h_bottom = (int)($fontSize * 1.6); #	height of the white bar at the bottom for copyright notice

        // use Imagick to add the name to the bottom
        if ($debug) $msg .= "adding photographer $eol";
        $text = "Photo by $photographer";
        $h_new = $im_src->getImageHeight();
        $w_new = $im_src->getImageWidth();
        $h_new = $h_new + $h_bottom;
        $im_src->extentImage($w_new, $h_new, 0, 0);     // add white bar at bottom


        $draw->setFontSize($fontSize);

        $baseline = (int)(($h_bottom - $fontSize) / 5);         // h_bottom - fontsize is the extra space, want to split it in half for top and bottom margin
        $msg .= "font size $fontSize, bottom size = $h_bottom, baseline = $baseline $eol";
        $marginLeft = (int)(($w_new - $metrics["textWidth"]) / 2);
        if ($debug);
        $msg .= "placing annotation '$text' at $marginLeft, $baseline with font size " . $draw->getFontSize() . " $eol";
        $draw->annotation($marginLeft, $baseline, $text);
        $im_src->drawImage($draw);
        $draw->destroy();
        if ($debug) $msg .= "deleting source $sourceFile $eol";
        $result = unlink($sourceFile);
        if ($debug) $msg .= "writing source $sourceFile $eol";
        $im_src->writeImage($sourceFile);
        return $msg;
    } // end NameToBottom

    public static function resizeImage(string $fullFileNameIn, string $fullFileNameNameOut, int $w_max, int $h_max)
    {
        global $eol;
        global $eol, $errorBeg, $errorEnd;
        $msg = "";
        $debug = rrwParam::Boolean("resize");
        $debug = false;
        if (!file_exists($fullFileNameIn)) {
            throw new Exception("$errorBeg E#162 resizeToWidth:file: '$fullFileNameIn' Not found $errorEnd");
        } else {
            $msg .= "resizeImage( $fullFileNameIn, $fullFileNameNameOut, $w_max, $h_max ) $eol";
        }
        if (file_exists($fullFileNameNameOut)) {
            $resultLink = unlink($fullFileNameNameOut); // remove resultant file
            if (false === $resultLink)
                throw new Exception("$errorBeg E#164 resizeToWidth:file:
                    '$fullFileNameNameOut' did not unlink $errorEnd");
        }
        $imgGDin = self::imageCreateFrom($fullFileNameIn);
        $w_cur = imagesx($imgGDin);
        $h_cur = imagesy($imgGDin);
        if ($debug) $msg .= "input size =  $w_max, $h_max $eol
                            current size =  $w_cur. $h_cur $eol";
        $w_scaleFactor = $h_scaleFactor = $scaleFactor = "not set";
        if (0 >= $w_max && 0 >= $h_max) {
            throw new Exception("$msg $errorBeg E#163 resizeImage:
            Invalid widths specified $w_max, $h_max  $errorEnd");
        } elseif (0 >= $h_max) {; // donothing w,0
        } elseif (0 >= $w_max) {
            $scaleFactor = $h_max / $h_cur;
            $w_max = round($w_max * $scaleFactor);
            $h_max = 0;
        } else {
            $w_scaleFactor = $w_max / $w_cur;
            $h_scaleFactor = $h_max / $h_cur;
            $scaleFactor = min($w_scaleFactor, $h_scaleFactor);
            $w_max = floor($w_cur * $scaleFactor);
            $h_max = 0;
        }
        if ($debug)
            $msg .= "scales $w_scaleFactor, $h_scaleFactor used $scaleFactor $eol resize scaled $w_cur, $h_cur to
                                $w_max, $h_max $eol";
        $imgGDout = imagescale($imgGDin, $w_max);
        if (false === $imgGDout)
            throw new Exception("$msg $errorBeg #155 failure in resize
                                    using $w_max, $h_max $errorEnd ");
        $resultOut = imagejpeg($imgGDout, $fullFileNameNameOut, 100);
        if (false === $resultOut)
            throw new Exception("$msg $errorBeg E#196 failure in resize:write
                        failed to $fullFileNameNameOut $errorEnd");
        if ($debug) $msg .= ", file succefully created $eol";
        return $msg;
    } // end resize

    private static function imageCreateFrom(string $sourceFile)
    {
        global $eol, $errorBeg, $errorEnd;
        if (!file_exists($sourceFile)) {
            throw new Exception("$$errorBeg E#137 imageCreateFrom:file:
                    '$sourceFile' Not found $errorEnd");
        }
        $mime_type = mime_content_type($sourceFile);
        switch ($mime_type) {
            case "image/gif": //   gif -> jpg
                $img_src = imagecreatefromgif($sourceFile);
                break;
            case "image/jpg": //   jpeg -> jpg
            case "image/jpeg": //   jpeg -> jpg
                $img_src = imagecreatefromjpeg($sourceFile);
                break;
            case "image/png": //   png -> jpg
                $img_src = imagecreatefrompng($sourceFile);
                break;
            default:
                throw new Exception(" $errorBeg E#133 File '$sourceFile' is
                    mime_type, '$mime_type' -  only GIF, JPG, jpeg or PNG are allowed
                    $errorEnd");
        }
        return $img_src;
    } // end imageCreateFrom
    /*
    //-------------------------------------------------- ENOUGH MEMORY ?
    private static function enoughMemory($x, $y)
    {
        $maxMemory = 32 * 1024 * 1024;
        return ($x * $y * 3 * 1.7 < $maxMemory - memory_get_usage());
    } // end enoughMemory
     */
} // end class uploadProcessDire
