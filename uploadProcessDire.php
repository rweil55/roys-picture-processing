<?php

class uploadProcessDire {

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
        $debug = false;

        try {
            if ( $debug )$msg .= "uploadProcessDire ($uploadPath) $eol";
            include "setConstants.php";

            $uploadfilename = rrwPara::String( "uploadfilename", $attr );
            if ( $debug )$msg .= "found $uploadfilename in the calling parameters $eol";


            if ( !empty( $uploadfilename ) ) {
                $msg .= self::ProcessOneFile( $uploadfilename );
                $cntUploaded = 1;
            } else {
                // not a single file request. walk the directory
                $handle = opendir( $uploadPath );
                if ( !is_resource( $handle ) )
                    throw new Exception( "$errorBeg E#496 failed to 
                                    open $uploadPath $errorEnd" );
                if ( $debug )$msg .= "Entries:$eol";
                $cnt = 0;
                $cntUploaded = 0;
                while ( false !== ( $uploadfilename = readdir( $handle ) ) ) {
                    $cnt++;
                    if ( $cnt > 600 )
                        break;
                    if ( is_dir( "$uploadPath/$uploadfilename" ) )
                        continue; // ignore directories
                    if ( $debug ) $msg .= "found $uploadfilename in the diretory search $eol";
                    $msg .= self::ProcessOneFile( $uploadfilename );
                    $cntUploaded++;
                    break;
                } // end while

            } // end  if (! empty($uploadfilename))
            if ( 1 == $cntUploaded ) {
                $photoname = str_replace (".jpg", "", $uploadfilename);
                if ( $debug )$msg .= "DisplayOne( array( \"photoname\" => $photoname ) )";
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
        $debug = false;

        if ( $debug )$msg .= "$entry, ";
        $sourceFile = "$uploadPath/$entry"; // in uplosd dire
        // ------new ----------------------------  validate filename

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
        $photoname = substr( $entry, 0, strlen( $entry ) - 4 );
        if ( $debug )$msg .= "photoname just aftercreate $photoname $eol";
        $pregResults = preg_match( "/[-a-zA-z0-9 _]*/",
            $photoname, $matchs );
        if ( 1 != count( $matchs ) )
            throw new RuntimeException( "file name can consist of only
                letters, numbers, and spaces" );
        // --------------------------- deal with database entry
        if ( $debug )$msg .= "photoname just matchs $photoname $eol";
        $sqlRec = "select * from $rrw_photos 
                        where filename = '$photoname'";
        $recs = $wpdbExtra->get_resultsA( $sqlRec );
        if ( 1 == $wpdbExtra->num_rows ) {
            // have meta data
            $sqldate = "update $rrw_photos set uploaddate = now() 
                                where filename ='$photoname' ";
            $cnt = $wpdbExtra->query( $sqldate );
        } else {
            // no meta data
            $insertData = array(
                "photoname" => $photoname,
                "filename" => $photoname,
                "highresfilename => $entry",
                "uploaddate" => date( "Y-m-d H:i" ),
                /* all others defalt to blank */
            );
            $wpdbExtra->insert( $rrw_photos, $insertData );
        }
        
        $sqlRec = "select * from $rrw_photos 
                        where filename = '$photoname'";
        $recs = $wpdbExtra->get_resultsA( $sqlRec );
        $recOld = $recs[ 0 ];
        $photographer = $recOld["photographer"];
        
        $mdg .= self::MakeImakeImages( $sourceFile, $photographer ) ;

        // meta date exists make it consistant with the EXIF
        $msg .= freewheeling_fixit::Do_forceDatabse2matchexif( $recOld );
        return $msg;
    } // end function processOneFile


    private static function MakeImakeImages( $sourceFile, $photographer ) {
        // assume the file is a temp location - gone when done
        global $eol, $errorBeg, $errorEnd;
        global $photoUrl, $photoPath, $thumbUrl, $thumbPath, $highresUrl, $highresPath;
        //      creates the _cr version with bottom line credit of photographer
        //      creates the thumbnail version
        //      moves to the high_resoltion directory
        $debug = false;
        $msg = "";
        try {
            $desiredW = 200; #	force thumbnail width to this number
            $maxHeight = 700; // limit display mage to yhis number
            $h_botWhite = 20; #	height of the white bar at the bottom for copyright notice
            $fontSize = 12; #	height of the copyright text
            $fontfile = "arial.ttf";
            $fontDire = "/home/pillowan/www-shaw-weil-pictures/wp-content/plugins/roys-picture-processng";
            $fontfile = "$fontDire/mvboli.ttf";

            $fileSplit = pathinfo( $sourceFile );
            if ( $debug )$msg .= rrwUtil::print_r( $fileSplit, true, "the file split" );
            $extension = $fileSplit[ 'extension' ];
            $basename = $fileSplit[ 'basename' ];
            $filename = $fileSplit[ 'filename' ];
            $FullfileHighRes = "$highresPath/$basename";
            $fullfileThumb = "$thumbPath/$filename" . "_tmb.jpg";
            $fullFilePhoto = "$photoPath/$filename" . "_cr.jpg";
            if ( $debug ) {
                $msg .= "base name : " . $fileSplit[ 'basename' ] . $eol;
                $msg .= "extension : " . $fileSplit[ 'extension' ] . $eol;
                $msg .= "FullfileHighRes : $FullfileHighRes $eol";
                $msg .= "fullfileThumb : $fullfileThumb $eol";
                $msg .= "fullFilePhoto : $fullFilePhoto $eol";
            }

            //$msg .= "rename( $sourceFile, $FullfileHighRes )";
            if ( !rename( $sourceFile, $FullfileHighRes ) ) {
                throw new Exception( " $errorBeg $msg E#813 while attempting 
                move ($sourceFile, $FullfileHighRes) $errorEnd" );
            }
            if ( !file_exists( $FullfileHighRes ) ) {
                $msg .= "errorBeg Full Resolution did not get moved $errorEnd $eol 
                $FullfileHighRes $eol";
                return $msg;
            }
            if ( $debug )$msg .= "saved the source file in $FullfileHighRes $eol";

            //  ------------------------------------- got the file now process it
            $debugImageWork = false;
            if ( !file_exists( $fontfile ) ) {
                $msg .= "bad font $fontfile ";
                throw new Exception( "$msg $errorBeg E#812 Problems with the font file $errorEnd" );
            }

            // create new dimensions, keeping aspect ratio
            $imageInfo = getimagesize( $FullfileHighRes );
            $w_src = $imageInfo[ 0 ];
            $h_src = $imageInfo[ 1 ];

            if ( $h_src == 0 || $w_src == 0 ) {
                throw new Exception( " $errorBeg width is $w_src or Height is $h_src zero $errorEnd" );
            }
            if ( $debugImageWork )$msg .= "input inmage is $w_src X $h_src ... ";
            # load the image into core
            $mime_type = mime_content_type( $FullfileHighRes );
            if ( true ) { // $imagick ) 
                $im_src = new Imagick();
                $im_src->readimage( $FullfileHighRes );
                $im_src->scaleImage( 200, 0 );
                $im_src->writeImage( $fullfileThumb );
                $im_src->destroy();

                $im_src = new Imagick();
                $a = $im_src->getversion();
                $im_src->readimage( $FullfileHighRes );
                if ( $h_src > $maxHeight ) {
                    $im_src->scaleImage( 0, $maxHeight );
                }
                if ( !empty( $photographer ) ) {
                    $text = "Photo by $photographer";
                    $h_new = $im_src->getImageHeight();
                    $w_new = $im_src->getImageWidth();
                    $h_new = $h_new + $h_botWhite;
                    $im_src->extentImage( $w_new, $h_new, 0, 0 );

                    $draw = new ImagickDraw();
                    $draw->setStrokeColor( "#00000000" );
                    $draw->setFillColor( "black" );
                    $draw->setStrokeWidth( 0 );
                    //   $draw->setFont( $fontfile );
                    $draw->setFontSize( $fontSize );
                    $metrics = $im_src->queryFontMetrics( $draw, $text );
                    $baseline = $h_new - ( ( $h_botWhite - $fontSize ) / 2 );
                    $marginLeft = ( $w_new - $metrics[ "textWidth" ] ) / 2;
                    $draw->annotation( $marginLeft, $baseline, $text );
                    $im_src->drawImage( $draw );
                    $draw->destroy();
                }

                $im_src->writeImage( $fullFilePhoto );
                $im_src->destroy();
                //         $msg .= "<img src='$photoUrl/$filename" . "_cr.jpg'/>";
                return $msg;

            } else {
                switch ( $mime_type ) {
                    case "image/gif": //   gif -> jpg
                        $img_src = imagecreatefromgif( $FullfileHighRes );
                        break;
                    case "image/jpg": //   jpeg -> jpg
                    case "image/jpeg": //   jpeg -> jpg
                        $img_src = imagecreatefromjpeg( $FullfileHighRes );
                        break;
                    case "image/png": //   png -> jpg
                        $img_src = imagecreatefrompng( $FullfileHighRes );
                        break;
                    default:
                        throw new Exception( " $errorBeg File '$FullfileHighRes' is
                    mime_type, only GIF, JPG, jpeg or PNG are allowed 
                    $errorEnd" );
                }
                //  ------------------------------------------- resize thumbnail
                $aspect = $h_src / $w_src; // maintain the aspect ration
                $w_tmb = $desiredW;
                $h_tmb = $w_tmb * $aspect;
                if ( $debug )$msg .= "resizetmb $w_src, $h_src, $w_tmb, $h_tmb$eol ";
                $img_tmb =
                    self::resizeImage( $img_src, $w_src, $h_src, $w_tmb, $h_tmb );
                if ( !imagejpeg( $img_tmb, $fullfileThumb ) ) //  save new image
                    throw new Exception( " E#816 imagejpeg($img_tmb, 
                                    $fullfileThumb ) failed " );
                if ( $debugImageWork )$msg .= " thumbnail saved to $fullfileThumb $eol";
                imagedestroy( $img_tmb ); // free memory
                // --------------------------------------------- resize copyright
                $maxHeight = 768;
                $aspect = $h_src / $w_src;
                if ( $h_src > $maxHeight ) {
                    $h_cr = $maxHeight;
                    $w_cr = $h_cr / $aspect;
                    $img_copyright =
                        self::resizeImage( $img_src, $w_src, $h_src, $w_cr, $h_cr );
                    if ( $debugImageWork )$msg .= "aspect = $aspect image 
                    resized $w_src X $h_src to $w_cr X $h_cr $eol";

                } else {
                    $img_copyright = $img_src;
                    $h_cr = $h_src;
                    $w_cr = $w_src;
                }
                // img_copywite has been created.
                if ( empty( $photographer ) ) {
                    // skip adding the bottom line, just write the file
                    if ( $debugImageWork )$msg .= "about to write file to $fullFilePhoto ...";
                    if ( !imagejpeg( $img_copyright, $fullFilePhoto ) )
                        throw new Exception( "$msg $errorBeg E#621 
                        imagejpeg( $img_copyright, $fullFilePhoto )$errorEnd" );
                    if ( $debugImageWork )$msg .= " imageno border saved to $fullFilePhoto $eol";
                    if ( !imagedestroy( img_copyright ) )
                        throw new Exception( "$msg $errorBeg E#623 
                            imagedestroy( img_copyright ) $errorEnd" );
                } else {
                    //  ------------------------------------------------ place text
                    $h_new = $h_cr + $h_botWhite;
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
                    $tmpHeight = $h_cr - $h_botWhite;
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


    private static function resizeImage( $img_in, $curWidth, $curHeight,
        $w_new, $h_new ) {
        global $eol;

        $debugResize = true;
        $debugExif = true;

        $scalefactor = $w_new / $curWidth;

        $imgout = imagescale( $img_in, $scalefactor );
        if ( false === $imgout )
            throw new Exception( "E#651 failure in resize usine $scalefactor
            as scale factor or $w_new/ $curWidth " );
        return $imgout;


        $img_out = imagecreatetruecolor( $w_new, $h_new )
        or die( "imagecreatetruecolor($w_new, $h_new)" );
        imagecopyresampled( $img_out, $img_in, 0, 0, 0, 0, $w_new, $h_new, $curWidth, $curHeight )
        or die( "imagecopyresampled($img_out, $img_in, 0, 0, 0, 0, $w_new, $h_new, $curWidth, $curHeight" );
        return $img_out;
    }
    //-------------------------------------------------- ENOUGH MEMORY ?
    private static function enoughmem( $x, $y ) {
        $MAXMEMy = 32 * 1024 * 1024;
        return ( $x * $y * 3 * 1.7 < $MAXMEMy - memory_get_usage() );
    }

} // end class uploadProcessDire
?>