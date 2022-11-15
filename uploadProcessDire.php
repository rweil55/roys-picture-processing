<?php
class uploadProcessDire {
    /*
     *   nameToBottom( $sourceFile, $photographer ) 
     *   resizeImage( $pathin, $pathout, $w_max, $h_max ) {
     */
    public static function upload( $attr ) {
        global $eol, $errorBeg, $errorEnd;
        global $uploadPath;
        // looks for files in the upload directory 
        //      creates database record if not already there
        //      creates the _cr version with bottom line credit
        //      creates the thumbnail version
        //      extracts the exif data to the database
        //      moves to the high_resoltion directory
        //
        $msg = "";
        $debug = rrwUtil::setDebug( "upload" );
        try {
            if ( $debug )$msg .= "uploadProcessDire ($uploadPath) $eol";
            $uploadshortname = rrwPara::String( "uploadshortname", $attr );
            if ( $debug )$msg .= "found $uploadshortname in the calling parameters $eol";
            if ( !empty( $uploadshortname ) ) {
                $msg .= self::ProcessOneFile( $uploadshortname );
                $photoname = strToLower( $uploadshortname );
                $cntUploaded = 1;
            } else {
                // not a single file request. walk the directory
                $handle = opendir( $uploadPath );
                if ( !is_resource( $handle ) )
                    throw new Exception( "$errorBeg E#710 failed to 
                                    open $uploadPath $errorEnd" );
                if ( $debug )$msg .= "Entries:$eol";
                $cnt = 0;
                $cntUploaded = 0;
                while ( false !== ( $uploadshortname = readdir( $handle ) ) ) {
                    $cnt++;
                    if ( $cnt > 600 )
                        break;
                    if ( is_dir( "$uploadPath/$uploadshortname" ) )
                        continue; // ignore directories
                    if ( $debug )$msg .= "found $uploadshortname in the diretory search $eol";
                    $msg .= self::ProcessOneFile( $uploadshortname );
                    $photoname = strToLower( $uploadshortname );
                    $cntUploaded++;
                    break;
                } // end while
            } // end  if (! empty($uploadentry))
            if ( 1 == $cntUploaded ) {
                $photoname = substr( $photoname, 0, -4 );
                if ( $debug )$msg .= "DisplayOne( array( \"photoname\" => $photoname ) ) $eol";
                $msg .= freeWheeling_DisplayOne::DisplayOne(
                    array( "photoname" => $photoname ) );
            } else {
                $msg .= "$eol uploaded $cntUploaded files $eol";
            }
        } // end try
        catch ( Exception $ex ) {
            $msg .= $errorBeg . $ex->getMessage() . $errorEnd;
        }
        return $msg;
    } // end function uploadProcessDire::upload
    private static function processOneFile( $entry ) {
        global $eol, $errorBeg, $errorEnd;
        global $uploadPath;
        global $wpdbExtra, $rrw_photos;
        $msg = "";
        $debug = rrwUtil::setDebug( "onefile" );
        if ( $debug )$msg .= "$entry, ";
        $sourceFile = "$uploadPath/$entry"; // in uplosd dire
        // ------new ----------------------------  validate photoname
        if ( !file_exists( $sourceFile ) )
            throw new Exception( "$msg $errorBeg E#718 processOneFile( $entry )
                     file not found in upload $errorEnd $sourceFile $eol" );
        $mime_type = mime_content_type( $sourceFile );
        switch ( $mime_type ) {
            case 'image/jpeg':
                //    case 'image/png':
                //    case 'image/gif':
                break; // is good
            default:
                throw new RuntimeException( "file '$sourceFile' 
                        minetype is $mime_type, 
                        it should .jpg, " /*.png or .gif"*/ );
        }
        $fileExif = exif_read_data( $sourceFile ); // used to get time
        $photoname = substr( $entry, 0, strlen( $entry ) - 4 );
        $photoname = strtolower( $photoname );
        if ( $debug )$msg .= "photoname just aftercreate $photoname $eol";
        $pregResults = preg_match( "/[-a-zA-z0-9 _]*/",
            $photoname, $matchs );
        if ( 1 != count( $matchs ) )
            throw new RuntimeException( "file name can consist of only
                letters, numbers, and spaces" );
        // only logged n users can get here
        $current_user = wp_get_current_user();
        $userLogin = $current_user->get( "user_login" );
        // --------------------------- deal with database entry
        $Data = array(
            "highresShortname" => $entry,
            "uploaddate" => date( "Y-m-d H:i" ),
            /* all others default to blank */
        );
        $remotefile = rrwPara::String( "remotefile" );
        if ( !empty( $remotefile ) )
            $Data[ "Direonp" ] = $remotefile;
        $sqlRec = "select * from $rrw_photos 
                        where photoname = '$photoname'";
        $recs = $wpdbExtra->get_resultsA( $sqlRec );
        if ( 1 == $wpdbExtra->num_rows ) {
            // have meta data, update it   
            if ( $recs[ 0 ][ "owner" ] != $userLogin ) {
                $updator = $recs[ 0 ][ "owner" ];
                $iiComma = strrpos( $updator, "," );
                if ( false === $iiComma || 
                    substr( $updator, $iiComma +1 ) != $userLogin )
                    $Data[ "owner" ] = "$updator,$userLogin";
                if ( $debug )$msg .= rrwUtil::print_r( $Data, true,
                    "updating $photoname $eol" );
            }
            $key = array( "photoname" => $photoname );
            $cnt = $wpdbExtra->update( $rrw_photos, $Data, $key );
            if ( 1 != $cnt ) {
                $err = "$errorBeg E#782 update no change $errorEnd";
                $msg .= rrwUtil::print_r( $Data, true, $err );
            }
        } elseif ( 0 == $wpdbExtra->num_rows ) {
            // no meta data
            $Data[ "photoname" ] = $photoname;
            $Data[ "photographer" ] = "Mary Shaw";
            $Data[ "owner" ] = $userLogin;
            if ( $debug )$msg .= rrwUtil::print_r( $Data, true,
                "inserting $photoname $eol" );
            $cnt = $wpdbExtra->insert( $rrw_photos, $Data );
            if ( 1 != $cnt ) {
                $err = "$errorBeg E#766 insert fails $errorEnd";
                $msg .= rrwUtil::print_r( $Data, true, $err );
            }
        } else {
            $msg .= "$errorBeg E#679 found " . $wpdbExtra->num_rows . " of data 
                    for $errorEnd $sqlRec $eol";
            throw new Exception( $msg );
        }
        $sqlRec = "select * from $rrw_photos 
                        where photoname = '$photoname'";
        $recs = $wpdbExtra->get_resultsA( $sqlRec );
        $recOld = $recs[ 0 ];
        $photographer = $recOld[ "photographer" ];
        $msg .= freewheeling_fixit::sourceReject( $photoname, "use" );
        $msg .= self::makeImages( $sourceFile, $photographer );
        // meta date exists make it consistant with the EXIF
        $msg .= freewheeling_fixit::fixAssumeDatabaseCorrect( $recOld );
        if ( $debug )$msg .= "getting date $eol";
        $photoDate = freewheeling_fixit::getPhotoDateTime( $fileExif );
        if ( !empty( $photoDate ) ) {
            if ( $debug )$msg .= "photoDate now $photoDate";
            $sqlTimeUpdate = "update $rrw_photos set photodate = '$photoDate'
                                where photoname = '$photoname'";
            $wpdbExtra->query( $sqlTimeUpdate );
        }
        return $msg;
    } // end function processOneFile
    private static function makeImages( $sourceFile, $photographer ) {
        // assume the file is a temp location - gone when done
        global $eol, $errorBeg, $errorEnd;
        global $photoUrl, $photoPath, $thumbUrl, $thumbPath, $highresUrl, $highresPath;
        //      creates the _cr version with bottom line credit of photographer
        //      creates the thumbnail version
        //      moves to the high_resoltion directory
        $msg = "";
        $debug = rrwPara::Boolean( "makeimage" );
        $debugImageWork = rrwPara::Boolean( "imagework" );
        try {
            if ( $debug )$msg = "makeImages( $sourceFile, $photographer ) $eol";
            $widthThumb = 200; #	force thumbnail width to this number
            $heightMax = 768; // limit display mage to yhis number
            $widthMax = 1024;
            $fileSplit = pathinfo( $sourceFile );
            if ( $debug )$msg .= rrwUtil::print_r( $fileSplit, true, "the file split" );
            $extension = $fileSplit[ 'extension' ];
            $basename = $fileSplit[ 'basename' ];
            $photoname = $fileSplit[ 'filename' ];
            $photoname = strToLower( $photoname );
            $FullfileHighRes = "$highresPath/$basename";
            $fullfileThumb = "$thumbPath/$photoname" . "_tmb.jpg";
            $fullFilePhoto = "$photoPath/$photoname" . "_cr.jpg";
            if ( $debug ) {
                $msg .= "base name : " . $fileSplit[ 'basename' ] . $eol;
                $msg .= "extension : " . $fileSplit[ 'extension' ] . $eol;
                $msg .= "FullfileHighRes : $FullfileHighRes $eol";
                $msg .= "fullfileThumb : $fullfileThumb $eol";
                $msg .= "fullFilePhoto : $fullFilePhoto $eol";
            }
            //  -------------------------- setup done, now process 
            $msg .= self::resizeImage( /* do thumbnail */
                $sourceFile, $fullfileThumb, $widthThumb, 0 );
            // source exits or resize would have thrown error
            $msg .= self::resizeImage( /* do dispay image */
                $sourceFile, $fullFilePhoto, $widthMax, $heightMax );
            if ( !empty( $photographer ) )
                $msg .= self::nameToBottom( $fullFilePhoto, $photographer );

            // ----------------- move input image to save location
            if ( !rename( $sourceFile, $FullfileHighRes ) ) {
                throw new Exception( " $errorBeg $msg E#813 while attempting 
                move ($sourceFile, $FullfileHighRes) $errorEnd" );
            }
            if ( !file_exists( $FullfileHighRes ) ) {
                $msg .= "errorBeg Full Resolution did not get moved $errorEnd $eol 
                $FullfileHighRes $eol";
                return $msg;
            }
            if ( $debug )$msg .= "saved the source file 
                                    in $FullfileHighRes $eol";
            return $msg;
        } // end try
        catch ( Exception $ex ) {
            $msg .= $ex->getMessage() . "$errorBeg  E#430 main upload $errorEnd";
        }
        return $msg;
    } // end makeFile
    //
    public static function nameToBottom( $sourceFile, $photographer ) {
        global $eol, $errorBeg, $errorEnd;
        $msg = "";
        $debug = rrwPara::Boolean( "nameToBottom" );

        $h_bottom = 20; #	height of the white bar at the bottom for copyright notice
        $fontSize = 12; #	height of the copyright text
        $fontfile = "arial.ttf";
        $fontDire = "/home/pillowan/www-shaw-weil-pictures/wp-content/plugins/roys-picture-processng";
        $fontfile = "$fontDire/mvboli.ttf";
        if ( !file_exists( $fontfile ) ) {
            $msg .= "bad font $fontfile ";
            throw new Exception( "$msg $errorBeg E#812 Problems with the font file $errorEnd" );
        }
        // use Imagick to add the name to the bottom
        $im_src = new Imagick();
        $im_src->readimage( $sourceFile );
        if ( $debug )$msg .= "adding photographer $eol";
        $text = "Photo by $photographer";
        $h_new = $im_src->getImageHeight();
        $w_new = $im_src->getImageWidth();
        $h_new = $h_new + $h_bottom;
        $im_src->extentImage( $w_new, $h_new, 0, 0 );
        $draw = new ImagickDraw();
        $draw->setStrokeColor( "#00000000" );
        $draw->setFillColor( "black" );
        $draw->setStrokeWidth( 0 );
        //   $draw->setFont( $fontfile );
        $draw->setFontSize( $fontSize );
        $metrics = $im_src->queryFontMetrics( $draw, $text );
        $baseline = $h_new - ( ( $h_bottom - $fontSize ) / 2 );
        $marginLeft = ( $w_new - $metrics[ "textWidth" ] ) / 2;
        $draw->annotation( $marginLeft, $baseline, $text );
        $im_src->drawImage( $draw );
        $draw->destroy();
        $result = unlink( $sourceFile );
        $im_src->writeImage( $sourceFile );
        $im_src->destroy();
        return $msg;
    } // end NameToBottom 
    /*

                     // use GD to add the name to the bottom
 
                    $h_new = $h_cr + $h_bottom;
                    $imgFinal = imagecreatetruecolor( $w_cr, $h_new );
                    if ( !imagecopy( $imgFinal, $img_copyright,
                            0, 0, 0, 0, $w_src, $h_cr ) )
                        throw new Exception( "E#404
                            imagecopy( $imgFinal, $img_copyright,
                                0, 0, 0, 0, $w_src, $h_cr  failed" );
                    $white = imagecolorallocate( $imgFinal, 255, 255, 255 );
                    if ( $white === false )
                        throw new Exception( "E#639
                    imagecolorallocate($imgFinal, 255,255,0)" );
                    $black = imagecolorallocate( $imgFinal, 0, 0, 0 ); # returns zero
                    if ( $black === false )
                        throw new Exception( "E#648
                    imagecolorallocate($imgFinal, 0,0,0)" );
                    if ( !imagefilledrectangle( $imgFinal, 0, $h_cr, $w_src, $h_new, $white ) )
                        throw new Exception( " $msg E#629
                    imagerectangle(... 0, $h_cr,$w_src, $h_new, $white ) " );
                    $left = 2;
                    $top = $h_cr + 2;
                    $bot = $h_new - 3;
                    $copyrightMsg = "Photo by $photographer"; //pictures.shaw-weil.com";
                    // use true type fonts. First deterine the actual length
                    $bounds = imagettftext( $imgFinal, $fontSize, 0, $left, $bot, $black, $fontfile, $copyrightMsg );
                    if ( $bounds === false )
                        throw new Exception( "E#634 imagettftext ($img_copyright,
                                $fontSize, 0, $left, $bot, 
                                $black,$fontfile,$copyrightMsg)" );
                    $w_text = abs( $bounds[ 0 ] - $bounds[ 2 ] );
                    $h_text = abs( $bounds[ 1 ] - $bounds[ 5 ] );
                    $left = ( $w_cr - $w_text ) / 2; # center the text left to right
                    if ( $left < 2 )$left = 2;
                    $bot = $h_new - 3; # just barely off the bottom.
                    # black out the text just drawn, and redraw it centered on the visiable image
                    $tmpHeight = $h_cr - $h_bottom;
                    if ( !imagefilledrectangle( $imgFinal, 0, $h_cr, $w_src, $h_new, $white ) )
                        throw new Exception( "imagefilledrectangle( imgFinal, 0, $h_cr, $w_src $h_new, $white ) " );
                    $bounds = imagettftext( $imgFinal, $fontSize, 0, $left, $bot, $black, $fontfile, $copyrightMsg );
                    if ( $bounds === false )
                        throw new Exception( "imagettftext (img_copyright, $fontSize,               0, $left, $bot, $black, $fontfile,
                                    $copyrightMsg)" );
                    if ( !imagejpeg( $imgFinal, $fullFilePhoto ) )
                        throw new Exception( "imagejpeg( imgFinal, fullFilePhoto " );
                    if ( $debugImageWork )$msg .= " copyright border saved to $fullFilePhoto $eol";
                    if ( !imagedestroy( $imgFinal ) )
                        throw new Exception( "imagedestroy( img_copyright " );
                    if ( !imagedestroy( $img_src ) ) // free memory
                        throw new Exception( "$msg E#628 imagedestroy( img_sec )" );
                } // end of drawing photographer name
            } // end of if imagmik 
        } // end try
        catch ( Exception $ex ) {
            $msg .= $errorBeg . $ex->getMessage() . $errorEnd;
        }
        return $msg;
    } // end function MakeImakeImages 
*/
    public static function resizeImage( $pathin, $pathout,
        $w_max, $h_max ) {
        global $eol;
        global $eol, $errorBeg, $errorEnd;
        $msg = "";
        $debug = rrwPara::Boolean( "resize" );
        $debug = false;

        if ( !file_exists( $pathin ) ) {
            throw new Exception( "$errorBeg E#633 resizeToWidth:file: 
                    ''$pathin' Not found $errorEnd" );
        }
        if ( file_exists( $pathout ) ) {
            $resultLink = unlink( $pathout ); // remove resultant file
            if ( false === $resultLink )
                throw new Exception( "$errorBeg E#634 resizeToWidth:file:
                    '$pathout' did not unlink $errorEnd" );
        }
        $imgGDin = self::imageCreateFrom( $pathin );
        $w_cur = imagesx( $imgGDin );
        $h_cur = imagesy( $imgGDin );
        if ( $debug )$msg .= "input size =  $w_max, $h_max $eol
                            curent siz =  $w_cur. $h_cur $eol";
        $w_scalefactor = $h_scalefactor = $scalefactor = "not set";
        if ( 0 >= $w_max && 0 >= $h_max ) {
            throw new Exception( "$msg $errorBeg E#639 resizeImage: 
            Invalid widths specified $w_max, $h_max  $errorEnd" );
        } elseif ( 0 >= $h_max ) {; // donothing w,0
        } elseif ( 0 >= $w_max ) {
            $scalefactor = $h_max / $h_cur;
            $w_max = round( $w_max * $scalefactor );
            $h_max = 0;
        } else {
            $w_scalefactor = $w_max / $w_cur;
            $h_scalefactor = $h_max / $h_cur;
            $scalefactor = min( $w_scalefactor, $h_scalefactor );
            $w_max = floor( $w_cur * $scalefactor );
            $h_max = 0;
        }
        if ( $debug )
            $msg .= "scales $w_scalefactor, $h_scalefactor used $scalefactor $eol resize scaled $w_cur, $h_cur to 
                                $w_max, $h_max $eol";
        $imgGDout = imagescale( $imgGDin, $w_max );
        if ( false === $imgGDout )
            throw new Exception( "$msg $errorBeg #658 failure in resize 
                                    using $w_max, $h_max $errorEnd " );
        $resultOut = imagejpeg( $imgGDout, $pathout, 100 );
        if ( false === $resultOut )
            throw new Exception( "$msg $errorBeg E#657 failure in resize:write 
                        failed to $pathout $errorEnd" );
        if ( $debug )$msg .= ", file succefully created $eol";
        return $msg;
    } // end resize 
    /*
            $imgGDout = imagescale( $img_in, $scalefactor );
            if ( false === $imgGDout )
                throw new Exception( "E#669 failure in resize usine $scalefactor
                as scale factor or $w_new/ $curWidth " );
            $resultOut = imagejpeg($imgGDout, $pathout);
            if (false === $resultOut)
        
            $img_out = imagecreatetruecolor( $w_new, $h_new )
            or die( "imagecreatetruecolor($w_new, $h_new)" );
            imagecopyresampled( $img_out, $img_in, 0, 0, 0, 0, $w_new, $h_new, $curWidth, $curHeight )
            or die( "imagecopyresampled($img_out, $img_in, 0, 0, 0, 0, $w_new, $h_new, $curWidth, $curHeight" );
            return $img_out;
        } // end  resizeImage
      */
    private static function imageCreateFrom( $sourceFile ) {
        global $eol, $errorBeg, $errorEnd;
        $mime_type = mime_content_type( $sourceFile );
        switch ( $mime_type ) {
            case "image/gif": //   gif -> jpg
                $img_src = imagecreatefromgif( $sourceFile );
                break;
            case "image/jpg": //   jpeg -> jpg
            case "image/jpeg": //   jpeg -> jpg
                $img_src = imagecreatefromjpeg( $sourceFile );
                break;
            case "image/png": //   png -> jpg
                $img_src = imagecreatefrompng( $sourceFile );
                break;
            default:
                throw new Exception( " $errorBeg File '$sourceFile' is
                    mime_type, only GIF, JPG, jpeg or PNG are allowed 
                    $errorEnd" );
        }
        return $img_src;
    }
    //-------------------------------------------------- ENOUGH MEMORY ?
    private static function enoughmem( $x, $y ) {
        $MAXMEMy = 32 * 1024 * 1024;
        return ( $x * $y * 3 * 1.7 < $MAXMEMy - memory_get_usage() );
    }
} // end class uploadProcessDire
?>