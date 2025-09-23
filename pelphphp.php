<?php

function doUpload()
{
    global $photoUrl, $photoPath, $thumbUrl, $thumbPath, $highresUrl, $highresPath;
    global $eol, $errorBeg, $errorEnd;
    $debug = false;
    $msg = "";

    try {
        if ($debug) $msg .= "enter upload $eol";
        error_reporting(E_ALL);
        ini_set("display_errors", true);
        rrwUtil::deltaTimer();

        ##  Start of code
        if (!array_key_exists("photographer", $_POST)) {
            $msg .= processuploadDire();
            return "$msg processuploadDire loop ";
        }
        // file came from the form and is in $_Files
        $year = rrwUtil::fetchparameterString("year");
        $photographer = rrwUtil::fetchparameterString("photographer");
        $trail = rrwUtil::fetchparameterString("trail");
        if (empty($photographer))
            $photographer = "not Specified";
        else {
            $ii = strpos($photographer, "|");
            $photographer = substr($photographer, $ii + 1);
        }
        if ($_FILES["file"]["error"] > 0) {
            throw new Exception(" $errorBeg Upload file got return Code: " . $_FILES["file"]["error"] . $errorEnd);
        }
        $inputfile = $_FILES["file"]["name"];
        $fileSplit = pathinfo($inputfile);
        $filename = $fileSplit["filename"];
        $size = $_FILES["file"]["size"] / 1024;
        $tempFile = $_FILES["file"]["tmp_name"];
        if ($debug) {
            $msg .= "Upload: $inputfile <br />";
            $msg .= "Type: " . $_FILES["file"]["type"] . "<br />";
            $msg .= "Size: " . round($size, 1) . " Kb<br />";
            $msg .= "Temp file: $tempFile <br />";
        }
        $fullUploadName = "$photoPath/upload/$inputfile";
        move_uploaded_file($tempFile, $fullUploadName);
        $msg .= MakeImakeImages($fullUploadName, $year, $photographer, $trail);
        $msg .= database($filename, $year, $trail, $photographer);
    } catch (Exception $ex) {
        $msg .= "$errorBeg  E#193 main upload $errorEnd";
    }
    return $msg;
}

