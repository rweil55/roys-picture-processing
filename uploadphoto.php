<?php

function processuploadDire() {
    global $eol, $errorBeg, $errorEnd;
    global $uploadPath;
    global $wpdbExtra, $rrw_photos;
    // looks for files in the upload directory 
    //      creates database record if not already there
    //      creates the _cr version with bottom line credit
    //      creates the thumbnail version
    //      extracts the exif data to the database
    //      moves to the high_resoltion directory
    //
    $msg = "";
    $debug = true;

    $cntUploaded = 0;
    try {
        if ( $debug )$msg .= "processuploadDire ($uploadPath) $eol";
        include "setConstants.php";
        $handle = opendir( $uploadPath );
        if ( !is_resource( $handle ) )
            throw new Exception( "$errorBeg E#496 failed to 
                                    open $uploadPath $errorEnd" );
        if ( $debug )$msg .= "Entries:$eol";
        $cnt = 0;
        while ( false !== ( $entry = readdir( $handle ) ) ) {
            $cnt++;
            if ( $cnt > 600 )
                break;
            if ( $debug )$msg .= "$entry, ";
            $fullfilename = "$uploadPath/$entry"; // in uplosd dire
            if ( is_dir( $fullfilename ) )
                continue; // ignore directories
            // ------new ----------------------------  validate filename

            $mime_type = mime_content_type( $fullfilename );
            switch ( $mime_type ) {
                case 'image/jpeg':
                case 'image/png':
                case 'image/gif':
                    break; // is good
                default:
                    throw new RuntimeException( "file '$fullfilename' 
                        minetype needs to be .jpg, .png or .gif" );
            }
            $DataFilename = substr( $entry, strlen( $entry ) - 4 );
            $pregResults = preg_match( "/[-a-zA-z0-9 _]*/",
                $DataFilename, $matchs );
            if ( 1 != count( $matchs ) )
                throw new RuntimeException( "file name can consist of only
                letters, numbers, and spaces" );
            // --------------------------- deal with database entry
            $sqlRec = "select * from $rrw_photos 
                        where filename = '$DataFilename'";
            $recs = $wpdbExtra->get_resultsA( $sqlRec );
            if ( 1 == $wpdbExtra->num_rows ) {
                $photographer = $recs[ 0 ][ "photographer" ];
            } else {
                $wpdbExtra->insert( $rrw_photos,
                    array( "filename" => $DataFilename ) );
                $photographer = ""; // record not there 
            }
            // ------------------------- create image and updata database
            $msg .= MakeImakeImages( $fullfilename, $photographer );
            freewheeling_fixit::Do_forceDatabse2matchexif( $sqlRec );
            $cntUploaded++;
        } // end while
    } // end try
    catch ( Exception $ex ) {
        $msg .= $ex->getMessage() . "$errorBeg  E#430 main upload $errorEnd";
    }
    $msg .= "\n\nuploaded $cntUploaded files in " .
    rrwUtil::deltaTimer( "uploading files" ) . "\n\n";
    return $msg;
} // end function processuploadDire

function MakeImakeImages( $fullfilename, $photographer ) {
    // assume the file is a temp location - gone when done
    global $eol, $errorBeg, $errorEnd;
    global $photoUrl, $photoPath, $thumbUrl, $thumbPath, $highresUrl, $highresPath;
    //      creates the _cr version with bottom line credit of photographer
    //      creates the thumbnail version
    //      extracts the exif data to the database
    //      moves to the high_resoltion directory

    $debug = true;
    $msg = "";

    $fileSplit = pathinfo( $fullfilename );
    if ( $debug )$msg .= rrwUtil::print_r( $fileSplit, true, "the file split" );
    $extension = $fileSplit[ 'extension' ];
    $basename = $fileSplit[ 'basename' ];
    $filename = $fileSplit[ 'filename' ];
    $FullfileHighRes = "$highresPath/$basename";
    $fullFfileThumb = "$thumbPath/$filename" . "_tmb.jpg";
    $fullFilePhoto = "$photoPath/$filename" . "_cr.jpg";
    if ( $debug ) {
        $msg .= "base name : " . $fileSplit[ 'basename' ] . $eol;
        $msg .= "extension : " . $fileSplit[ 'extension' ] . $eol;
        $msg .= "FullfileHighRes : $FullfileHighRes $eol";
        $msg .= "fullFfileThumb : $fullFfileThumb $eol";
        $msg .= "fullFilePhoto : $fullFilePhoto $eol";
    }
    if ( file_exists( $FullfileHighRes ) ) {
        $dateMod = filemtime( $FullfileHighRes );
        $datemod = date( "Y-M-d", $dateMod );
        $msg .= "$errorBeg $eol E#814 $FullfileHighRes with date $dateMod already exists. Will be replaced $errorEnd";
        unlink( $FullfileHighRes );
    }
    $msg .= "rename( $fullfilename, $FullfileHighRes )";
    if ( !rename( $fullfilename, $FullfileHighRes ) ) {
        throw new Exception( " $errorBeg $msg E#813 while attempting 
                move ($fullfilename, $FullfileHighRes) $errorEnd" );
    }
    if ( $debug )$msg .= "saved the source file in $FullfileHighRes $eol";
    ini_set( "display_errors", true );

    //  ------------------------------------------ got the file now process it
    $w_dst = 200; #	force thumbnail width to this number
    $h_botWhite = 20; #	height of the white bar at the bottom for copyright notice
    $fontSize = 12; #	height of the copyright text
    $fontfile = "arial.ttf";
    $fontfile = "/home/pillowan/www-shaw-weil-pictures/wp-content/plugins/roys-picture-processng/mvboli.ttf";
    $debugImageWork = true;
    if ( !file_exists( $fontfile ) ) {
        $msg .= "bad font $fontfile ";
        throw new Exception( "$msg $errorBeg E#812 Problems with the font file $errorEnd" );
    }
    if ( !file_exists( $FullfileHighRes ) ) {
        $msg .= "errorBeg Full Resolution did not get moved $errorEnd $eol 
                $FullfileHighRes $eol";
        return $msg;
    }
    // create new dimensions, keeping aspect ratio
    list( $w_src, $h_src, $type ) = getimagesize( $FullfileHighRes );
    if ( $h_src == 0 || $w_src == 0 ) {
        throw new Exception( " $errorBeg width is $w_src or Height is $h_src zero $errorEnd" );
    }
    if ( $debugImageWork )$msg .= "input inmage is $w_src X $h_src ... ";
    # load the image into core
    if ( $debug )$msg .= "type is $type $eol";
    switch ( $type ) {
        case 1: //   gif -> jpg
            $img_src = imagecreatefromgif( $FullfileHighRes );
            break;
        case 2: //   jpeg -> jpg
            $img_src = imagecreatefromjpeg( $FullfileHighRes );
            break;
        case 3: //   png -> jpg
            $img_src = imagecreatefrompng( $FullfileHighRes );
            break;
        default:
            throw new Exception( " $errorBeg File '$FullfileHighRes' is not of type GIF, JPG, or PNG $errorEnd" );
    }
    //  ------------------------------------------------------ resize thumbnail
    $aspect = $w_src / $h_src; // maintain the aspect ration
    $desiredHeight = 150;
    $h_tmb = $desiredHeight;
    $w_tmb = round( $h_tmb * $aspect, 0 );
    if ( $debug )$msg .= "resizeImage( $img_src, $w_src, $h_src, $w_tmb, $h_tmb, 0 );";
    list( $img_tmb, $w_tmb, $h_tmb, $msgTmp ) =
        resizeImage( $img_src, $w_src, $h_src, $w_tmb, $h_tmb, 0 );
    $msg .= $msgTmp;
    if ( $debugImageWork )$msg .= "image resized to $w_tmb X $h_tmb ... ";
    if ( !imagejpeg( $img_tmb, $fullFfileThumb ) ) //  save new image
        throw new Exception( " $errorBeg E#816 imagejpeg($img_tmb, 
                                    $fullFfileThumb ) failed $errorEnd" );
    if ( $debugImageWork )$msg .= " thumbnail saved to $fullFfileThumb $eol";
    imagedestroy( $img_tmb ); // free memory
    // ------------------------------------------------------ resize copyright
    $maxHeight = 768;
    if ( $h_src > $maxHeight ) {
        $w_cr = round( $maxHeight * $aspect, 0 );
        $h_cr = $maxHeight;
        list( $img_copyright, $w_cr, $h_cr, $msgTmp ) =
            resizeImage( $img_src, $w_src, $h_src, $w_cr, $h_cr, 0 );
        $msg .= $msgTmp;
        if ( $debugImageWork )$msg .= "image resized to $w_cr X $h_cr - l";
    } else {
        $img_copyright = $img_src;
        $h_cr = $h_src;
        $w_cr = $w_src;
    }
    if ( empty( $photographer ) ) {
        // skip adding the bottom line, just write the file
        if ( $debugImageWork )$msg .= "about to write file to $fullFilePhoto ...";
        if ( !imagejpeg( $img_copyright, $fullFilePhoto ) )
            debugIimageWorkimagerectangle( "$msg $errorBeg 
                    ...,$fullFilePhoto) $errorEnd" );
        if ( $debugImageWork )$msg .= " wrote file ... ";
        if ( !imagedestroy( $img_copyright ) )
            debugImageWorkimagerectangle( "$msg $errorBeg imagedestroy( $img_copyright ) $errorEnd" );
        if ( $debugImageWork )$msg .= "released the memory $eol";
    } else {
        //  ---------------------------------------------------- place text
        $green = imagecolorallocate( $img_copyright, 0, 255, 0 );
        $white = imagecolorallocate( $img_copyright, 255, 255, 255 );
        if ( $white === false )
            throw new Exception( " $errorBeg magecolorallocate($img_copyright, 255,255,0)" );
        $black = imagecolorallocate( $img_copyright, 0, 0, 0 ); # returns zero
        if ( $black === false )
            throw new Exception( " $errorBeg magecolorallocate($img_copyright, 0,0,0)" );

        $h_new = $h_cr + $h_botWhite;
        $imgFinal = imagecreatetruecolor( $w_cr, $h_new );
        if ( !imagecopy( $imgFinal, $img_src, 0, 0, 0, 0, $w_src, $h_cr ) )
            throw new Exception( "4msg E#404 imaagecopy failed " );
        if ( $debugImageWork )$msg .= "rectangle( ..., 0,  $h_cr+1, $w_src, $h_new, $white )";
        if ( !imagefilledrectangle( $imgFinal, 0, $h_cr, $w_src, $h_new, $white ) )
            throw new Exception( " $errorBeg $mdg E#404 
                    imagerectangle(... 0, $h_cr,$w_src, $h_new, $white ) " );
        if ( $debugImageWork )$msg .= "appended white rectangle ...";
        $left = 2;
        $top = $h_cr + 2;
        $bot = $h_new - 3;
        $copyrightMsg = "Photo by $photographer"; //pictures.shaw-weil.com";
        // use true type fonts. First deterine the actual length
        $bounds = imagettftext( $imgFinal, $fontSize, 0, $left, $bot, $black,
            $fontfile, $copyrightMsg );
        if ( $bounds === false )
            throw new Exception( " $errorBeg E#404 imagettftext ($img_copyright,
                    $fontSize, 0, $left, $bot, 
										$black,$fontfile,$copyrightMsg)" );
        $w_text = abs( $bounds[ 0 ] - $bounds[ 2 ] );
        $h_text = abs( $bounds[ 1 ] - $bounds[ 5 ] );
        if ( $debugImageWork )$msg .= "Got the text size of $w_text, $h_text ... ";
        $left = ( $w_cr - $w_text ) / 2; # center the text left to right
        if ( $left < 2 )$left = 2;
        $bot = $h_new - 3; # just barely off the bottom.
        # black out the text just drawn, and redraw it centered on the visiable image
        $tmpHeight = $h_cr - $h_botWhite;
        if ( !imagefilledrectangle( $imgFinal, 0, $h_cr, $w_src, $h_new, $white ) )
            debugImageWorkimagerectangle( "$msg $errorBeg imagefilledrectangle( $imagefilledrectangle( ...,  $imgFinal, 0, $h_cr, $w_src $h_new, $white )))$errorEnd " );
        if ( $debugImageWork )$msg .= "filled rectangle ... 
            imagettftext ($imgFinal, $fontSize, 0, $left, 
										$bot, $black, $fontfile, $copyrightMsg)";
        $bounds = imagettftext( $imgFinal, $fontSize, 0, $left, $bot, $black, $fontfile, $copyrightMsg );
        if ( $bounds === false )
            debugImageWorkimagerectangle( "$msg $errorBeg magettftext ($img_copyright, $fontSize, 0, $left, 
										$bot, $black, $fontfile,$copyrightMsg)" );
        if ( $debugImageWork )$msg .= "Placed at $left, $bot, placed text $copyrightMsg ... ";

        if ( $debugImageWork )$msg .= "about to write file to $fullFilePhoto ...";
        if ( !imagejpeg( $imgFinal, $fullFilePhoto ) )
            debugIimageWorkimagerectangle( "$msg $errorBeg 
                    ...,$fullFilePhoto) $errorEnd" );
        if ( $debugImageWork )$msg .= " wrote file ... ";
        if ( !imagedestroy( $imgFinal ) )
            debugImageWorkimagerectangle( "$msg $errorBeg imagedestroy( $img_copyright ) $errorEnd" );
        if ( $debugImageWork )$msg .= "released the memory $eol";
    }
    // end of drawing photographer name
    # write the ext onto the high resolution image also
    if ( false ) {
        #resize to the high esolution, same size with a bottom border
        $h_high = $h_src; #+ $h_botWhite;
        if ( !enoughmem( $h_high, $w_src ) ) {
            $msg .= ( "not enough memeory$eol" );
            return $msg;
        }
        if ( $debug )$msg .= ( "imagecreatetruecolor($w_src, $h_high)$eol" ); //  place holder with border.
        $img_high = imagecreatetruecolor( $w_src, $h_high );
        #	or die ("imagecreatetruecolor($w_src,$h_high") ;  //  place holder with border.
        if ( $debug )$msg .= "high res space created$eol";
        if ( $debug )$msg .= "imagecopy($img_high, $img_src, 0, 0, 0, 0, $w_src, $h_src)$eol";
        imagecopy( $img_high, $img_src, 0, 0, 0, 0, $w_src, $h_src )
        or die( "imagecopy($img_high, $img_src, 0, 0, 0, 0, $w_src, $h_src)$eol" );
        if ( $debug )$msg .= "copied $eol";
        imagedestroy( $img_src );
    } // end of adding border to high res image
    $msg .= "$eol <a href='/display-one-photo?photoname=$filename'  target='one'>$filename</a>$eol";
    return $msg;
} // end function MakeImakeImages 


function resizeImage( $img_in, $curWidth, $curHeight,
    $maxWidth, $maxHeight, $botBorder ) {
    global $eol;
    $msg = "";
    $debugResize = true;
    if ( $debugResize )$msg .= "$eol resizeImage( Image, $curWidth, $curHeight,
                $maxWidth, $maxHeight, $botBorder ) $eol";
    if ( $curHeight > $maxHeight || $curWidth > $maxWidth ) {
        $ratio = $curHeight / $maxHeight;
        $ratioW = $curWidth / $maxWidth;
        if ( $ratioW < $ratio )
            $ratio = $ratioW;
        $h_new1 = floor( $curHeight / $ratio );
        $w_new1 = floor( $curWidth / $ratio );
        $h_new = $h_new1 + $botBorder;
        $img_out = imagecreatetruecolor( $w_new1, $h_new )
        or die( "imagecreatetruecolor($w_new1, $h_new)" ); //  place holder
        if ( $debugResize )$msg .= "imagecopyresampled(imageIn,imageout, 0, 0, 0, 0, $w_new1, $h_new1, $curWidth, $curHeight) $eol";
        imagecopyresampled( $img_out, $img_in, 0, 0, 0, 0, $w_new1, $h_new1, $curWidth, $curHeight )
        or die( "imagecopyresampled($img_out, $img_in, 0, 0, 0, 0, $w_new1, $h_new, $curWidth, $curHeight" );
    } else {
        $ratio = 1;
        $h_new1 = $curHeight;
        $h_new = $h_new1 + $botBorder;
        $img_out = imagecreatetruecolor( $curWidth, $h_new )
        or die( "imagecreatetruecolor( $curWidth, $curHeight)" ); //  place holder
        imagecopy( $img_out, $img_in, 0, 0, 0, 0, $curWidth, $curHeight )
        or die( "imagecopy($img_out, $img_in, 0,0,0,0, $curWidth, $curHeight)" );
        $w_new1 = $curWidth;
        $h_new = $curHeight;
    }
    if ( $debugResize )$msg .= "Ratio is $ratio $eol";
    if ( $debugResize )$msg .= "Height calculated $h_new1, border of $botBorder$eol";
    if ( $debugResize )$msg .= "Height adjusted $h_new was $curHeight,  $eol";
    if ( $debugResize )$msg .= "Width is $w_new1 was $curWidth $eol";
    $result = array( $img_out, $w_new1, $h_new, $msg );
    return $result;
}
//-------------------------------------------------- ENOUGH MEMORY ?
function enoughmem( $x, $y ) {
    $MAXMEMy = 32 * 1024 * 1024;
    return ( $x * $y * 3 * 1.7 < $MAXMEMy - memory_get_usage() );
}
/*
function processuploadDire() {
    global $eol, $errorBeg, $errorEnd;
    global $uploadPath;
    global $wpdbExtra, $rrw_photos;
    $msg = "";
    $debug = false;
    if ( $debug )$msg .= "processuploadDire ($uploadPath) $eol";
    $handle = opendir( $uploadPath );
    if ( !is_resource( $handle ) )
        throw new Exception( "$errorBeg E#496 failed to 
                                    open $uploadPath $errorEnd" );
    if ( $debug )$msg .= "Entries:$eol";
    $cnt = 0;
    $cntUploaded = 0;
    while ( false !== ( $entry = readdir( $handle ) ) ) {
        $cnt++;
        if ( $cnt > 500 )
            break;
        if ( $debug )$msg .= "$entry, ";
        $fullfilename = "$uploadPath/$entry";
        if ( is_dir( $fullfilename ) )
            continue;
        $filename = basename( $fullfilename, ".jpg" );
        $msg .= "$fullfilename  -- $filename -- ";
        $sql = "select photographer, copyright from $rrw_photos 
                    where filename = '$filename'";
        if ( $debug )$msg .= "$sql $eol";
        $recset_query = $wpdbExtra->get_resultsA( $sql );
        $msg .= "num rows " . $wpdbExtra->num_rows;
        if ( 1 == $wpdbExtra->num_rows ) {
            if ( $debug )$msg .= "one record";
            $photographer = $recset_query[ 0 ][ "photographer" ];
        } else {
            $photographer = "";
        if ( $debug )$msg .= "$photographer $eol";
        if ( $debug )$msg .= "trying image $entry\n";
        $msg .= MakeImakeImages( $fullfilename, $photographer);
        $cntUploaded++;
    } // end while
    $msg .= "\n\nuploaded $cntUploaded files in " .
    rrwUtil::deltaTimer( "uploading files" ) . "\n\n";
    return $msg;
} // end function processuploadDire

*/
?>