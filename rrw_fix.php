<?php
/*		Freewheeling Easy Mapping Application
 *		A collection of routines for display of trail maps and amenities
 *		copyright Roy R Weil 2019 - https://royweil.com
 */
if ( class_exists( "freewheeling_fixit" ) )
    return;
class freewheeling_fixit {
    public static function fit_it( $attr ) {
        global $eol, $errorBeg, $errorEnd;
        error_reporting( E_ALL | E_STRICT );
        ini_set( "display_errors", true );
        $msg = "";
        try {
            $task = rrwUtil::fetchparameterString( "task" );
            $msg .= "<!-- task == $task -->\n";
            switch ( $task ) { // those tasks which do not reqire a line in
                case "listing":
                    $msg .= freewheeling_fixit::listing();
                    return $msg;
                case "compareexif":
                    $msg .= self::displayExif( $attr );
                    return $msg;
                default:
                    break;
            }
            if ( rrwUtil::notAllowedToEdit( "fix things", "", true ) )
                return "$msg $errorBeg E#219 Not alloed to search $errorEnd";
            switch ( $task ) {
                case "add":
                    $msg .= freewheeling_fixit::addphotos();
                    break;
                case "addlist":
                    $msg .= freewheeling_fixit::addList();
                    break;
                case "allow":
                    $msg .= self::AddCapacity( $attr );
                    break;
                case "bydate": //get collection of photos between two dates
                    $msg .= freewheeling_fixit::byDate();
                    break;
                case "badcopyright":
                    $msg .= freewheeling_fixit::badCopyright();
                    break;
                case "copymeta":
                    $msg .= rrwExif::copymeta();
                    break;
                case "copyrightfix":
                    $msg .= freewheeling_fixit::copyrightfix();
                    break;
                case "datanoimage":
                    $msg .= freewhilln_Administration_Pictures::cntMissingImage( true );
                    break;
                case "deleteonephoto":
                    $msg .= freewheeling_fixit::DeleteOnePhotoname( "", "" );
                    break;
                case "deletephoto":
                    $msg .= freewheeling_fixit::deletePhoto();
                    break;
                case "duplicatephoto":
                    $msg .= freewheeling_fixit::setUseStatus( "duplicate" );
                    break;
                case "extractexif":
                    $msg .= self::extractexif();
                    break;
                case "forcedatabase":
                    $msg .= freewheeling_fixit::forceDatabse2matchexif();
                    break;
                case "forceOneExif":
                    $msg .= self::forceOneExif();
                case "extrakeyword":
                    $msg .= freewheeling_fixit::extraKeyword();
                    break;
                case "filelike":
                    $msg .= freewheeling_fixit::filelike( $attr );
                    break;
                case "filesmissing":
                    $msg .= self::filesmissing();
                    break;
                case "highresmissing":
                    $msg .= self::highresmissing();
                    break;
                case "photomissing":
                    $msg .= self::photomissing();
                    break;
                case "reload":
                    $msg .= self::reload( $attr );
                    break;
                case "rename":
                    $msg .= freewheeling_fixit::updateRename( "", "" );
                    break;
                case "searchform":
                    $msg .= freewheeling_fixit::searchform();
                    break;
                case "SourceLoc":
                    $msg .= freewheeling_fixit::direonpOnFileMatch();
                    break;
                case "SourceLocClose":
                    $msg .= freewheeling_fixit::direonpOnFileMatch();
                    $msg .= freewheeling_fixit::updateDiretoryCloseFileMatch();
                    break;
                case "sourcepush":
                    $msg .= freewheeling_fixit::sourcePush();
                    break;
                case "sourcereject":
                    $msg .= freewheeling_fixit::sourceReject( "", "" );
                    break;
                case "sourceset":
                    $msg .= freewheeling_fixit::SourceSetUseFromPhotos();
                    break;
                case "upload":
                    $msg .= self::upload();
                    break;
                case "keyworddups":
                    $msg .= freewheeling_fixit::keywordDups();
                    break;
                case "keywordform":
                    $msg .= freewheeling_fixit::keywordForm();
                    break;
                case "removecreditline":
                    $msg .= freewheeling_fixit::removeCreditline();
                    break;
                case "test":
                    $msg .= freewheeling_fixit::test();
                    break;
                case "testpel":
                    $msg .= rrwExif::testpel();
                    break;
                default:
                    $msg .= "task=$task$eol
       <a href='/fix/?task=add' >upload photos from the source table</a>$eol 
       <a href='/fix/?task=addlist' >list available photos from the source      
                                        table</a>$eol 
       <a href='/fix/?task=by date' > list photos aded between 'startdate' and
                                        'enddate' </a>$eol 
       <a href='/admin#delete' > delete photo - See admin </a>$eol 
       <a href='/fix/?task=duplicatephoto' > set duplicate photo flag </a>$eol 
       <a href='/fix/?task=extractexif' > extract exif to database </a>$eol 
       <a href='/fix/?task=forcedatabase' > force All database to match exif </a>$eol 
       <a href='/fix/?task=forceOneExif' > force One database to match exif </a>$eol 
       <a href='/fix/?task=rename' > rename a photo </a>$eol 
       
   <strong>File consistancy</strong>$eol
      <a href='/fix/?task=highresmissing' target='admin'> high resolution image
                                            missing </a>$eol 
      <a href='/fix/?task=photomissing' > photo information missing</a>$eol 
      <a href='/fix/?task=filesmissing' > highres missng _cr, _tmb </a>$eol 
      <a href='/fix/?task=datanoimage' > data entry with no image </a>$eol
       
   <strong>Copyright</strong>$eol
     <a href='/fix/?task=badcopyright' > copyright not begining with 
                                        copyright </a>$eol
       <a href='/fix/?task=copyrightfix' > fix missing Copyright based on 
                                        photographer </a>$eol 
    <strong>souce data</strong>$eol
        <a href='/fix/?task=SourceLocClose' > close match photoname of  
                                            dire/photoname spoke</a>$eol 
       <a href='/fix/?task=SourceLocMatch' > close match photoname of  
                                            dire/photoname spoke </a>$eol 
      <a href='/fix/?task=filelike' > find source file like 'partfile' </a>$eol 
   <strong>keywords</strong>$eol
      <a href='/fix/?task=keyworddups' > list photos with 
                                            duplicate keywords </a>$eol 
       <a href='/fix/?task=extrakeyword' > list sql to update keyword table
                                                from photo descrioin</a>$eol 
        <a href='/fix/?task=keywordForm' > merge/delete keywords </a>$eol 
     
      ";
                    return $msg;
                    break;
            }
        } catch ( Exception $ex ) {
            $msg .= "$errorBeg E#208 " . $ex->getMessage() . $errorEnd;
        }
        return $msg;
    }
    private static function AddCapacity( $attr ) {
        global $eol, $errorBeg, $errorEnd;
        global $cap_submit;
        $msg = "";
        $cap_submit = "submit_photo";
        $userName = rrwPara::String( "user", $attr );
        $remove = rrwPara::String( "remove", $attr, false );
        if ( empty( $userName ) )
            return "$msg $errorBeg missing user name $errorEnd";
        $user = new WP_User( "", $userName );
        $msg .= rrwUtil::print_r( $user, true, "Found user" );
        if ( false === $user || !$user->exists() )
            return "$msg $errorBeg E#218 no such user '$userName' $errorEnd";
        if ( $remove ) {
            $user->remove_cap( $cap_submit );
            return "$msg user '$userName'can no longer submit photos";
        } else {
            $user->add_cap( $cap_submit, true );
            return "$msg user '$userName'can now submit photos";
        }
        return $msg;
    }
    public static function allowedSubmit() {
        if ( current_user_can( "edit_posts" ) )
            return true;
        if ( current_user_can( "submit_photo" ) )
            return true;
        return false;
    }
    public static function allowedEdit( $photoOwner ) {
        if ( current_user_can( "edit_posts" ) )
            return true;
        $user = wp_get_current_user();
        if ( !$user->exists() )
            return false;
        if ( current_user_can( "edit_photo" ) )
            return true;
        $userlogin = $user->get( "user_login" );
        if ( false !== strpos($photoOwner, $userlogin) )
            return true;
        return false;
    }
    private static function extractexif() {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photographers, $rrw_photos;
        global $photoPath;
        global $numberPhotosMax;
        $msg = " ";
        $displayExif = 2 + $numberPhotosMax;
        $sqlEmpty = "update $rrw_photos set exif = '', width = -1, height = -1";
        $wpdbExtra->query( $sqlEmpty );
        $sqlcnt = "select count(*) from $rrw_photos";
        $cntUse = $wpdbExtra->get_var( $sqlcnt );
        $sqlMissingExif = "select photoname FROM $rrw_photos 
                        where exif = '' "; //and photostatus = 'use' ";
        $recs = $wpdbExtra->get_resultsA( $sqlMissingExif );
        $cntphotos = $wpdbExtra->num_rows;
        $msg .= " there are $cntUse photos in use out of $cntphotos. max = $numberPhotosMax $eol ";
        $cnt = 0;
        foreach ( $recs as $rec ) {
            $cnt++;
            if ( $cnt > $numberPhotosMax )
                break;
            $photoname = $rec[ "photoname" ];
            $msg .= "<a href='/display-one-photo?photoname=$photoname' target='edit'
                        >$photoname</a>, ";
            $photoLoc = "$photoPath/{$photoname}_cr.jpg";
            $exif = rrwExif::rrw_exif_read_data( $photoLoc );
            if ( $displayExif == $cnt )
                $msg .= rrwUtil::print_r( $exif, true, "Exif for the second file read." );
            if ( !is_array( $exif ) ) {
                $msg .= "$errorBeg E#688 bad exif for $photoLoc $errorEnd ";
            } else {
                $height = $exif[ "COMPUTED" ][ "Height" ];
                $width = $exif[ "COMPUTED" ][ "Width" ];
                $exifJson = json_encode( $exif );
                $update = array( "exif" => $exifJson,
                    "height" => $height,
                    "width" => $width, );
                $where = array( "photoname" => $photoname );
                // $msg .= "$exifJson $eol";
                $numRows = $wpdbExtra->update( $rrw_photos, $update, $where );
                $msg .= $numrows;
            }
        }
        return $msg;
    }
    private static function filesmissing() {
        global $eol;
        global $photoUrl, $photoPath, $thumbUrl, $thumbPath, $highresUrl, $highresPath;
        global $displaykey;
        ini_set( "display_errors", true );
        $msg = "";
        $dirlistRes = self::getFileList( $highresPath );
        $dirlist_cr = self::getFileList( $photoPath );
        $dirlist_tmb = self::getFileList( $thumbPath );
        $datalist = self::getDatabaseList();
        $msg .= rrwUtil::print_r( $dirlistRes, true, "Data ist " );
        $lists = array(
            "Database" => $datalist,
            "High Resolution" => $dirlistRes,
            "display photo" => $dirlist_cr,
            "Thumbnail" => $dirlist_tmb,
        );
        $msg .= self::compareLists( "Database", $datalist,
            "High Resolution", $dirlistRes );
        $msg .= self::compareLists( "Database", $datalist,
            "display photo", $dirlist_cr, "_cr" );
        $msg .= self::compareLists( "Database", $datalist,
            "Thumbnail", $dirlist_tmb, "_tmb" );
        $msg .= self::compareLists( "High Resolution", $dirlistRes,
            "Database", $datalist );
        $msg .= self::compareLists( "High Resolution", $dirlistRes,
            "display photo", $dirlist_cr, "_cr" );
        $msg .= self::compareLists( "High Resolution", $dirlistRes,
            "Thumbnail", $dirlist_tmb, "_tmb" );
        return $msg;
    } // end filemissing
    private static function compareLists( $name1, $list1, $name2, $list2,
        $ending = "" ) {
        global $eol, $errorBeg, $errorEnd;
        $msg = "";
        $msg .= "<strong>There are $name1 images and 
                        no matching $name2 images </strong>$eol";
        $cntFound = 0;
        foreach ( $list1 as $file => $fileFull ) {
            $iiDot = strpos( $file, "." );
            if ( false !== $iiDot )
                $file = substr( $file, 0, $iiDot ) . $ending . substr( $file, $iiDot );
            if ( !array_key_exists( $file, $list2 ) ) {
                $msg .= "Compare --$file--";
                $displayFull = self::removeHost( $fileFull );
                $displayPhoto = self::removeHost( $file );
                $msg .= "$displayFull $name1 is not in $name2 $displayPhoto";
                $msg .= self::addcreate_searchlink( $file ) . $eol;
                $cntFound++;
            }
        }
        $msg .= "Found $cntFound $eol";
        $msg .= "<strong>There are $name2 images and 
                        no matching $name1 images </strong>$eol";
        $cntFound = 0;
        foreach ( $list2 as $file => $fileFull ) {
            $iiDot = strpos( $file, "." );
            $file = str_replace( $ending, "", $file );
            if ( !array_key_exists( $file, $list1 ) ) {
                $msg .= "Compare --$file--";
                $displayFull = self::removeHost( $fileFull );
                $displayPhoto = self::removeHost( $file );
                $msg .= "$displayFull $name2 is not in $name1 $displayPhoto";
                $msg .= self::addcreate_searchlink( $file ) . $eol;
                $cntFound++;
            }
        }
        $msg .= "Found $cntFound $eol";
        return $msg;
    } // end coparelists
    /*
        return $msg;
        $msg .= "<strong>There is high resolution image, 
                    but no display (_cr) image </strong><br>";
        $cntFound = 0;
        foreach ( $dirlistRes as $file => $fileFull ) {
            if ( !array_key_exists( $file, $dirlist_cr ) ) {
                $displayFull = self::removeHost( $fileFull );
                $displayPhoto = self::removeHost( $photoPath );
                $msg .= "$displayFull is not in $displayPhoto/.._cr ";
                $msg .= self::addcreate_searchlink( $file );
                $cntFound++;
            }
        }
    } // end file missing
    /*
        $msg .= "<strong>$cntFound high resolution image </strong>$eol$eol
                    <strong>There are (_cr) image, 
                    but no high resolution image </strong><br>";
        foreach ( $dirlist_cr as $file => $fileFull ) {
            if ( !array_key_exists( $file, $dirlistRes ) ) {
             $displayFull = self::removeHost( $fileFull );
                $displayhighresPath = self::removeHost( $highresPath );
                   $msg .= "$displayFull is not in $displayhighresPath ";
                $msg .= self::addcreate_searchlink( $file );
            }
        }
        return $msg;
    }
*/
    private static function highresmissing() {
        global $eol;
        global $wpdbExtra, $rrw_photos;
        global $highresPath;
        $msg = "";
        $msg .= "<strong>There is meta data in the database, 
                    but no high resolution image </strong><br>";
        $dirlistRes = self::getFileList( $highresPath );
        $sqlall = "select highresShortname from $rrw_photos";
        $recAlls = $wpdbExtra->get_resultsA( $sqlall );
        $cntMissng = 0;
        foreach ( $recAlls as $rec ) {
            $highresShortname = strToLower( $rec[ "highresShortname" ] );
            if ( !array_key_exists( "$highresShortname", $dirlistRes ) ) {
                $msg .= "$highresShortname is not in $highresPath, but we have " .
                "<a href='/display-one-photo?photoname=$highresShortname' " .
                " target ='displayone' > photo information </a>$eol ";
                $cntMissng++;
            }
        }
        $msg .= "there were $cntMissng high resolution images missng$eol";
        return $msg;
    }
    private static function photomissing() {
        global $eol;
        global $wpdbExtra, $rrw_photos;
        global $highresPath;
        $msg = "";
        $msg .= "<strong>There is high resolution image, 
                    but no meta information in the database </strong><br>";
        $dirlistRes = self::getFileList( $highresPath );
        $sqlall = "select photoname from $rrw_photos";
        $recAlls = $wpdbExtra->get_resultsA( $sqlall );
        $photolist = array();
        foreach ( $recAlls as $rec ) {
            $photoname = strtolower( $rec[ "photoname" ] );
            $photolist[ $photoname ] = 1;
        }
        $cntFound = 0;
        foreach ( $dirlistRes as $highres => $fullfilename ) {
            $highresTest = self::removeJpgDire( $highres );
            if ( !array_key_exists( $highresTest, $photolist ) ) {
                $msg .= "<a href='/display-one-photo?photoname=$highresTest' >
                $highres</a> is  a high resolution image, 
                        but not in the photo database ";
                $msg .= " [ " . self::updateeRenameLink( $highresTest ) . " ] ";
                $msg .= " [ " . self::addcreate_searchlink( $highres ) . " ] ";
                $cntFound++;
            }
        }
        $msg .= "Found $cntFound photos  but no photo information ";
        return $msg;
    }
    private static function addcreate_searchlink( $filefullname ) {
        // may have -s, -w ...
        global $eol, $errorBeg, $errorEnd;
        $msg = "";
        $photoname = self::removeJpgDire( "$filefullname" );
        $msg .= " [ <a href='/fix/?task=filelike&photoname=$photoname'
                        target='reject' > find matches </a> ] ";
        if ( file_exists( $filefullname ) )
            $msg .= " [ <a href='/fix/?task=reload&fullfilename=$filefullname' 
                    target='create' > create meta</a> 
                    <strong>$photoname</strong> ] $eol ";
        return $msg;
    }
    private static function linkTo127upload( $sourcefile ) {
        global $httpSource;
        $host = $_SERVER[ 'HTTP_HOST' ];
        if ( strpos( $host, "dev" ) !== false )
            $dev = "&dev=1";
        else
            $dev = "&dev=0";
        $photoname = self::removejpgDire( $sourcefile );
        $sourceEncoded = urlencode( $sourcefile );
        $photonameEncoded = urlencode( $photoname );
        $bold = "<strong>";
        $unbold = "</strong>";
        if ( false !== strpos( $sourcefile, "contest" ) ||
            false !== strpos( $sourcefile, "show" ) )
            $bold = $unbold = "";
        $link = "<a href='$httpSource/pict/sub.php?task=pushtoupload$dev" .
        "&sourcefile=$sourceEncoded&photname=$photonameEncoded'  >
                    $bold create entry  $photoname $unbold</a> ";
        return $link;
    }
    private static function reload( $attr ) {
        // given a fullfilename, move file to the upload directoy and reload it
        global $eol, $errorBeg, $errorEnd;
        global $uploadPath, $highresPath;
        $msg = "";
        $debug = false;
        $fullFilename = rrwPara::String( "fullfilename" );
        if ( empty( $fullFilename ) )
            throw new Exception( "$msg $errorBeg E#669 missing filename $errorEnd" );
        $photoname = self::removeJpgDire( $fullFilename );
        $iislash = strrpos( $fullFilename, "/" );
        $Highresname = substr( $fullFilename, $iislash );
        $uploadfullname = "$uploadPath/$Highresname";
        // move the file
        if ( $debug )$msg .= "= rename( $highresPath/$Highresname, $uploadfullname $eol ";
        $answer = rename( "$highresPath/$Highresname", $uploadfullname );
        if ( $debug )$msg .= "lets process upload dire$eol";
        $attr = array( "uploadshortname" => $Highresname );
        if ( $debug )$msg .= rrwUtil::print_r( $attr, true, "parameters to upload" );
        $msg .= uploadProcessDire::upload( $attr );
        return $msg;
    }
    public static function Author2Copyright( $attr ) {
        // copy the database coppyright to the photo
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photographers, $rrw_photos;
        global $photoPath;
        $msg = "
            ";
        try {
            $filename = rrwUtil::fetchparameterString( "
            filename " );
            if ( empty( $filename ) )
                throw new Exception( "
            $errorBeg E #188 missing filename parameter $errorEnd" );
            $sqlPhoto = "select copyrightDefault from $rrw_photos ph
        join $rrw_photographers ger on ph.photographer = ger.photographer 
                    where filename ='$filename' ";
            $copyRight = $wpdbExtra->get_var( $sqlPhoto );
            if ( false === $copyRight || empty( $copyRight ) )
                throw new Exception( "$errorBeg E#187 missing Author or author copyright $errorEnd $sqlPhoto $eol " );
            $fileFull = "$photoPath/$filename" . "_cr.jpg";
            //      $msg .= rrwExif::rrwExif::pushToImage( $fileFull, "Copyright", $copyRight );
            $sqlUpdate = "update $rrw_photos set copyright = '$copyRight' 
                        where filename = '$filename'";
            $cnt = $wpdbExtra->query( $sqlUpdate );
            $msg .= "update $cnt record in the photo database";
        } catch ( Exception $ex ) {
            $msg .= "E#186 top level - " . $ex->getMessage();
        }
        return $msg;
    }
    private static function removeCreditline( $attr ) {
        global $eol, $errorBeg, $errorEnd;
        global $photoPath, $photoUrl;
        // remove 20 pixels from the bottom of the image.
        $taglinePath = "$photoPath/tagline";
        $taglineUrl = "$photoUrl/tagline";
        $msg = "working on $photoPath $eol";
        $files = GetDireList( $photoPath, true );
        $msg .= "<table>\n";
        $cntDire = 0;
        foreach ( $files as $file => $fullFile ) {
            try {
                $cntDire++;
                if ( $cntDire > 5000 )
                    break;
                $outfile = "$taglinePath/$file";
                $imgdis = new Imagick();
                $handle = fopen( $fullFile, "r" );
                $imgdis->readImageFile( $handle );
                $w_src = $imgdis->getImageWidth();
                $h_src = $imgdis->getImageHeight();
                $h_chop = $h_src - 20;
                $imgdis->chopImage( 0, $h_chop, 0, 0 );
                $imgdis->writeImage( $outfile );
                $imgdis->destroy();
                $imgUrl = "<img src='$taglineUrl/$file' />";
                $msg .= rrwFormat::cellRow( $file, " $w_src X $h_src, $imgUrl" );
            } catch ( Exception $ex ) {
                print "Working on $file $eol $eol";
            }
        }
        $msg .= "</table\n";
        $msg .= "processed $cntDire files $eol";
        return ( $msg );
    }
    private static function displayExif( $attr ) {
        global $eol;
        global $photoPath, $highresPath;
        $msg = "";
        $photoname = rrwUtil::fetchparameterString( "photoname" );
        $file1 = "$highresPath/$photoname.jpg";
        $file2 = "$photoPath/${photoname}_cr.jpg";
        $exif1 = rrwExif::rrw_exif_read_data( $file1 );
        $exif2 = rrwExif::rrw_exif_read_data( $file2 );
        $display1 = rrwUtil::print_r( $exif1, true, "${photoname}.jpg" );
        $display2 = rrwUtil::print_r( $exif2, true, "${photoname}_cr.jpg" );
        $msg .= "<table>" . rrwFormat::headerRow( $file1, $file2 ) .
        rrwFormat::CellRow( $display1, $display2 ) .
        "</table> 
        $eol<img src='$file2' />";
        return $msg;
    }
    /*
        private static function extraKeyword() {
            list( $msg, $cntExtra, $cntMissing ) = freewheeling_fixit::extraKeywordCnts( true );
            return $msg;
        }
        public static function extraKeywordCnts( $list = false ) {
            global $eol;
            global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_keywords, $rrw_trails;
            $msg = "";
            $sqlKey = "select keyword from $rrw_keywords";
            $recKeys = $wpdbExtra->get_resultsA( $sqlKey );
            $keys = array();
            foreach ( $recKeys as $recKey ) {
                $keys[ $recKey[ "keyword" ] ] = 0;
            }
            $sqlPhotoKeys = "select filename, photoKeyword from $rrw_photos ";
            $recphotos = $wpdbExtra->get_resultsA( $sqlPhotoKeys );
            $cntMissing = 0;
            foreach ( $recphotos as $recphoto ) {
                $words = explode( ",", $recphoto[ "photoKeyword" ] );
                foreach ( $words as $word ) {
                    $word = trim( $word );
                    if ( empty( $word ) )
                        continue;
                    if ( array_key_exists( $word, $keys ) ) {
                        $keys[ $word ]++;
                    } else {
                        $cntMissing++;
                        $keys[ $word ] = 1;
                        if ( $list ) {
                            $filename = $recphoto[ "filename" ];
                            $sqlins = "insert into $rrw_keywords (keywordFilename, keyword, is_location )  values ('$filename', '$word','0')";
                            $msg .= "$sqlins $eol";
                        }
                    }
                }
            }
            $cntExtra = 0;
            ksort( $keys );
            foreach ( $keys as $key => $value ) {
                if ( 0 == $value ) {
                    // key word in keyword list, but not in any file
                    $cntExtra++;
                    if ( $list ) {
                        $sqldel = "delete from $rrw_keywords where keyword = '$key'";
                        $msg .= "$sqldel $eol";
                    }
                }
            }
            $msg .= "Found $cntExtra Extra keywords, $cntMissing missing keywords $eol";
            return array( $msg, $cntExtra, $cntMissing );
        }
    */
    private static function setUseStatus( $newStatus ) {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_keywords, $rrw_trails;
        global $photoPath, $thumbPath, $highresPath;
        $msg = "";
        $debug = false;
        $photoname = rrwUtil::fetchparameterString( "photoname" );
        if ( empty( $photoname ) )
            return "$msg $eol $errorBeg No photoname specifed $errorEnd";
        $sqlsetUseStatus = "update $rrw_source set sourcestatus = '$newStatus' 
                        where searchname = '$photoname'";
        $answer = $wpdbExtra->query( $sqlsetUseStatus );
        $msg .= "$answer &nbsp; $sqlsetUseStatus $eol ";
        $sqlsetUseStatus = "update $rrw_photos set photostatus = '$newStatus' 
                        where filename = '$photoname'";
        $answer = $wpdbExtra->query( $sqlsetUseStatus );
        $msg .= "$answer &nbsp; $sqlsetUseStatus $eol ";
        $url = "https://here/pict/fix127.php?task=$newStatus" .
        "photo&photoname=$photoname";
        $msg .= "[ <a href='$url' target='admin'> $url </a> ]";
        return "$msg  $newStatus $photoname";
    }
    private static function addphotos() {
        global $eol;
        global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_keywords, $rrw_trails;
        global $photoPath, $thumbPath, $highresPath;
        $msg = "Needs a rewrite to determne what is gong on ";
        return $msg;
        /*
        $debug = false;
        $sqlCoor = "select * from $rrw_trails
                        where not corridor = ''
                            order by corridor, trail_short_name  ";
        $recCoors = $wpdbExtra->get_resultsA( $sqlCoor );
        $cntTrail = 0;
        foreach ( $recCoors as $recCoor ) {
            $cntTrail++;
            if ( $cntTrail > 100 )
                break;
            $trailname = $recCoor[ "trailName" ];
            $shortname = $recCoor[ "trail_short_name" ];
            $msg .= "----- Extracting $trailname with $shortname $eol";
            $sql = "SELECT sourcefullname, searchname from $rrw_source 
                    where not searchname in 
                        (select filename from $rrw_photos) 
                        and sourcestatus = 'use' and searchname like '%$shortname%'
                        order by searchname";
            if ( $debug )$msg .= "$sql $eol";
            $recPhotos = $wpdbExtra->get_resultsA( $sql );
            $msg .= "found " . $wpdbExtra->num_rows . " of photos to add $eol";
            $cnt = 0;
            $msg .= "<table>";
            $sqllist = array();
            foreach ( $recPhotos as $recPhoto ) {
                $cnt++;
                if ( $cnt > 10 )
                    break 2;
                $filename = $recPhoto[ "searchname" ];
                $direonp = $recPhoto[ "sourcefullname" ];
                $msg .= rrwFormat::CellRow( $filename, $trailname, $direonp );
                $sqlInsert = "insert into $rrw_photos (filename, trail_name, photographer, 
            direonp) values (
            '$filename', '$trailname', 'Mary Shaw',
            '$direonp')";
                array_push( $sqllist, $sqlInsert );
            }
        }
        $msg .= "</table>
        $eol [ You now need to run 
        c:\_e\php sub.php &nbsp; in a command window ] 
                follwowed by  [ <a href='/upload' target='admin'' >upload /</a> ]
                [ <a href='fix?task=compareexif'> copyright</a> $eol";
        foreach ( $sqllist as $sql ) {
            $cnt = $wpdbExtra->query( $sql );
            $msg .= "$cnt &nbsp; $sql $eol";
        }
        return $msg;
        */
    }
    public static function addsearch( $which = "" ) {
        global $eol;
        global $rrw_photos, $rrw_source;
        $msg = "";
        $which = rrwPara::string( "which" );
        switch ( $which ) {
            case "wpa":
                $select = "sourcefullname like '%w-pa-trails%' ";
                break;
            case "ytrek":
                $select = " sourcefullname like '%ytrek%'";
                break;
            case "contest":
                $select = " sourcefullname like '%contest%'";
                break;
            case "show":
                $select = " sourcefullname like '%show%'";
                break;
            case "they":
                $select = " sourcefullname like '%they%'";
                break;
            case "xc":
                $select = " sourcefullname like '%xc%'";
                break;
            case "canoe":
                $select = " sourcefullname like '%canoe%'";
                break;
            case "":
                $select = " (sourcefullname like '%w-pa-trails%' or" .
                " sourcefullname like '%ytrek%' )";
                break;
            default:
                $select = "sourcefullname like '%which%' ";
                break;
        }
        $sql = "select * from $rrw_source where sourcestatus = '' and " .
        " $select and searchname like '%-s'";
        return $sql;
    }
    private static function addList() {
        // display a list of field that might added
        global $eol;
        $msg = "";
        $sql = self::addsearch( "sourceFullname" ) .
        " order by searchname, sourceFullname limit 17"; 
        // a sql to find addtional files to add
        $msg .= print "addlist:sql: $sql $eol";
        $msg .= self::rrwFormatDisplayPhotos( $sql,
            "photos that might be uploaded", 20 );
        return $msg;
    }
    private static function deletePhoto() {
        // called fro the admin page 
        global $eol, $errorBeg, $errorEnd;
        $msg = "";
        $debug = false;
        $why = rrwUtil::fetchParameterString( "why" );
        if ( empty( $why ) )
            $why = "because";
        $photoname = rrwUtil::fetchParameterString( "del2" );
        if ( $debug )$msg .= "into delete photo $photoname $eol";
        $msg .= "task = deleteing the Photo <strong>$photoname</strong> $eol ";
        $del3 = rrwUtil::fetchParameterString( "del3" );
        if ( strcmp( $photoname, $del3 ) != 0 ) {
            $msg .= "input parameters do not match. photo not deleted";
            return $msg;;
        }
        $msg .= self::DeleteOnePhotoname( $photoname, $why );
        return $msg;
    }
    private static function DeleteOnePhotoname( $photoname, $why ) {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_source, $rrw_keywords;
        global $photoPath, $thumbPath, $highresPath, $rejectPath;
        $msg = "";
        $debug = true;
        if ( empty( $photoname ) ) {
            $photoname = rrwPara::String( "photoname" );
            if ( empty( $photoname ) ) {
                $msg .= "Missing or invalid photo name";
                return $msg;
            }
        }
        if ( empty( $why ) ) {
            $why = rrwPara::String( "why" );
            if ( empty( $why ) )
                $why = "duplicate";
        }
        if ( $debug )$msg .= "called DeleteOnePhotoname( $photoname, $why ) $eol";
        $sql = "delete from $rrw_photos where photoname ='$photoname'";
        $answer = $wpdbExtra->query( $sql );
        $msg .= "Deleted $answer photo record, &nbsp";
        $sql = "delete from $rrw_keywords where keywordFilename = '$photoname'";
        $answer = $wpdbExtra->query( $sql );
        $msg .= "$answer keyword records, &nbsp; ";
        $sqlreject = "update $rrw_source set sourcestatus = '$why' 
                        where searchname = '$photoname'";
        $answer = $wpdbExtra->query( $sqlreject );
        $msg .= "marked $answer source record as $why, &nbsp; $eol ";
        $filename1 = "$thumbPath/$photoname" . "_tmb.jpg";
        $filename2 = "$photoPath/$photoname" . "_cr.jpg";
        $filename3 = "$highresPath/$photoname.jpg";
        $ToFilename3 = "$rejectPath/$photoname.jpg";
        if ( false ) {
            $msg .= "photo '$photoname' deleted from database. $eol 
            Can be recovered by doing Add Photos, if not deleted from server$eol 
            $filename1 $eol $filename2 $eol ";
        } else {
            $notexist = "<strong>does not exist</strong>";
            if ( file_exists( $filename1 ) ) {
                $msg .= "$eol Deleting file $filename1 $eol";
                unlink( "$filename1" );
            } else {
                $msg .= "file $filename1 $notexist $eol ";
            }
            if ( file_exists( $filename2 ) ) {
                $msg .= "$eol Deleting file $filename2 $eol";
                unlink( "$filename2" );
            } else {
                $msg .= "file $filename2 $notexist $eol";
            }
            if ( file_exists( $filename3 ) ) {
                $msg .= "renameing $filename3 -- to -- , $ToFilename3 ";
                $msg .= rename( $filename3, $ToFilename3 );
            } else {
                $msg .= "file $filename3 $notexist $eol";
            }
            $msg .= $eol;
        }
        return $msg;
    } // end functin deletephoto 
    private static function keywordDups() {
        global $eol;
        global $wpdbExtra, $rrw_keywords;
        $msg = "";
        $sqlKeywordDups = "select count(*), keyword, keywordfilename
                from $rrw_keywords 
                group by keyword, keywordFilename having count(*) > 1";
        $msg .= freewheeling_fixit::rrwFormatDisplayPhotos( $sqlKeywordDups,
            "duplicate key word entries" );
        return $msg;
    }
    private static function keywordForm() {
        global $eol;
        global $wpdbExtra, $rrw_keywords, $rrw_photos;
        $msg = "";
        $submit = rrwUtil::fetchparameterString( "submit" );
        if ( empty( $submit ) ) {
            if ( !isset( $old ) )
                $old = "";
            $msg .= "<form> 
            Old Keyword:<input type='text' name='old' id = 'old' />,
            New Keyword:<input type='text' name='new' id = 'new' />
            <input type='hidden' name='task' id='task' value='keywordform' />
            <input type='submit' name='submit' id='submit' 
                        value='change the keyword $old' />
            </form >";
        } else {
            $old = rrwUtil::fetchparameterString( "old" );
            $new = rrwUtil::fetchparameterString( "new" );
            $sqlChange = "update $rrw_keywords set keyword = '$new' 
                        where keyword = '$old' ";
            $cnt = $wpdbExtra->query( $sqlChange );
            $msg .= "Update $cnt keywords in keyword table from $old to $new $eol
            Do a refresh to see the selection counts $eol";
        }
        return $msg;
    } //  end keywordForm
    private static function listPhotog() {
        global $eol;
        global $wpdbExtra, $rrw_photos;
        $msg = "";
        $msg = "";
        $photog = rrwUtil::fetchparameterString( "photog" );
        if ( empty( $photog ) ) { //nophotographer specified
            $sql = "select count(*) cnt, photographer, copyright, photostatus
                    from $rrw_photos 
                    group by photographer, copyright ";
            $msg .= freewheeling_fixit::rrwFormatDisplayPhotos( $sql,
                "count of photographer in the database " );
            return $msg;
        }
        $sql = "select photoname, photographer, photostatus from $rrw_photos 
                where photographer = '$photog'
                order by photographer, photoname";
        $msg .= freewheeling_fixit::rrwFormatDisplayPhotos( $sql,
            "photos created to the photographer $photog ", 5000 );
        return $msg;
    }
    private static function GetPhotogList( & $photographers ) {
        global $eol;
        $msg = "";
        global $wpdbExtra, $rrw_photos, $rrw_photographers;
        $sqlPhotog = "select Photographer, copyrightdefault from $rrw_photographers";
        $recphotogs = $wpdbExtra->get_resultsA( $sqlPhotog );
        foreach ( $recphotogs as $recphotog ) {
            $Photographer = $recphotog[ "Photographer" ];
            $copyrightdefault = $recphotog[ "copyrightdefault" ];
            $photographers[ "$Photographer" ] = $copyrightdefault;
        }
        //     $msg .= rrwUtil::print_r($photographers, true, "$eol photographers inside get$eol ");
        return $msg;
    }
    private static function byDate() { //get collection of photos between two dates
        global $eol;
        global $photoDB, $rrw_photos;
        $msg = "";
        $startdate = rrwUtil::fetchparameterString( "startdate" );
        $startdate = new DateTime( $startdate );
        $startdate = $startdate->format( "Y-m-d" );
        $enddate = rrwUtil::fetchparameterString( "enddate" );
        $enddate = new DateTime( $enddate );
        $enddate = $enddate->format( "Y-m-d" );
        $update = " DATE_FORMAT(uploaddate, '%Y-%m-%d') ";
        $sql = "select direonp, photoname, photostatus " .
        "from $rrw_photos where '$startdate' <= $update and $update < '$enddate'"; // missng source
        print "<!-- sql is $sql -->\n" ;
        $msg .= freewheeling_fixit::rrwFormatDisplayPhotos( $sql,
            "photos uploaded between $startdate and less than $enddate" );
        return $msg;
    }
    private static function badCopyright() {
        global $eol;
        global $wpdbExtra, $rrw_photos;
        $msg = "";
        $msg = "";
        $sql = "SELECT photoname, copyright, photostatus FROM $rrw_photos
                        where not copyright like 'copyright%' 
                        and photostatus = 'use' ";
        $msg .= freewheeling_fixit::rrwFormatDisplayPhotos( $sql,
            "photos copyright does not start with 'copyright'" );
        $msg .= "Run <a href='/fix/?task=compareexif' target='admin'>update something</a> $eol";
        return $msg;
    }
    private static function copyrightfix() {
        global $eol;
        global $wpdbExtra, $rrw_photos, $rrw_photographers;
        $msg = "";
        $sqlFind = " select photoname, copyrightDefault from $rrw_photos ph
        join $rrw_photographers ar on ar.photographer = ph.photographer
                        where copyright = '' and ! ph.photographer =''";
        $recFinds = $wpdbExtra->get_resultsA( $sqlFind );
        $msg .= "found " . $wpdbExtra->num_rows . " records with
                    photographer,, but no copyright $eol";
        $cntUpdate = 0;
        foreach ( $recFinds as $recFind ) {
            $photoname = $recFind[ "photoname" ];
            $copyright = $recFinf[ "copyrightDefault" ];
            $sqlShove = "update $rrw_photos set coppyright = '$copyright'
                        where photoname = '$photoname'";
            $snt = $wpdbExtra->query( $sqlShove );
            $cntUpdate += $cnt;
        }
        $msg .= "updated $cntUpdate records $eol";
        return $msg;
    }
    public static function fileLike( $attr ) {
        global $eol;
        global $wpdbExtra, $rrw_source, $rrw_photos;
        global $highresUrl, $highresPath;
        global $httpSource;
        $msg = "";
        $debug = false;
        $host = $_SERVER[ 'HTTP_HOST' ];
        if ( strpos( $host, "dev" ) !== false )
            $dev = "&dev=1";
        else
            $dev = "&dev=0";
        $partial = rrwPara::String( "partial", $attr );
        $photoname = rrwPara::String( "photoname", $attr );
        if ( $debug )$msg .= "filelike:photoname: $photoname, partial $partial $eol";
        if ( empty( $partial ) )
            $partial = $photoname;
        if ( empty( $photoname ) )
            $highresShortname = "$partial.jpg";
        else
            $highresShortname = "$photoname.jpg";
        $sqlimage = "select * from $rrw_photos where photoname = '$photoname'";
        $recs = $wpdbExtra->get_resultsA( $sqlimage );
        if ( 1 == $wpdbExtra->num_rows ) {
            $source = "<strong>Source Directory<?strong>" . $recs[ 0 ][ "DireOnP" ] . $eol;
        } else {
            $source = "";
        }
        $highresShortname = str_replace( "._cr", "", $highresShortname );
        $FullFilwname = "$highresPath/$highresShortname";
        if ( file_exists( $FullFilwname ) ) {
            $imageSize = getimagesize( $FullFilwname );
            $msg .= "<img src='$highresUrl/$highresShortname' width='300px' /> &nbsp;
            <a href='/display-one-photo?photoname=$photoname' >show image details</a>
            Current high reslution image" . $imageSize[ 3 ] . $source . $eol .
            $photoname;
        }
        foreach ( array( "_cr.", "_tmb.", "-s.", "-w." ) as $check ) {
            if ( strpos( $photoname, $check ) !== false ) {
                $newphotoname = str_replace( $check, ".", $photoname );
                $msg .= " &nbsp; <a href='/fix/?task=rename&photoname=$photoname&newname=$newphotoname' target='check' > becomes  $newphotoname </a> ";
            }
        } // end foreach check
        $msg .= "<table><tr><td><form method='get' action='/fix/'>
        <input type='hidden' id='photoname' name='photoname' value='$photoname' />
            <input type='text' id='partial' name='partial' value='$partial' />
            <input type='hidden' id='task' name='task' value='filelike' />
            <input type='submit' value='go search' />
            </form></td><td align='left' valign='bottom'>
            <a href='/fix/?task=deleteonephoto&photoname=$partial&why=duplicate' > delete $partial</a>
           ";
        if ( empty( $partial ) )
            return "</tr></table>$msg";
        $sqlExisting = "select * from $rrw_photos where photoname like '%$partial%'";
        $recs = $wpdbExtra->get_resultsA( $sqlExisting );
        if ( 4 < $wpdbExtra->num_rows ) {
            $msg .= "found " . $wpdbExtra->num_rows . " that match";
        } else {
            foreach ( $recs as $rec ) {
                $photoname = $rec[ "photoname" ];
                $msg .= "&nbsp; &nbsp; &nbsp; " . $photoname;
            }
        }
        $msg .= "</tr></table>";
        // lets look
        foreach ( array( "searchname, sourceFullname",
                "sourceFullname, searchname"
            ) as $orderby ) {
            $msg .= "<hr> <strong>ordered by by $orderby</strong> $eol";
            $sqlFind = "
                select sourcefullname, searchname, aspect, sourcestatus
                from $rrw_source 
                where sourcefullname like '%$partial%' 
                or sourceFullname like '%$partial%' 
                order by $orderby ";
            $recs = $wpdbExtra->get_resultsA( $sqlFind );
            $msg .= "Found " . $wpdbExtra->num_rows . " records like '$partial' $eol";
            $msg .= "<table>$eol" . rrwFormat::HeaderRow(
                "Status", "On local machine", "aspect", "upload", "" );
            $color = rrwUtil::colorswap();
            $display = "";
            $cnt = 0;
            foreach ( $recs as $rec ) {
                $color = rrwUtil::colorswap( $color );
                $cnt++;
                $newphotoname = $rec[ "searchname" ];
                $ext = substr( $newphotoname, -3 );
                if ( "PCD" == $ext )
                    continue;
                $sourcefullname = $rec[ "sourcefullname" ];
                $aspect = $rec[ "aspect" ];
                $status = $rec[ "sourcestatus" ];
                $link = "<a href='/fix/?task=sourcepush&photoname=$photoname"
                    . "&sourcefullname=$sourcefullname$dev' > change dire</a> ";
                if ( strncmp( "d:", $sourcefullname, 2 ) == 0 ) {
                    $sourcefile = "d:" . substr( $sourcefullname, 2 );
                    $sourceuploadLink = self::linkTo127upload( $sourcefile );
                } else {
                    $sourceuploadLink = "";
                }
                $imgFile = $httpSource . substr( $sourcefullname, 2 );
                $sourcefullnameDisplay = "<a href='$imgFile' 
                target='127'>$sourcefullname</a> <button id='btn' onclick='onClickCopy2Clip(\"got it\");' > copy</button> ";
                $msg .= rrwFormat::CellRow( $color, "$cnt $status",
                    $sourcefullnameDisplay, $aspect, $sourceuploadLink, $link );
                $display .= "<div class='rrwDinoItem' >
                        <a href='$imgFile' target='one' >
                        <img src='$imgFile' width='300' />
                        $eol $cnt)$sourcefullname $eol $newphotoname $eol $aspect $eol
                        </a></div>";
            }
            $msg .= "</table>\n";
        }
        $msg .= "<strong> ------------ available images ------ </strong$eol
        <div class='rrwDinoGrid' >" . $display . "</div>";
        $msg .= "<strong> ------------  to update ------ </strong$eol";
        $msg .= rrwPicSubmission::displayform(); //uses photographer, photoname
        return $msg;
    }
    private static function sourcePush( $attr = "" ) {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photos;
        $msg = "";
        $photoname = rrwPara::String( "photoname", $attr );
        $sourcefullname = rrwPara::String( "sourcefullname", $attr );
        if ( empty( $photoname ) || empty( $sourcefullname ) )
            throw new Exception( "$msg $errorBeg 
                    E#657 missing photoname or sourceFullPath dire $errorEnd" );
        $sqlExist = "select * from $rrw_photos where searchname = '$photoname'";
        $wpdbExtra->query( $sqlExist );
        if ( 0 == $wpdbExtra->num_rows ) {
            $msg .= "creating a new record, 
                    <a href=fix/?task=upload&photoname=$photoname > 
                    will need to be uploaded</a> $eol";
            $sqlinsert = "insert inot $rrw_photos set photoname = '$photoname',
                        direonp = '$sourceFullPath'";
            $cnt = $wpdbExtra->query( $sqlInsert );
            if ( 0 == $cnt )
                $msg .= "$errorBeg E#674 file failed to insert $errorEnd
                            $sqlinsert $eol";
            $msg .= "inserted $cnt records $eol ";
        } else {
            $sqlUpdate = "update $rrw_photos set direonp = '$sourcefullname'
                            where photoname = '$photoname'";
            $cntUpdate = $wpdbExtra->query( $sqlUpdate );
            if ( 0 == $cntUpdate )
                $msg .= "$errorBeg E#683 file failed to update $errorEnd
                            $sqlUpdate $eol";
            $msg .= "update $cntUpdate records 
            <a href='/display-one-photo?photoname=$photoname' > $photoname </a>$eol ";
        }
        return $msg;
    } // end functin sourcepush()
    public static function sourceReject( $photoname, $why ) {
        // update sll version of this photo form the usable collection
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_source;
        $msg = "";
        if ( empty( $photoname ) ) {
            $photoname = rrwPara::String( "photoname" );
            if ( empty( $photoname ) )
                return "$msg $errorBeg E#217 source reject missing name $errorEnd";
        }
        if ( empty( $why ) ) {
            $why = rrwPara::String( "why" );
            if ( empty( $why ) )
                $why = "duplicate";
        }
        $photoname = self::removeEndingsJpgDire( $photoname );
        if ( empty( $photoname ) )
            return "E#216 No photo name given to source Reject $eol";
        foreach ( self::photonameEndings() as $end ) {
            if ( "use" == $why ) {
                $cnt = $wpdbExtra->query( "update $rrw_source 
                        set sourcestatus  = '$why'
                        where searchname = '$photoname$end'" );
                //          $msg .= "update $cnt for $photoname$end $eol";
            } else {
                $msg .= self::DeleteOnePhotoname( "$photoname$end", $why );
            }
        }
        return $msg;
    }
    public static function photonameEndings() {
        return array( "-s", "-w", "-b", "" );
    }
    private static function removeJpgDire( $filename ) {
        $iiSlash = strrpos( $filename, "/" );
        if ( false !== $iiSlash )
            $filename = substr( $filename, 0, $iiSlash );
        $iiDot = strrpos( $filename, "." );
        if ( false !== $iiDot )
            $filename = substr( $filename, 0, $iiDot );
        $filename = strToLower( $filename );
        return $filename; // realy the stripped done photoname
    }
    private static function removeEndingsJpgDire( $photoname ) {
        global $eol;
        $debug = false;
        if ( $debug ) print "removeEndingsJpgDire:namein: $photoname";
        $photoname = self::removeJpgDire( $photoname );
        if ( $debug ) print ", after jpgdire: $photoname";
        // not perfrect, order matters
        foreach ( array( "_cr" => 3, "_tmp" => 4, "-s" => 2, "-w" => 2, "-b" => 2 ) as $remove => $cnt ) {
            $end = substr( $photoname, -$cnt );
            if ( $remove == $end ) {
                $photoname = substr( $photoname, 0, -$cnt );
                if ( $debug ) print ", found $remove-$cnt $photoname";
            }
        }
        if ( $debug ) print ", return $photoname $eol";
        return $photoname;
    }
    private static function removeHost( $filename ) {
        $filename = str_replace( "-dev/", "/", $filename );
        $filename = str_replace( "/home/pillowan/www-shaw-weil-pictures", "", $filename );
        return $filename;
    }
    private static function listing() {
        global $eol;
        global $rrw_photos, $rrw_source, $rrw_keywords;
        $msg = "";
        $sqlWhere = rrwUtil::fetchparameterString( "where" );
        $table = rrwUtil::fetchparameterString( "table" );
        $limitin = rrwPara::Number( "limit" );
        $startAt = rrwPara::Number( "startat" );
        $description = rrwUtil::fetchparameterString( "description" );
        if ( 0 != $startAt )
            update_site_option( "sqlCount-$description", $startAt );
        if ( 0 == $limitin )
            $limitin = 20;
        $limit = $limitin + $startAt;
        $urlNext = "/fix/?task=listing&table=$table&description=$description&limit=&limit&where=$sqlWhere" .
        "&startat=$limit";
        $sqlWhere = str_replace( "xxy", "'", $sqlWhere );
        $sqlWhere = htmlspecialchars_decode( $sqlWhere );
        switch ( $table ) {
            case "$rrw_source":
                $fields = "sourcefullname, searchname, 1 createentry, aspect, sourcestatus";
                $orderby = "sourcefullname, searchname, aspect";
                break;
            case "$rrw_keywords":
                $msg .= "$eol I#699 &nbsp; select * from $rrw_keywords 
                    where keywordfilename in (
                        select keywordfilename from $rrw_keywords where not keywordfilename in( select photoname from $rrw_photos) order by keywordfilename, keyword ) limit 20 $eol ";
                $fields = "keywordfilename, keyword";
                $orderby = "keywordfilename, keyword";
                break;
            default:
                if ( empty( $table ) )
                    $table = "$rrw_photos";
                $fields = " photoname, '1' search,  trail_name, photographer, photostatus ";
                $orderby = " photoname, trail_name, photographer, photostatus ";
        }
        //  item is  trail_name, direonp
        $sql = "select $fields from $table 
                where $sqlWhere  order by $orderby "; // missng source
        if ( !empty( $limit ) )
            $sql .= " limit $limit ";
        $msg .= freewheeling_fixit::rrwFormatDisplayPhotos( $sql,
            "photos wih $description", $startAt, $urlNext );
        return $msg;
    }
    private static function rrwFormatDisplayPhotos( $sql, $description,
        $startAt = 0, $urlNext = "" ) {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra;
        global $httpSource;
        $msg = "";
        error_reporting( E_ALL | E_STRICT );
        try {
            print "<!-- sql request is \n\n$sql\n, start = $startAt\n\n -->\n" ;
            $missngsource = $wpdbExtra->get_resultsA( $sql );
            $missngsourceCnt = $wpdbExtra->num_rows;
            $msg .= "<strong>There are $missngsourceCnt $description</strong> $eol";
            $cnt = 0;
            if ( 0 == $missngsourceCnt )
                return "$msg E#215 $sql $eol ";
            $color = rrwUtil::colorSwap();
            $msg .= "$eol <table><tr> \n";
            $msg .= rrwFormat::CellHeader( "count" );
            foreach ( $missngsource[ 0 ] as $name => $valu ) {
                $msg .= rrwFormat::CellHeader( $name );
            }
            $msg .= "</tr>";
            $cnt = 0;
            $totalCnt = count( $missngsource );
            $display = "";
            foreach ( $missngsource as $recset ) {
                $cnt++;
                if ( $cnt < $startAt )
                    continue;
                $color = rrwUtil::colorSwap( $color );
                $msg .= "<tr style='background-color:$color;' >\n";
                if ( array_key_exists( "sourcestatus", $recset ) )
                    $sourcestatus = $recset[ "sourcestatus" ];
                else
                    $sourcestatus = "";
                $msg .= rrwFormat::Cell( "$cnt $sourcestatus " );
                foreach ( $recset as $name => $valu ) {
                    $imgfile = "";
                    switch ( strToLower( $name ) ) {
                        case 'photoname':
                        case "searchname":
                            $photoname = $valu;
                            $valu = freewheeling_fixit::formatPhotoLink( $valu );
                            break;
                        case 'filename':
                        case "keywordfilename":
                        case "searchfullname":
                            $valu = freewheeling_fixit::formatPhotoLink( $valu );
                            break;
                        case 'photographer':
                            $valu = "<a href='/fix?task=photog&photog=$valu' 
                                        target='list'  > $valu </a>";
                            break;
                        case 'create entry':
                            $valu = self::linkTo127upload( $sourcefile );
                            break;
                        case "sourcefullname":
                            $imgFile = $httpSource . substr( $valu, 2 );
                            break;
                        case "status":
                            if ( !empty( $valu ) )
                                $valu = "<strong>$valu</strong>";
                            break;
                        case "aspect":
                            $aspect = $valu;
                            break;
                        case "search":
                            $photonameEncoded = urlencode( $photoname );
                            $url = "fix/?task=filelike&partial=" .
                            "$photonameEncoded&photoname=$photonameEncoded";
                            $valu = "<a href='$url' target='search' > search<a>";
                            break;
                        default:
                            // just plane $valu
                            break;
                    }
                    $msg .= rrwFormat::Cell( $valu );
                } // end foreach itrm n the a record.
                $msg .= "</tr>";
                if ( !empty( $imgFile ) ) {
                    $photonameStripped = self::removeEndingsJpgDire( $photoname );
                    $display .= "<div class='rrwDinoItem' >
                        <a href='$imgFile' target='one' >
                        <img src='$imgFile' width='300' />
                        $eol $cnt)$sourcestatus $photoname $eol </a> $aspect $eol
                <a href='/fix/?task=sourcereject&photoname=$photoname&why=reject'
                        target='reject' > reject All versions photo 
                        </a>$eol
                <a href='/fix/?task=sourcereject&photoname=$photoname&why=use'
                        target='reject' > clean status 
                        </a> 
                        <a href='/fix/?task=filelike&photoname=$photonameStripped'
                        target='reject' > find matches 
                        </a>
                        </div>";
                }
            } // end for each record 
            $msg .= "</table>\n";
            if ( $cnt < $totalCnt ) {
                $remain = $totalCnt - $cnt;
                $msg .= "$errorBeg E#201 limit of reached,
                            there are $remain more $eol";
            }
            $msg .= "<strong> ------------ available images ------ </strong$eol
          <div class='rrwDinoGrid' >" . $display . "</div>";
            $msg .= self::filelike( array() ) .
            " ] [ <a href='$urlNext' >Next group</a> $eol";
        } catch ( Exception $ex ) {
            $msg .= "E#206 " . $ex->getMessage() . "<p> $sql </p> ";
        }
        return $msg;
    }
    public static function updateRenameform() {
        return "<form acton='/fix/' method='post' >
            Old Name: 
        <input type='text' name='photoname' size='20' value='' />
        New Name: 
        <input type='text' name='newname' size='20' value='' />
         &nbsp;
        <input type='submit' value='rename dataase/files' />
        <input type='hidden' name='task' value='rename' />
        </form>";
    }
    public static function updateeRenameLink( $photoname = "" ) {
        return "<a href='/fix/?task=rename&photoname=$photoname.jpg&newname=' > rename $photoname</a>";
    }
    public static function updateRename( $photoname = "", $newname = "" ) {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photos, $rrw_keywords;
        global $photoPath, $thumbPath, $highresPath;
        $msg = "";
        $debug = false;
        if ( empty( $photoname ) ) {
            $photoname = trim( rrwUtil::fetchparameterString( "photoname" ) );
            $newname = trim( rrwUtil::fetchparameterString( "newname" ) );
        }
        if ( empty( $newname ) && empty( $photoname ) ) {
            return self::updateRenameform();
        }
        if ( $debug )$msg .= "start updateRename( $photoname, $newname ) $eol";
        if ( empty( $photoname ) )
            return "$msg $errorBeg E#214 updateRename missng photoname $errorEnd";
        if ( empty( $newname ) ) {
            // no new name supplied, Assume remove endings
            $newname = self::removeEndingsJpgDire( $photoname );
        }
        $photoname = self::removeJpgDire( $photoname );
        $newname = self::removeJpgDire( $newname );
        if ( $photoname == $newname )
            return "$msg $errorBeg E#213 $photoname = $newname, 
                    rename not neccessary $errorEnd";
        //  ---------------------------------------------- move three files
        $msg .= self::checkAndRename( "$photoPath/${photoname}.jpg",
            "$photoPath/{$newname}.jpg" );
        $msg .= self::checkAndRename( "$photoPath/${photoname}_cr.jpg",
            "$photoPath/{$newname}_cr.jpg" );
        $msg .= self::checkAndRename( "$thumbPath/{$photoname}_tmb.jpg",
            "$thumbPath/{$newname}_tmb.jpg" );
        $msg .= self::checkAndRename( "$highresPath/{$photoname}.jpg",
            "$highresPath/{$newname}.jpg" );
        //  ------------------------------------------------- things in rrw_photos
        $sqlExist = "select photoname from $rrw_photos where photoname = '$newname'";
        $recExists = $wpdbExtra->get_resultsA( $sqlExist );
        if ( 0 != $wpdbExtra->num_rows )
            $msg .= "$errorBeg E#295 file $newname is already in the photo table,
                    not replaced $errorEnd";
        else {
            $sqlupdate = "update $rrw_photos set photoname = '$newname'
                where photoname ='$photoname' ";
            $cnt = $wpdbExtra->query( $sqlupdate );
            $msg .= "updated $cnt records with $sqlupdate $eol";
            $sqlupdate = "update $rrw_photos 
                set photoname = replace(filename, '$photoname', '$newname')
                where photoname ='$photoname' ";
            $cnt = $wpdbExtra->query( $sqlupdate );
            $msg .= "updated $cnt records with $sqlupdate $eol";
            $sqlupdate = "update $rrw_keywords set keywordfilename = '$newname'
                where keywordfilename ='$photoname' ";
            $cnt = $wpdbExtra->query( $sqlupdate );
            $msg .= "updated $cnt records $sqlupdate $eol";
        } // ------------------------------------------------  direonp
        $sqldireonp = "update $rrw_photos set direonp =
                replace(direonp, '$photoname', '$newname') where photoname = '$newname'";
        $cnt = $wpdbExtra->query( $sqldireonp );
        $msg .= "updated $cnt records with $sqldireonp $eol";
        $msg .= self::updateRenameform();
        return $msg;
    } // end  updateRename( $photoname, $newname ) {
    private static function checkAndRename( $fileFrom, $fileTo ) {
        global $eol, $errorBeg, $errorEnd;
        $msg = "";
        if ( !file_exists( $fileFrom ) ) {
            $msg .= "From file $fileFrom does not exists $eol";
            return $msg;
        }
        if ( file_exists( $fileTo ) ) {
            $msg .= "To file $fileTo exists and will be over ridden $eol";
            unlink( $fileTo );
        }
        rename( $fileFrom, $fileTo );
        return "$msg rename ($fileFrom, $fileTo) $eol";
    }
    public static function searchform() {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photos, $rrw_photographers;
        $msg = "
<form action='/displayphotos/' method=POST>
<input type='text' name='photoname' id='searchphoto' value='' /> photo<br />
<input type='text' name='directory' id='searchdire' value='' /> directory<br />
<input type='text' name='location' id='searchloc' value='' /> location<br />
<input type='text' name='people' id='searchpeople' value='' /> people<br />
<input type='text' name='source' id='searchsource' value='' /> source<br />";
        $msg .= rrwFormat::selectBox( $wpdbExtra, $rrw_photographers,
            "photographer", "", "photographer" ) . " photographer $eol";
        $msg .= "$eol 
<input type='submit' name='submit' id='submit' value='Search any' />
</form>";
        return $msg;
    }
    private static function direonpOnFileMatch() {
        // used to relate the entries in photo datta database to the file location on spoke
        // if file name matchs then update the diterctory field
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_photographers;
        $msg = "$errorBeg needs check because of rrw_souce dchange $errorEnd";
        $debug = false;
        try {
            $msg .= direReport( "direonp", "with blank DireOnP" );
            //      return $msg;
            $sql = "select direonp, sourcefullname, sourceFullname from $rrw_photos 
            left join $rrw_source on filename=sourceFullname 
                where sourceFullname is not null and not direonp = sourcefullname 
                    and sourcestatus ='use' order by sourceFullname";
            if ( $debug );
            $msg .= "$sql $eol";
            $recs = $wpdbExtra->get_resultsA( $sql );
            $cntrecs = $wpdbExtra->num_rows;
            $msg .= "Found $cntrecs rows of data - 
                    probably duplicate entries $eol";
            $cntInsert = 0;
            $sqlList = array(); // list of the flippers
            foreach ( $recs as $rec ) {
                $cntInsert++;
                if ( $cntInsert > 500 )
                    break;
                $direonp = $rec[ "direonp" ];
                $searchname = $rec[ "searchname" ];
                $sourceFullname = $rec[ "sourceFullname" ];
                $sqlins = "update $rrw_photos set direonp = 'sourceFullname'
                    where photoname = '$searchname' ";
                $updateCnt = $wpdbExtra->query( $sqlins );
                if ( $debug )$msg .= "$updateCnt -- $sqlins $eol ";
                $sqlList[ $sourceFullname ] = $sourceFullname;
            }
            $msg .= direReport( "direonp", "with blank DireOnP" ) . "$eol <table> $eol";
            foreach ( $sqlList as $key => $value ) {
                $keyDisplay = "<a href='http://pictures.shaw-weil.com/display-one-photo" .
                "?photoname=$key' target='one' >$key</a>";
                $msg .= rrwFormat::CellRow( $keyDisplay, $value );
            }
            $msg .= "</table> <hr weight=3>";
            $sqlblank = "select photoname from $rrw_photos 
                where direonp = '' or direonp is null ";
            $msg .= "$sqlblank $eol ";
            $recs = $wpdbExtra->get_resultsA( $sqlblank );
            $cntrecs = $wpdbExtra->num_rows;
            $msg .= "Found $cntrecs rows of blank direonp $eol";
            $msg .= "<table> ";
            foreach ( $recs as $rec ) {
                $key = $rec[ "photoname" ];
                $keyDisplay = "<a href='http://pictures.shaw-weil.com/display-one-photo" .
                "?photoname=$key' target='one' >$key</a>";
                $msg .= rrwFormat::CellRow( $keyDisplay );
            }
            $msg .= "</table> ";
        } catch ( Exception $ex ) {
            $msg .= "E3494 in direonpOnFileMatch $sourceFullname" . $ex->getMessage();
        }
        return $msg;
    } // wns updateDiretoryOnFileMatch
    private static function updateDiretoryCloseFileMatch() {
        global $eol;
        global $wpdbExtra, $rrw_photos, $rrw_source;
        $msg = "";
        // used to relate the entries in photo datta database to the file location on spoke
        // if file name is close (contains) then update the diterctory field
        $msg .= direReport( "direonp", "with blank DireOnP" );
        $sql = "select photoname from $rrw_photos wheredireonp = '' ";
        $msg .= "$sql $eol ";
        $recs = $wpdbExtra->get_resultsA( $sql );
        $cntrecs = $wpdbExtra->num_rows;
        $msg .= "Found $cntrecs photos not marched to directories of data $eol";
        $photos = array();
        foreach ( $recs as $rec )
            array_push( $photos, $rec[ "photoname" ] );
        $sqlSource = "select searchname, sourceFullname from $rrw_source 
                    where sourcestatus = 'use'";
        $recPcs = $wpdbExtra->get_resultsA( $sqlSource );
        $cntrecs = $wpdbExtra->num_rows;
        $msg .= "Found $cntrecs posible  marchs to portion of file name $eol";
        $cntInsert = 0;
        $msg .= "<table>" . rrwFormat::HeaderRow( "PC dire", "web File",
            "PC File", "New File" );
        $color = rrwUtil::colorSwap();
        //  create a new file name by some rules
        $testPass = 1; // new filename based on pattern '/\d*_*(.+)_\d*/';
        $testPass = 2; // new file naame is forced to match
        $TestPass = 3; //
        foreach ( $recPcs as $recPC ) {
            $sourceFullname = $recPC[ "sourceFullname" ];
            $searchname = $recPC[ "searchname" ];
            for ( $ii = 0; $ii < count( $photos ); $ii++ ) {
                if ( strpos( $photos[ $ii ], $sourceFullname ) !== false ) {
                    // got a match update databse rename files
                    switch ( $testPass ) {
                        case 1:
                            $filename = $photos[ "$ii" ];
                            $pattern = '/\d*_*(.+)_\d*/';
                            $newname = preg_replace( $pattern, '${1}', $filename );
                            break;
                        case 2:
                            $filename = $photos[ "$ii" ];
                            $newname = $sourceFullname;
                            break;
                        case 3:
                            $filename = $photos[ "$ii" ];
                            $pattern = '/\d*_*(.+)_\d*/';
                            $newname = preg_replace( $pattern, '${1}', $filename );
                            $newname = $sourceFullname;
                            break;
                        default:
                            throw new Exception( "E#204 iinvalid testpas = $testPass" );
                    }
                    if ( $newname != $searchname ) {
                        $match = "** adjust ***";
                    } else
                        $match = "";
                    $color = rrwUtil::colorSwap( $color );
                    $msg .= rrwFormat::CellRow( $color, $sourceFullname, $filename,
                        $sourceFullname, $newname, $match );
                    if ( $newname != $sourceFullname )
                        continue;
                    $msg .= self::updateRename( $filename, $newname );
                    $cntInsert++;
                    if ( $cntInsert > 5 )
                        return "$msg </table> - finished one entry";
                } // end search matches a piece of filename 
            } // end looking for match in the photolist
        } // end looping thru the source list
        $msg .= "</table> $eol";
        $msg .= direReport( "direonp", "with blank DireOnP" ) . $eol . $eol;
        $sqlEmpty = "select photoname, photo_id from $rrw_photos wheredireonp = '' 
            and photoname like '%_ms%'
            order by substring(photoname,7,5) ";
        $recEmptys = $wpdbExtra->get_resultsA( $sqlEmpty );
        $msg .= "</table>\n";
        foreach ( $recEmptys as $recEmpty ) {
            $photoname = $recEmpty[ "photoname" ];
            $photo_id = $recEmpty[ "photo_id" ];
            if ( strpos( $photoname, "_ms" ) === false ) {
                $newname = "";
            } else {
                $newname = "dale0" . substr( $photoname, 9, 1 ) . "-img00" .
                substr( $photoname, 11, 2 );
            }
            $c1 = "<form action='/fix?task=rename' method='post' >
                <input type='text' value='photoname' name='photoname'/>";
            $c2 = "<input type='text' value='$newname' name='new'/>";
            $c3 = "<input type='submit' value='rename' /> </form>";
            $c4 = "<a href='https://pictures.shaw-weil.com/display-one-photo?photoid=$photo_id' target='one' > $photo_id </a>";
            $msg .= rrwFormat::CellRow( $c1, $c2, $c3, $c4 );
        }
        $msg .= "</table>\n";
        return $msg;
    } // end function updateDiretoryCloseFileMatch() 
    private static function forceOneExif() {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_source;
        $photoname = $rrwPara::String( "photoname" );
        $sql = "select * from $rrw_photo where photoname = '$photoname'";
        $recs = $wpdbExtra->get_resultsA( $sql );
        if ( 1 != $wpdbExtra->num_recs )
            throw new Exception( "$errorBeg E#674 wrong number of records found, or not found $errorEnd $sql $eol" );
        $msg .= fixAssumeExifCorrect( $recs[ 0 ] );
        return $msg;
    }
    private static function SourceSetUseFromPhotos() {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_source, $rrw_photos;
        $msg = "";
        $status = "use";
        $sqlAll = "select photoname, photostatus from $rrw_photos ";
        $recalls = $wpdbExtra->get_resultsA( $sqlAll );
        foreach ( $recalls as $recall ) {
            $photoname = $recall[ "photoname" ];
            $status = $recall[ "photostatus" ];
            foreach ( $self::photonameEndings() as $end ) {
                $sqlUpdate = "update $rrw_source set status = '$status'
                where searchname = '$photoname$end'";
                if ( debug )$msg .= "$sqlUpdate $eol";
                $cnt = $wpdbExtra->query( $sqlUpdate );
                if ( $debug )$msg .= "update $cnt records for $photoname$end $eol";
            } //  for each photonameEndings
        } // end foreach photoname
        return $msg;
    } // end SourceSetUseFromPhotos
    private static function forceDatabse2matchexif() {
        global $eol, $errorBeg, $errorEnd;
        $msg = "";
        try {
            $sql = "SELECT * FROM $rrw_photos
                     order by photoname limit 1 ";
            $recs = $wpdbExtra->get_resultsA( $sql );
            $cntRecs = 0;
            $cntPUShed = 0;
            foreach ( $recs as $rec ) {
                $msg .= fixAssumeExifCorrect( $rec );
                // search through all photos
                $cntRecs++;
                if ( $cntRecs > 3 )
                    break;
            }
            return $msg;
        } // end try
        catch ( Exception $ex ) {
            $msg .= $ex->getMessage();
        }
        return $msg;
    } // end function
    public static function fixAssumeDatabaseCorrect( $rec ) {
        return self::fixAssume( $rec, false );
    }
    public static function fixAssumeExifCorrect( $rec ) {
        return self::fixAssume( $rec, true );
    }
    private static function fixAssume( $rec, $exifCorrect ) {
        // if either is empty, move known to empty
        // if difference, use $exif to determine which way to move
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photos, $rrw_keywords,
        $rrw_photographers;
        global $photoPath, $thumbPath, $highresPath;
        $msg = "";
        $debugForce = false;
        $sofar = "fixAssumeExifCorrect entry";
        try {
            $photoname = $rec[ "photoname" ];
            $databaseCopyright = $rec[ "copyright" ];
            $datebasePhotoDate = $rec[ "PhotoDate" ];
            $databasePhotographer = $rec[ "photographer" ];
            $databaseTrail = $rec[ "trail_name" ];
            $databaseKeyword =
                freeWheeling_DisplayOne::keywordsDisplay( $photoname );
            $databaseHeight = $rec[ "height" ];
            $databaseWidth = $rec[ "width" ];
            $fullFile = "$photoPath/$photoname" . "_cr.jpg";
            $sqlUpdate = array();
            if ( !file_exists( $fullFile ) )
                throw new Exception( "$errorBeg E#203  file $fullFile not found $errorEnd" );
            if ( $debugForce )$msg .= "
            <a href='display-one-photo?photoname=$photoname' 
            target='one' >$photoname </a>, ";
            // --------------------------------------------- exif
            // https://exiftool.org/TagNames/EXIF.html list most tags
            try {
                $fileExif = rrwExif::rrw_exif_read_data( $fullFile );
            } // end try
            catch ( Exception $ex ) {
                throw new Exception( "$msg E#202 " . $ex->getMessage() . " while        reading exif of '$fullFile' in fixAssumeExifCorrect " );
            }
            if ( empty( $fileExif ) || !is_array( $fileExif ) )
                throw new Exception( "$errorBeg #185 Fetch of exif 
                                from $fullFile failed $errorEnd" );
            if ( $debugForce )$msg .= rrwUtil::print_r( $fileExif, true, "$fullFile exif " );
            $sofar = "exif rread, go datetime";
            //  -------------------------- fix Database auther but no copywight
            if ( empty( $databaseCopyright ) && !empty( $databasePhotographer ) ) {
                $sqlDefault = "select copyrightDefault from $rrw_photographers
                            where photographer = '$databasePhotographer' ";
                $copyrightDefault = $wpdbExtra->get_var( $sqlDefault );
                if ( 1 == $wpdbExtra - num_recs ) {
                    $databaseCopyright = $copyrightDefault; // found it
                    $sqlUp = "update $rrw_photos 
                            set Copyright = '$copyrightDefault'
                            where photoname = '$photoname' ";
                    $wpdbExtra->query( $sqlUp );
                }
            }
            // ---------------------------- ------------------ copyright
            //  note: order matters, older jpg will accept only one attribute
            $sofar = "About to process copyright";
            if ( array_key_exists( "Copyright", $fileExif ) )
                $fileCopyRight = $fileExif[ "Copyright" ];
            else
                $fileCopyRight = "";
            if ( $debugForce )$msg .= "file: $fileCopyRight , database: $databaseCopyright $eol";
            if ( empty( $fileCopyRight ) && empty( $databaseCopyright ) ) {
                $msg .= ""; // do nothing
            } elseif ( empty( $fileCopyRight ) && !empty( $databaseCopyright ) ) {
                $msg .= rrwExif::pushToImage( $photoname, "Copyright", $databaseCopyright );
                $fileCopyRight = $databaseCopyright;
            } elseif ( !empty( $fileCopyRight ) && empty( $databaseCopyright ) ) {
                $sqlUpdate[ "copyright" ] = $fileCopyRight;
                $databaseCopyright = $fileCopyRight;
            } elseif ( $databaseCopyright != $fileCopyRight ) { // both have data
                if ( $exifCorrect ) {
                    $sqlUpdate[ "copyright" ] = $fileCopyRight;
                    $databaseCopyright = $fileCopyRight;
                } else {
                    $msg .= rrwExif::pushToImage( $photoname, "Copyright", $databaseCopyright );
                    $fileCopyRight = $databaseCopyright;
                }
            }
            //  -------------------------------------------- photographer
            $sofar = "About to process Artist ";
            $dataPotogrpherWeb = "$databasePhotographer https://pictures.shaw-weil.com/";
            if ( array_key_exists( "Artist", $fileExif ) )
                $fileArtist = $fileExif[ "Artist" ];
            else
                $fileArtist = "";
            if ( $debugForce )$msg .= "Artist file - $fileArtist, 
                    database - $databasePhotographer $eol";
            if ( empty( $fileArtist ) && empty( $databasePhotographer ) )
            ; // do nothing
            elseif ( empty( $fileArtist ) && !empty( $databasePhotographer ) ) {
                $msg .= rrwExif::pushToImage( $photoname, "Artist",
                    $dataPotogrpherWeb );
                $fileArtist = $dataPotogrpherWeb;
            }
            elseif ( !empty( $fileArtist ) && empty( $databasePhotographer ) ) {
                $sqlUpdate[ "photographer" ] = $fileArtist;
                $databasePhotographer = $fileAtrist;
            }
            elseif ( $databasePhotographer != $fileArtist ) { // both have data
                    if ( $exifCorrect ) {
                        $sqlUpdate[ "photographer" ] = $fileArtist;
                        $databasePhotographer = $fileArtist;
                    } else {
                        $msg .= rrwExif::pushToImage( $photoname, "Artist", $dataPotogrpherWeb );
                        $fileArtist = $dataPotogrpherWeb;
                    }
                } // end diff test
            $sofar = "done phtographer";
            //  --------------------------------------------- datetime
            $sofar = "About to process dateTime";
            $FileDateTime = self::getPhotoDateTime( $fileExif );
            $debugDate = false;
            if ( empty( $FileDateTime ) && empty( $datebasePhotoDate ) ) {
                if ( $debugDate )$msg .= "I#741 no dates available $eol ";
                // do nothing
            } elseif ( empty( $FileDateTime ) && !empty( $datebasePhotoDate ) ) {
                if ( $debugDate )$msg .= "I#741 no photo date,but have database $datebasePhotoDate $eol ";
                if ( strpos( $datebasePhotoDate, "1800" ) !== false ) {
                    if ( $debugDate )$msg .= "I741 a real date, pudh it to photo $eol";
                    $msg .= rrwExif::pushToImage( $photoname, "DateTimeOriginal", $datebasePhotoDate );
                }
                $FileDateTime = datebasePhotoDate;
            } elseif ( !empty( $FileDateTime ) && empty( $datebasePhotoDate ) ) {
                if ( $debugDate )$msg .= "I#741 have photo $FileDateTime,but have database date $eol ";
                $sqlUpdate[ "PhotoDate" ] = $FileDateTime;
                $datebasePhotoDate = $FileDateTime;
            } else { // both have dates
                $datePH = new Datetime( $datebasePhotoDate );
                $dateDB = new DateTime( $FileDateTime );
                $dateDiff = $datePH->diff( $dateDB );
                if ( abs( $dateDiff->days ) > 1 ) { // more than one day difference
                    if ( $debugDate )$msg .= "I#741  photo $FileDateTime, not equal database $datebasePhotoDate $eol ";
                    if ( $exifCorrect ) {
                        if ( $debugDate )$msg .= "I#741 update database $eol";
                        $sqlUpdate[ "PhotoDate" ] = $fileArtist;
                        $datebasePhotoDate = $fileArtist;
                    } else {
                        if ( $debugDate )$msg .= "I#741 update database $eol";
                        $msg .= rrwExif::pushToImage( $photoname, "DateTimeOriginal", $datebasePhotoDate );
                        $fileArtist = $datebasePhotoDate;
                    }
                } // end if (abs($dateDiff->day)
            }
            // -------------------------------------------- keywodes
            // more code needed here to get the file tags
            $sofar = "About to process keywrods";
            $debugKeywords = false;
            $keywordname = "0x9c9e";
            $keywordname = "tags";
            $keywordname = "Tags";
            $keywordname = "IPTC:Keywords";
            $keywordname = "IPTC:keywords";
            $keywordname = "XPKeywords";
            $keywordname = "Description";
            $keywordname = "description";
            $keywordname = "comment";
            $keywordname = "keyword";
            $keywordname = "keywords";
            $keywordname = "ImageDescription";
            if ( $debugKeywords )$msg .= "fileKeywords - $fileKeywords $eol";
            if ( $debugKeywords )$msg .= "databaseKeyword - $databaseKeyword $eol";
            if ( empty( $fileKeywords ) && empty( $databaseKeyword ) )
            ; // do nothing
            elseif ( empty( $fileKeywords ) && !empty( $databaseKeyword ) ) {
                $newDescription = "$databaseTrail, " .
                "photographer: $databasePhotographer," .
                " Keyword: $databaseKeyword ";
                $msg .= rrwExif::pushToImage( $photoname, $keywordname, $newDescription );
                $fileKeywords = $databaseKeyword;
            } elseif ( !empty( $fileKeywords ) && empty( $databaseKeyword ) ) {
                // removed from db  $sqlUpdate[ "photoKeyword" ] = $fileKeywords;
                $databaseKeyword = $fileKeywords;
                // update keyword table
            } elseif ( $databaseKeyword != $fileKeywords ) { // both have data
                    if ( $exifCorrect ) {
                        // $sqlUpdate[ "photoKeyword" ] = $fileKeywords;
                        $databaseKeyword = $fileKeywords;
                    } else {
                        $msg .= rrwExif::pushToImage( $photoname, "DateTimeOriginal", $databaseKeyword );
                        $fileKeywords = $databaseKeyword;
                    }
                }
                // --------------------------------- image height, image width
            $sofar = "About to process height, width";
            $imageheight = $fileExif[ "COMPUTED" ][ "Height" ];
            $imagewidth = $fileExif[ "COMPUTED" ][ "Width" ];
            if ( $databaseHeight != $imageheight )
                $sqlUpdate[ "height" ] = $imageheight;
            if ( $databaseWidth != $imagewidth )
                $sqlUpdate[ "width" ] = $imagewidth;
            //  all the checks have been done, Any dtabase to update
            if ( count( $sqlUpdate ) > 0 ) { // now update the database
                $answer = $wpdbExtra->update( $rrw_photos, $sqlUpdate,
                    array( "photoname" => $photoname ) );
                if ( $debugForce )$msg .= "had " . count( $sqlUpdate ) .
                " to be updated in $answer record $eol";
            }
            $sofar = "fixAssumeExifCorrect is done";
        } catch ( Exception $ex ) {
            $msg .= "$msg E#200 in fixAssumeExifCorrect: $sofar: " .
            $ex->getMessage() . $eol;
        }
        return $msg;
    } // end function fixAssumeExifCorrect
    public static function getPhotoDateTime( $fileExif ) {
        global $eol;
        $debugTime = false;
        try {
            // -----------------------------------------------------------  datetime
            // return the photo data in the formst YYYY-MM-DD
            // or return blank
            $debugDate = false;
            $pictureDate = "1800-01-01"; // date not present in photo
            if ( !is_array( $fileExif ) )
                return $pictureDate;
            foreach ( array( /* all times are strings  */
                    "datetimeoriginal", "DateTimeOriginal",
                    "previewdatetime", "PreviewDateTime",
                    "ModifyDate", "modifydate",
                    //       "gpstimestamp", "GPSTimeStamp", // rational64 number
                    "gpsdatestamp", "GPSDateStamp" ) as $dateKey ) {
                if ( array_key_exists( $dateKey, $fileExif ) ) {
                    $pictureDate = $fileExif[ $dateKey ];
                    break;
                }
            }
            if ( empty( $pictureDate ) )
                return "1800-02-02";
            // now get the correct format
            if ( strncmp( "16450", $pictureDate, 5 ) == 0 ) {
                if ( $debugTime ) print "pictureDate $pictureDate is unix ";
                $pictureDate = gmdate( "Y-m-d H:i", $pictureDate );
                if ( $debugTime ) print "calculate = $pictureDate";
                return $pictureDate;
            }
            if ( $debugTime ) print "pictureDate $pictureDate $eol";
            $picdate = new DateTime( $pictureDate );
            $picFormated = $picdate->format( "Y-m-d" );
            if ( $debugTime ) print "$pictureDate goes to $picFormated $eol";
            return $picFormated;
        } // end try
        catch ( Exception $ex ) {
            print $ex->getMessage() . "$errorBeg  E#673 somewhere in 
                getPhotoDateTime with a datekey of $datekey $errorEnd";
            return "1800-03-03";
        }
        return $msg;
    } // end  getPhotoDateTime function
    private static function test() {
        $msg = "trail is " . freewheeling_fixit::findTrailname( "P:/digipix//w-pa-trails/1-ahtm/river-trail/4th-av-launch-constr-s.jpg" );
        return $msg;
    }
    private static function findTrailname( $direonp ) {
        global $eol;
        global $wpdbExtra, $rrw_trails;
        $sqltrails = "select trailname, trail_short_name from $rrw_trails 
                    where not trail_short_name = ''";
        $rectrails = $wpdbExtra->get_resultsA( $sqltrails );
        $trails = array();
        $trails = array();
        foreach ( $rectrails as $rectrail ) {
            $trailname = $rectrail[ "trailname" ];
            $short = $rectrail[ "trail_short_name" ];
            $trails[ $short ] = $trailname;
        }
        foreach ( $trails as $key => $name ) {
            if ( strpos( $direonp, $key ) !== false )
                return $name;
        }
        return "";
    }
    public static function formatPhotoLink( $photoname ) {
        return "<a href='/display-one-photo?photoname=$photoname'
                                target='one' > $photoname </a>";
    }
    public static function getDatabaseList() {
        // returns an array, key = photoname, value= full path to file
        global $rrw_photos, $wpdbExtra;
        $list = array();
        $sqlall = "select highresShortname photoname, direonp,highresShortname from $rrw_photos";
        $recAlls = $wpdbExtra->get_resultsA( $sqlall );
        foreach ( $recAlls as $recAll ) {
            $photoname = $recAll[ "photoname" ];
            $direonp = $recAll[ "direonp" ];
            $highresShortname = $recAll[ "highresShortname" ];
            if ( empty( $direonp ) )
                $direonp = $highresShortname;
            $photoname = strToLower( $photoname );
            $list[ $photoname ] = strToLower( $direonp );
        }
        ksort( $list );
        return $list;
    }
    public static function getFileList( $dire ) {
        // returns an array, key = photoname, value= full path to file
        $files = array();
        $dh = opendir( $dire );
        if ( !is_resource( $dh ) )
            throw new Exception( "the directory '$dire' did not open" );
        while ( $entry = readdir( $dh ) ) {
            if ( false === $entry )
                break;
            if ( !is_file( "$dire/$entry" ) )
                continue;
            $entry = strToLower( $entry );
            $files[ $entry ] = strToLower( "$dire/$entry" );
        }
        ksort( $files );
        return $files;
    }
} // end class
?>