function MakeImakeImages($fullfilename, $year, $photographer, $trail)
{
    // assum the $_Files has been move to temp location
    global $eol, $errorBeg, $errorEnd;
    global $photoUrl, $photoPath, $thumbUrl, $thumbPath, $highresUrl, $highresPath;
    $debug = false;
    $msg = "";


    $fileSplit = pathinfo($fullfilename);
    if ($debug) $msg .= rrwUtil::print_r($fileSplit, true, "the file split");

    $extension = $fileSplit['extension'];
    $basename = $fileSplit['basename'];
    $filename = $fileSplit['filename'];

    $FullfileHighRes = "$highresPath/$basename";
    $fullFfileThumb = "$thumbPath/$filename" . "_tmb.jpg";
    $fullFilePhoto = "$photoPath/$filename" . "_cr.jpg";

    if ($debug) {
        $msg .= "base name : " . $fileSplit['basename'] . $eol;
        $msg .= "extension : " . $fileSplit['extension'] . $eol;
        $msg .= "FullfileHighRes : $FullfileHighRes $eol";
        $msg .= "fullFfileThumb : $fullFfileThumb $eol";
        $msg .= "fullFilePhoto : $fullFilePhoto $eol";
    }

    if (file_exists($FullfileHighRes)) {
        $dateMod = filemtime($FullfileHighRes);
        $datemod = date("Y-M-d", $dateMod);
        $msg .= "$errorBeg $eol E#190 $FullfileHighRes with date $dateMod already exists. Will be replaced $errorEnd";
        unlink($FullfileHighRes);
    }
    $msg .= "rename( $fullfilename, $FullfileHighRes )";
    if (!rename($fullfilename, $FullfileHighRes)) {
        throw new Exception(" $errorBeg $msg E#189 while attempting
                move ($fullfilename, $FullfileHighRes) $errorEnd");
    }

    if ($debug) $msg .= "saved the source file in $FullfileHighRes $eol";
    ini_set("display_errors", true);

    //  ------------------------------------------------------ got the file now process it
    $w_dst = 200; #	force thumbnail width to this number
    $h_botWhite = 20; #	height of the white bar at the bottom for copyright notice
    $fontSize = 12; #	height of the copyright text
    $fontfile = "arial.ttf";
    $fontfile = "/home/pillowan/www-shaw-weil-pictures/wp-content/plugins/roys-picture-processing/mvboli.ttf";
    $debugImageWork = false;
    if (!file_exists($fontfile)) {
        $msg .= "bad font $fontfile ";
        throw new Exception("$msg $errorBeg E#192 Problems with the font file $errorEnd");
    }
    if (!file_exists($FullfileHighRes)) {
        $msg .= "errorBeg Full Resolution did not get moved $errorEnd $eol
                $FullfileHighRes $eol";
        return $msg;
    }
    list($w_src, $h_src, $type) = getimagesize($FullfileHighRes); // create new dimensions, keeping aspect ratio
    if ($h_src == 0 || $w_src == 0) {
        throw new Exception(" $errorBeg width is $w_src or Height is $h_src zero $errorEnd");
    }
    if ($debugImageWork) $msg .= "input inmage is $w_src X $h_src ... ";

    # load the image into core
    if ($debug) $msg .= "type is $type $eol";
    switch ($type) {
        case 1: //   gif -> jpg
            $img_src = imagecreatefromgif($FullfileHighRes);
            break;
        case 2: //   jpeg -> jpg
            $img_src = imagecreatefromjpeg($FullfileHighRes);
            break;
        case 3: //   png -> jpg
            $img_src = imagecreatefrompng($FullfileHighRes);
            break;
        default:
            throw new Exception(" $errorBeg File '$FullfileHighRes' is not of type GIF, JPG, or PNG $errorEnd");
    }
    //  ------------------------------------------------------ resize thumbnail
    $aspect = $w_src / $h_src; // maintain the aspect ration
    $desiredHeight = 150;
    $h_tmb = $desiredHeight;
    $w_tmb = round($h_tmb * $aspect, 0);
    if ($debug)  $msg .= "resizeImage( $img_src, $w_src, $h_src, $w_tmb, $h_tmb, 0 );";
    list($img_tmb, $w_tmb, $h_tmb, $msgTmp) =
        resizeImage($img_src, $w_src, $h_src, $w_tmb, $h_tmb, 0);
    $msg .= $msgTmp;
    if ($debugImageWork) $msg .= "image resized to $w_tmb X $h_tmb ... ";
    if (!imagejpeg($img_tmb, $fullFfileThumb)) //  save new image
        throw new Exception(" $errorBeg E#191 imagejpeg($img_tmb,
                                    $fullFfileThumb ) failed $errorEnd");
    if ($debugImageWork) $msg .= " thumbnail saved to $fullFfileThumb $eol";
    imagedestroy($img_tmb); // free memory
    // ------------------------------------------------------ resize copyright
    $maxHeight = 768;
    if ($h_src > $maxHeight) {
        $h_cr = $maxHeight;
        $w_cr = round($h_cr / $aspect, 0);
        list($img_copyright, $w_cr, $h_cr, $msgTmp) =
            resizeImage($img_src, $w_src, $h_src, $w_cr, $h_cr, $h_botWhite);
        $msg .= $msgTmp;
        if ($debugImageWork) $msg .= "image resized to $w_cr X $h_cr - l";
    } else {
        $img_copyright = $img_src;
        $h_cr = $h_src;
        $w_cr = $w_src;
    }
    //  ---------------------------------------------------- place text
    $green = imagecolorallocate($img_copyright, 0, 255, 0);
    $white = imagecolorallocate($img_copyright, 255, 255, 255);
    if ($white === false)
        throw new Exception(" $errorBeg magecolorallocate($img_copyright, 255,255,0)");
    $black = imagecolorallocate($img_copyright, 0, 0, 0); # returns zero
    if ($black === false)
        throw new Exception(" $errorBeg magecolorallocate($img_copyright, 0,0,0)");
    $h_new = $h_cr + $h_botWhite;
    $imgFinal = imagecreatetruecolor($w_cr, $h_new);
    if (!imagecopy($imgFinal, $img_src, 0, 0, 0, 0, $w_src, $h_cr))
        throw new Exception("$msg E#211 imaagecopy failed ");
    if ($debugImageWork) $msg .= "rectangle( ..., 0,  $h_cr+1, $w_src, $h_new, $white )";
    if (!imagefilledrectangle($imgFinal, 0, $h_cr, $w_src, $h_new, $white))
        throw new Exception(" $errorBeg $msg E#120
                    imagerectangle(... 0, $h_cr,$w_src, $h_new, $white ) ");
    if ($debugImageWork) $msg .= "appended white rectangle ...";
    $left = 2;
    $top = $h_cr + 2;
    $bot = $h_new - 3;
    $copyrightMsg = "Photo by $photographer  ©"; //pictures.shaw-weil.com";
    // use true type fonts. First deterine the actual length
    $bounds = imagettftext(
        $imgFinal,
        $fontSize,
        0,
        $left,
        $bot,
        $black,
        $fontfile,
        $copyrightMsg
    );
    if ($bounds === false)
        throw new Exception(" $errorBeg E#169 imagettftext ($img_copyright,
                    $fontSize, 0, $left, $bot,
										$black,$fontfile,$copyrightMsg)");
    $w_text = abs($bounds[0] - $bounds[2]);
    $h_text = abs($bounds[1] - $bounds[5]);
    if ($debugImageWork) $msg .= "Got the text size of $w_text, $h_text ... ";
    $left = ($w_cr - $w_text) / 2; # center the text left to right
    if ($left < 2) $left = 2;
    $bot = $h_new - 3; # just barely off the bottom.
    # black out the text just drawn, and redraw it centered on the visiable image
    $tmpHeight = $h_cr - $h_botWhite;
    if (!imagefilledrectangle($imgFinal, 0, $h_cr, $w_src, $h_new, $white))
        debugImageWorkimagerectangle("$msg $errorBeg imagefilledrectangle( imagefilledrectangle( ...,  $imgFinal, 0, $h_cr, $w_src $h_new, $white )))$errorEnd ");
    if ($debugImageWork) $msg .= "filled rectangle ...
            imagettftext ($imgFinal, $fontSize, 0, $left,
										$bot, $black, $fontfile, $copyrightMsg)";

    $bounds = imagettftext($imgFinal, $fontSize, 0, $left, $bot, $black, $fontfile, $copyrightMsg);
    if ($bounds === false)
        debugImageWorkimagerectangle("$msg $errorBeg magettftext ($img_copyright, $fontSize, 0, $left,
										$bot, $black, $fontfile,$copyrightMsg)");
    if ($debugImageWork) $msg .= "Placed at $left, $bot, placed text $copyrightMsg ... ";

    if ($debugImageWork) $msg .= "about to write file to $fullFilePhoto ...";
    if (!imagejpeg($imgFinal, $fullFilePhoto))
        debugImageWorkimagerectangle("$msg $errorBeg
                    ...,$fullFilePhoto) $errorEnd");
    if ($debugImageWork) $msg .= " wrote file ... ";
    if (!imagedestroy($imgFinal))
        debugImageWorkimagerectangle("$msg $errorBeg imagedestroy( $img_copyright ) $errorEnd");
    if ($debugImageWork) $msg .= "released the memory $eol";

    # write the ext onto the high resolution image also

    if (false) {
        #resize to the high esolution, same size with a bottom border
        $h_high = $h_src; #+ $h_botWhite;
        if (!enoughmem($h_high, $w_src)) {
            $msg .= ("not enough memeory$eol");
            return $msg;
        }
        if ($debug) $msg .= ("imagecreatetruecolor($w_src, $h_high)$eol"); //  place holder with border.
        $img_high = imagecreatetruecolor($w_src, $h_high);
        #	or die ("imagecreatetruecolor($w_src,$h_high") ;  //  place holder with border.
        if ($debug) $msg .= "high res space created$eol";
        if ($debug) $msg .= "imagecopy($img_high, $img_src, 0, 0, 0, 0, $w_src, $h_src)$eol";
        imagecopy($img_high, $img_src, 0, 0, 0, 0, $w_src, $h_src)
            or die("imagecopy($img_high, $img_src, 0, 0, 0, 0, $w_src, $h_src)$eol");
        if ($debug) $msg .= "copied $eol";
    }
    imagedestroy($img_src);

    $msg .= "$eol <a href='https://pictures.shaw-weil.com/display-one-photo?photoname=$filename'  target='one'>$filename</a>$eol";

    return $msg;
} // end function doupload
function debugImageWorkimagerectangle($msg)
{
    global $eol, $errorBeg, $errorEnd;
    global $eol;
    print $errorBeg . $msg . $errorEnd;
    throw new exception($msg);
}
function database($filename, $copyDate, $trail, $photogepher)
{
    global $eol, $errorBeg, $errorEnd;
    global $wpdbExtra, $rrw_photos;
    $msg = "";
    $debug = false;
    //   return "Skip update database ****************************************";
    $sql = "select photographer, copyright from $rrw_photos
                    where filename = '$filename'";
    $recset_query = $wpdbExtra->get_resultsA($sql);
    if (1 != $wpdbExtra->num_rows) {
        $msg .= "E#194 photo recordnot found $eol $sql $eol ";
        $photographer = "mary M Shaw";
        $copyright = "";
        $sqlinsert = "insert into $rrw_photos (filename)
                values ('$filename')";
        $recset_insert = $wpdbExtra->query($sqlinsert);
    } else {
        $recset = $recset_query[0];
        $photographer = $recset["photographer"];
        $copyright = $recset["copyright"];
    }
    $sqlUpdate = "update $rrw_photos set photographer = '$photographer' ";
    if (!empty($trail))
        $sqlUpdate .= ", trail_name = '$trail' ";
    if (!empty($copyright))
        $sqlUpdate .= ", copyright = '$copyDate' ";
    $sqlUpdate .= ", uploaddate = now() ";
    $sqlUpdate .= " where filename = '$filename' ";
    $recset_query = $wpdbExtra->query($sqlUpdate);
    $msg .= "$sqlUpdate $eol";
    return $msg;
}

function resizeImage(
    $img_in,
    $curWidth,
    $curHeight,
    $maxWidth,
    $maxHeight,
    $botBorder
): array {
    global $eol;
    $h_new = 0;
    $w_dst = 0;
    $img_out = 0;
    $msg = "";
    $ratio = 1;

    $debugResize = false;
    if ($curHeight > $maxHeight || $curWidth > $maxWidth) {
        $ratio = $curHeight / $maxHeight;
        $ratioW = $curWidth / $maxWidth;
        if ($ratioW < $ratio)
            $ratio = $ratioW;
        $h_new = floor($curHeight / $ratio);
        $w_dst = floor($curWidth / $ratio);
        $h_new = $h_new + $botBorder;
        $img_out = imagecreatetruecolor($w_dst, $h_new)
            or die("imagecreatetruecolor($w_dst, $h_new)"); //  place holder
        if ($debugResize) $msg .= "imagecopyresampled($img_out, $img_in, 0, 0, 0, 0, $w_dst, $h_new, $curWidth, $curHeight) $eol";
        imagecopyresampled($img_out, $img_in, 0, 0, 0, 0, $w_dst, $h_new, $curWidth, $curHeight)
            or die("imagecopyresampled($img_out, $img_in, 0, 0, 0, 0, $w_dst, $h_new, $curWidth, $curHeight");
    } else {
        $ratio = 1;
        $h_new1 = $curHeight;
        $h_new = $h_new1 + $botBorder;
        $img_out = imagecreatetruecolor($curWidth, $h_new)
            or die("imagecreatetruecolor( $curWidth, $curHeight)"); //  place holder
        imagecopy($img_out, $img_in, 0, 0, 0, 0, $curWidth, $curHeight)
            or die("imagecopy($img_out, $img_in, 0,0,0,0, $curWidth, $curHeight)");
        $w_dst = $curWidth;
        $h_new = $curHeight;
    }
    if ($debugResize) $msg .= "Ratio is $ratio $eol";
    if ($debugResize) $msg .= "Height calculated $h_new, border of $botBorder$eol";
    if ($debugResize) $msg .= "Height adjusted $h_new was $curHeight,  $eol";
    if ($debugResize) $msg .= "Width is $w_dst was $curWidth $eol";
    $result = array($img_out, $w_dst, $h_new, $msg);
    return $result;
}
//-------------------------------------------------- ENOUGH MEMORY ?
function enoughmem($x, $y)
{
    $MAXMEMy = 32 * 1024 * 1024;
    return ($x * $y * 3 * 1.7 < $MAXMEMy - memory_get_usage());
}

function processuploadDire()
{
    global $eol, $errorBeg, $errorEnd;
    global $uploadPath;
    global $wpdbExtra, $rrw_photos;
    $msg = "";
    $debug = false;

    $msg .= rrwUtil::deltaTimer("");
    if ($debug) $msg .= " go to processuploadDire () $eol";
    $handle = opendir($uploadPath);
    if (!is_resource($handle))
        throw new Exception("$errorBeg E#159 failed to
                                    open $uploadPath $errorEnd");
    if ($debug) $msg .= "Entries:$eol";

    $cnt = 0;
    $cntUploaded = 0;
    while (false !== ($entry = readdir($handle))) {
        $cnt++;
        if ($cnt > 500)
            break;
        if ($debug) $msg .= "$entry, ";
        $fullfilename = "$uploadPath/$entry";
        if (is_dir($fullfilename))
            continue;
        $filename = basename($fullfilename, ".jpg");
        $msg .= "$fullfilename  -- $filename -- ";
        $sql = "select photographer, copyright from $rrw_photos
                    where filename = '$filename'";
        if ($debug) $msg .= "$sql $eol";
        $recset_query = $wpdbExtra->get_resultsA($sql);
        $msg .= "num rows " . $wpdbExtra->num_rows;
        if (1 == $wpdbExtra->num_rows) {
            if ($debug) $msg .= "one record";
            $photographer = $recset_query[0]["photographer"];
        } else {
            $photographer = "Mary Shaw";
            $sqlinsert = "insert into $rrw_photos (filename, photographer)
                values ('$filename','$photographer')";
            if ($debug) $msg .= "$sqlinsert $eol";
            $recset_insert = $wpdbExtra->query($sqlinsert);
        }
        if ($debug) $msg .= "$photographer $eol";
        //         $msg .= MakeImakeImages( $fullfilename, "", "Sandy Mateer", "" ) ;
        if ($debug) $msg .= "trying image $entry\n";
        $msg .= MakeImakeImages($fullfilename, "", $photographer, "");
        $msg .= database($filename, "", "", $photographer);
        $cntUploaded++;
    } // end while
    $msg .= "\n\nuploaded $cntUploaded files in " .
        rrwUtil::deltaTimer("uploading files") . "\n\n";
    return $msg;
} // end function processuploadDire
