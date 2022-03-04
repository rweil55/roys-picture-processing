<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
function gimmeFile(filename){
    
    alert (filename);
     $.get(filename, function(data, status){
    alert("Data: " + data + "\nStatus: " + status);
  });
}
</script>
<?php

//require_once "rrw_util_inc.php";
//require_once "display_tables_inc.php";

if ( class_exists( "freewheeling_fixit" ) )
    return;

class freewheeling_fixit {
    public static function fit_it( $attr ) {
        global $eol, $errorBeg, $errorEnd;
        error_reporting( E_ALL | E_STRICT );
        ini_set( "display_errors", true );
        $msg = "";

        try {
            $msg .= "<!-- before allow -->\n";
            if ( rrwUtil::notAllowedToEdit( "fix things" ) )
                throw new Exception( "$msg E#694 not allowed" );
            $msg .= "<!-- before set constatnts -->\n";
            $msg .= SetConstants( "updateDiretoryOnFileMatch" );
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
            $msg .= self::checkForLogIn( "fix task '$task' " );

            switch ( $task ) {
                case "add":
                    $msg .= freewheeling_fixit::addphotos();
                    break;
                case "addlist":
                    $msg .= freewheeling_fixit::addList();
                    break;
                case "bydate": //get collection of photos between two dates
                    $msg .= freewheeling_fixit::byDate();
                    break;
                case "badcopyright":
                    $msg .= freewheeling_fixit::badCopyright();
                    break;
                case "copymeta":
                    $msg .= freewheeling_fixit::copymeta();
                    break;
                case "copyrightfix":
                    $msg .= freewheeling_fixit::copyrightfix();
                    break;
                case "deletephoto":
                    $msg .= freewheeling_fixit::deletePhoto();
                    break;
                case "duplicatephoto":
                    $msg .= freewheeling_fixit::setUseStatus( "duplicate" );
                    break;
                case "exifmissing":
                    $msg .= self::exifMissing();
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
                case "sourceset":
                    $msg .= freewheeling_fixit::SourceSetUseFromPhotos();
                    break;
                case "sourcepush":
                    $msg .= freewheeling_fixit::sourcePush();
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
                    $msg .= testpel();
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
       <a href='/fix/?task=exifmissing' > photo with no exif data </a>$eol 
       <a href='/fix/?task=forcedatabase' > force All database to match exif </a>$eol 
       <a href='/fix/?task=forceOneExif' > force One database to match exif </a>$eol 
       <a href='/fix/?task=rename' > rename a photo </a>$eol 
   <strong>File consistancy</strong>$eol
      <a href='/fix/?task=highresmissing' > high resolution image
                                            missing </a>$eol 
      <a href='/fix/?task=photomissing' > photo information missing</a>$eol 
      <a href='/fix/?task=filesmissing' > highres missng _cr, _tmb </a>$eol 
       
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
      <a href='/fix/?task=keywordDups' > list photos with 
                                            duplicate keywords </a>$eol 
       <a href='/fix/?task=extrakeyword' > list sql to update keyword table
                                                from photo descrioin</a>$eol 
        <a href='/fix/?task=keywordForm' > merge/delete keywords </a>$eol 
     
      ";
                    return $msg;
                    break;
            }
        } catch ( Exception $ex ) {
            $msg .= "$errorBeg E#495 " . $ex->getMessage() . $errorEnd;
        }
        return $msg;
    }

    public static function checkForLogIn( $errMessage = "" ) {
        // throw error is user isnot allowed to edit
        global $eol, $errorBeg, $errorEnd;
        $msg = "";

        if ( current_user_can( 'edit_posts' ) )
            return $msg;
        $bt = debug_backtrace();
        $caller = array_shift( $bt );
        $file = $caller[ 'file' ];
        $line = $caller[ 'line' ];
        throw new Exception( "$msg $errorBeg $errMessage 
                    permission has not been granted,
                    you need to be logged in to do this.  $errorEnd" );
    }

    private static function copymeta() {
        global $highresPath;
        $msg = "";
        //        $msg .= SetConstants( "copyMeta" );


        $fileIn = "$highresPath/IMGP3723.jpg";
        $fileout = "$highresPath/IMGP3723_clean.jpg";
        $msg .= FreewheelingCommon::copyMeta( $fileIn, $fileout );
        $msg .= "did it";
        return $msg;


    }

    private static function exifMissing() {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photographers, $rrw_photos;
        global $photoPath;
        $msg = " ";

        $sqlMissingExif = "
            select filename FROM $rrw_photos where originalexif = ''
            and status = 'use'
            ";
        $recs = $wpdbExtra->get_resultsA( $sqlMissingExif );
        $cntphotos = $wpdbExtra->num_rows;
        $msg .= "
            there are $cntphotos photos in use that have no exif data $eol ";
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
        $dirlist_cr = self::getFileList( $photoPath );

        $msg .= "<strong>There is high resolution image, 
                    but no display (_cr) image </strong><br>";
        $cntFound = 0;
        foreach ( $dirlistRes as $file => $fileFull ) {
            if ( !array_key_exists( $file, $dirlist_cr ) ) {
                $msg .= "$fileFull is not in $photoPath ";
                $msg .= self::addcreate_searchlink( $file );
                $cntFound++;
            }
        }
        $msg .= "<strong>There are $cntFound isdisplay (_cr) image, 
                    but no high resolution image </strong><br>";
        foreach ( $dirlist_cr as $file => $fileFull ) {
            if ( !array_key_exists( $file, $dirlistRes ) ) {
                $msg .= "$fileFull is not in $highresPath ";
                $msg .= self::addcreate_searchlink( $file );
            }
        }
        return $msg;
    }

    private static function highresmissing() {
        global $eol;
        global $wpdbExtra, $rrw_photos;
        global $highresPath;
        $msg = "";

        $msg .= "<strong>There is meta data in the database, 
                    but no high resolution image </strong><br>";
        $dirlistRes = self::getFileList( $highresPath );
        $sqlall = "select filename from $rrw_photos";
        $recAlls = $wpdbExtra->get_resultsA( $sqlall );
        $cntMissng = 0;
        foreach ( $recAlls as $rec ) {
            $filename = $rec[ "filename" ];
            if ( !array_key_exists( "$filename.jpg", $dirlistRes ) ) {
                $msg .= "$filename.jpg is not in $highresPath,
                but we have <a href='/display-one-photo?photoname=$filename' >
                photo information </a>$eol ";
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
            $photoname = strtolower($rec[ "photoname" ]);
            $photolist[ $photoname ] = 1;
        }
        $cntFound = 0;
        foreach ( $dirlistRes as $highres => $fullfilename ) {
            $filename = str_replace( "$highresPath/", "", $highres );
            $highresTest = str_ireplace( ".jpg", "", $filename );
            $highresTest = strtolower($highresTest);
            if ( !array_key_exists( $highresTest, $photolist ) ) {
                $msg .= "<a href='/display-one-photo?photoname=$highresTest' >
                $highresTest</a> is not in the photo table,
                but we have a high resolution image ";
                $msg .= " [ " . self::updateeRenameLink( $highresTest ) . " ] ";
                $msg .= " [ " . self::addcreate_searchlink( $filename ) . " ] ";
                $cntFound++;
            }
        }
        $msg .= "Found $cntFound photos  but no photo information ";
        return $msg;
    }

    public static function BadPhotonameEndings() {
        return array( "_cr.", "_tmb.", "-s.", "-w." );
    }

    private static function addcreate_searchlink( $photoname ) {
        global $eol, $errorBeg, $errorEnd;
        $msg = "";

        $partial = "";
        //      $msg .= "$eol addcreate_searchlink( $photoname ) $eol"; 
        foreach ( self::BadPhotonameEndings() as $bad )
            if ( strpos( $photoname, $bad ) !== false ) {
                $partial = str_replace( $bad, ".", $photoname );
                break;
            }
        if ( empty( $partial ) )
            $msg .= "<a href='/fix/?task=reload&fullfilename=$photoname' 
                    target='create' > create meta</a> 
                    <strong>$photoname</strong> $eol ";
        else
            $msg .= "<a href='/fix/?task=filelike&photoname=$photoname&partial=$partial' 
                    target='create' > search for high res</a> 
                    <strong>$photoname</strong> $eol ";
        return $msg;
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
        $iislash = strrpos( $fullFilename, "/" );
        $filename = substr( $fullFilename, $iislash +1 );
        $uploadfullname = "$uploadPath/$filename";
        // move the file
        if ( $debug )$msg .= "= rename( $highresPath/$filename, $uploadfullname $eol ";
        $answer = rename( "$highresPath/$filename", $uploadfullname );
        if ( $debug )$msg .= "lets process upload dire$eol";
        $attr = array( "uploadshortname" => $filename );
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
            $errorBeg E #867 missing filename parameter $errorEnd" );
            $sqlPhoto = "select copyrightDefault from $rrw_photos ph
        join $rrw_photographers ger on ph.photographer = ger.photographer 
                    where filename ='$filename' ";
            $copyRight = $wpdbExtra->get_var( $sqlPhoto );
            if ( false === $copyRight || empty( $copyRight ) )
                throw new Exception( "$errorBeg E#865 missing Author or author copyright $errorEnd $sqlPhoto $eol " );
            $fileFull = "$photoPath/$filename" . "_cr.jpg";
            //      $msg .= pushToImage( $fileFull, "copyright", $copyRight );
            $sqlUpdate = "update $rrw_photos set copyright = '$copyRight' 
                        where filename = '$filename'";
            $cnt = $wpdbExtra->query( $sqlUpdate );
            $msg .= "update $cnt record in the photo database";
        } catch ( Exception $ex ) {
            $msg .= "E#868 top level - " . $ex->getMessage();
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
        $exif1 = rrw_exif_read_data( $file1 );
        $exif2 = rrw_exif_read_data( $file2 );
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

    public static function addsearch( $fields ) {
        global $eol;
        global $rrw_photos, $rrw_source;
        $msg = "";

        $sql = "select $fields from $rrw_source where sourcestatus = 'use' and
                    (sourcefullname like '%w-pa-trails%' or
                     sourcefullname like '%ytrek%' ) and 
                    not searchname in (select photoname from $rrw_photos)";
        return $sql;
    }
    private static function addList() {
        // display a list of field that might added
        global $eol;
        $msg = "";
        $sql = freewheeling_fixit::addsearch( "sourceFullname" ) .
        " order by searchname, sourceFullname"; // a sql to find addtional files to add
        $msg .= freewheeling_fixit::rrwFormatDisplayPhotos( $sql,
            "photos that might be uploaded", 20 );
        return $msg;
    }

    private static function deletePhoto() {

        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_source, $rrw_keywords;
        global $photoPath, $thumbPath, $highresPath, $rejectPath;
        $msg = "";

        $debug = false;

        $why = rrwUtil::fetchParameterString( "why" );
        if ( empty( $wny ) )
            $why = "because";
        $filename = rrwUtil::fetchParameterString( "del2" );
        if ( $debug )$msg .= "into delete photo $filename $eol";
        $msg .= "task = deleteing the Photo <strong>$filename</strong> $eol ";
        $del3 = rrwUtil::fetchParameterString( "del3" );
        if ( strcmp( $filename, $del3 ) != 0 ) {
            $msg .= "input parameters do not match. photo not deleted";
            return $msg;;
        }
        if ( $filename == "" ) {
            $msg .= "Missing or invalid photo name";
            return $msg;;
        }
        $sql = "select photo_id, filename from $rrw_photos 
                    where filename = '$filename'";
        if ( $debug )$msg .= "\n<!-- sql is $sql -->\n";
        $recs = $wpdbExtra->get_resultsA( $sql );
        if ( 1 != $wpdbExtra->num_rows )
            $msg .= "Sql did not find file name in the database $eol";

        $sql = "delete from $rrw_photos where Filename ='$filename'";
        $answer = $wpdbExtra->query( $sql );
        $msg .= "Deleted $answer photo record, &nbsp";

        $sql = "delete from $rrw_keywords where keywordFilename = '$filename'";
        $answer = $wpdbExtra->query( $sql );
        $msg .= "$answer keyword record, &nbsp; ";

        $sqlreject = "update $rrw_source set sourcestatus = '$why' 
                        where searchname = '$filename'";
        $answer = $wpdbExtra->query( $sqlreject );
        $msg .= "$answer source record, &nbsp; $eol ";

        $filename1 = "$thumbPath/$filename" . "_tmb.jpg";
        $filename2 = "$photoPath/$filename" . "_cr.jpg";
        $filename3 = "$highresPath/$filename.jpg";
        $ToFilename3 = "$rejectPath/$filename.jpg";

        if ( false ) {
            $msg .= "photo '$filename' deleted from database. $eol 
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
        $sql = "select filename, photographer, photostatus from $rrw_photos 
                where photographer = '$photog'
                order by photographer, filename";
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
        $sql = "select direonp, filename, photostatus from $rrw_photos where 
    '$startdate' <= $update and $update < '$enddate'"; // missng source
        print( "<!-- sql is $sql -->\n" );
        $msg .= freewheeling_fixit::rrwFormatDisplayPhotos( $sql,
            "photos uploaded between $startdate and less than $enddate" );
        return $msg;
    }
    private static function badCopyright() {
        global $eol;
        global $wpdbExtra, $rrw_photos;
        $msg = "";
        $msg = "";

        $sql = "SELECT filename, copyright, photostatus FROM $rrw_photos
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

        $sqlFind = " select filename, copyrightDefault from $rrw_photos ph
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
                        where filename = '$photoname'";
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
        $msg = "";

        $host = $_SERVER[ 'HTTP_HOST' ];
        if ( strpos( $host, "dev" ) !== false )
            $dev = "&dev=1";
        else
            $dev = "&dev=0";

        $partial = rrwPara::String( "partial", $attr );
        $photoname = rrwPara::String( "photoname", $attr );
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
                $msg .= " &nbsp; <a href='/fix/?task=rename&filename=$photoname&newname=$newphotoname' target='check' > becomes  $newphotoname </a> ";
            }
        } // end foreach check
        $msg .= "<form method='get' action='/fix/'>
        <input type='hidden' id='photoname' name='photoname' value='$photoname' />
            <input type='text' id='partial' name='partial' value='$partial' />
            <input type='hidden' id='task' name='task' value='filelike' />
            <input type='submit' value='go find' />
            </form>";
        if ( empty( $partial ) )
            return $msg;

        // lets look

        foreach ( array( "sourceFullname, searchname",
                "searchname, sourceFullname" ) as $sort ) {
            $msg .= "<hr> <strong>sorted by $sort</strong> $eol";
            $sqlFind = "
                select sourcefullname, searchname, aspect from $rrw_source 
                where sourcefullname like '%$partial%' 
                or sourceFullname like '%$partial%' 
                order by $sort ";

            $recs = $wpdbExtra->get_resultsA( $sqlFind );
            $msg .= "Found " . $wpdbExtra->num_rows . " records like '$partial' $eol";

            $msg .= "<table>$eol" . rrwFormat::HeaderRow(
                "On local machine", "aspect", "upload", "" );
            $color = rrwUtil::colorswap();
            $display = "";
            foreach ( $recs as $rec ) {
                $color = rrwUtil::colorswap( $color );

                $newphotoname = $rec[ "searchname" ];
                $ext = substr( $newphotoname, -3 );
                if ( "PCD" == $ext )
                    continue;
                $sourcefullname = $rec[ "sourcefullname" ];
                $aspect = $rec[ "aspect" ];
                $link = "<a href='/fix/?task=sourcepush&photoname=$photoname"
                    . "&sourcefullname=$sourcefullname$dev' > change dire</a> ";
                if ( strncmp( "d:", $sourcefullname, 2 ) == 0 ) {
                    $sourcefile = "d:" . substr( $sourcefullname, 2 );
                    $sourceuploadLink = "
                    <a href='http://127.0.0.1/pict/sub.php?task=pushtoupload$dev" .
                    "&sourcefile=$sourcefile&photname=$photoname'  >
                    create entry $newphotoname</a> ";
                } else {
                    $sourceuploadLink = "";
                }

                $imgFile = "http://127.0.0.1" . substr( $sourcefullname, 2 );
                $sourcefullnameDisplay = "<a href='$imgFile' target='127'>$sourcefullname</a>";
                $msg .= rrwFormat::CellRow( $color,
                    $sourcefullnameDisplay, $aspect, $sourceuploadLink, $link );
                $display .= "<div class='rrwDinoItem' >
                        <a href='$imgFile' target='one' >
                        <img src='$imgFile' width='300' />
                        $eol $sourcefullname $eol $newphotoname $eol $aspect $eol
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


    private static function listing() {
        global $eol;
        global $rrw_photos;
        $msg = "";

        $sqlWhere = rrwUtil::fetchparameterString( "where" );
        $table = rrwUtil::fetchparameterString( "table" );
        $sqlWhere = str_replace( "xxy", "'", $sqlWhere );
        $sqlWhere = htmlspecialchars_decode( $sqlWhere );
        $description = rrwUtil::fetchparameterString( "description" );
        if ( empty( $table ) ) {
            $table = "$rrw_photos";
            $fields = " photoname, trail_name, photographer, photostatus ";
        } else {
            $fields = "keywordFilename, keyword ";
        }
        //  item is  trail_name, direonp
        $sql = "select $fields from $table 
                where $sqlWhere  order by $fields "; // missng source
        $msg .= freewheeling_fixit::rrwFormatDisplayPhotos( $sql,
            "photos wih no $description" );
        return $msg;
    }

    private static function rrwFormatDisplayPhotos( $sql, $desvripton, $limit = 100 ) {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra;
        $msg = "";
        error_reporting( E_ALL | E_STRICT );

        try {
            print( "<!-- sql request is \n\n$sql\n\n -->\n" );
            $missngsource = $wpdbExtra->get_resultsA( $sql );
            $missngsourceCnt = $wpdbExtra->num_rows;
            $msg .= "<strong>There are $missngsourceCnt $desvripton</strong> $eol";
            $cnt = 0;
            if ( 0 == $missngsourceCnt )
                return $msg;
            $color = rrwUtil::colorSwap();
            $msg .= "$eol <table></tr> \n";
            foreach ( $missngsource[ 0 ] as $name => $valu ) {
                $msg .= rrwFormat::CellHeader( $name );
            }
            $msg .= "</tr>";
            $cnt = 0;
            $totalCnt = count( $missngsource );
            foreach ( $missngsource as $recset ) {
                $cnt++;
                if ( $cnt > $limit ) {
                    $remain = $totalCnt - $limit;
                    throw new Exception( "$msg $errorBeg E#498 limit of $limit reached,
                            there are $remain more $eol" );
                }
                $color = rrwUtil::colorSwap( $color );
                $msg .= "<tr style='background-color:$color;' >\n";
                foreach ( $recset as $name => $valu ) {
                    switch ( $name ) {
                        case 'filename':
                        case 'photoname':
                        case "keywordfilename":
                        case "searchname":
                            $valu = freewheeling_fixit::formatPhotoLink( $valu );
                            break;
                        case 'photographer':
                            $valu = "<a href='/fix?task=photog&photog=$valu' 
                                        target='list'  > $valu </a>";
                            break;
                        default:
                            // just plane $valu
                            break;
                    }
                    $msg .= rrwFormat::Cell( $valu );
                }
                $msg .= "</tr>";
            }
            $msg .= "</table>\n";
            //           $msg .= rrwUtil::print_r( $missngsource[ 0 ], true, "one recod" );
            //           $msg .= rrwUtil::print_r( $missngsource[ 0 ], true, "one recod" );
        } catch ( Exception $ex ) {
            $msg .= "E#401 " . $ex->getMessage() . "<p> $sql </p> ";
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
        if ( $debug )$msg .= "start updateRename( $photoname, $newname ) $eol";
        if ( empty( $newname ) && !empty( $photoname ) ) {
            $notfound = "true";
            foreach ( self::BadPhotonameEndings() as $bad ) {
                if ( strpos( $photoname, $bad ) !== false ) {
                    $newname = str_replace( $bad, ".", $photoname );
                    $notfound = false;
                    break;
                }
            }
            if ( $notfound )
                $msg .= "$errorBeg E#710 $photoname does not contain " .
            implode( ";", self::BadPhotonameEndings() ) .
            $errorEnd;
        }
        if ( empty( $newname ) && empty( $photoname ) ) {
            return self::updateRenameform();
        }
        // remove the .jpg
        $photoname = str_ireplace( ".jpg", "", $photoname );
        $newname = str_ireplace( ".jpg", "", $newname );
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
            $msg .= "$errorBeg E#420 file $newname is already in the photo table,
                    not replaced $errorEnd";
        else {
            $sqlupdate = "update $rrw_photos set photoname = '$newname'
                where photoname ='$photoname' ";
            $cnt = $wpdbExtra->query( $sqlupdate );
            $msg .= "updated $cnt records with $sqlupdate $eol";
            $sqlupdate = "update $rrw_photos 
                set filename = replace(filename, '$photoname', '$newname')
                where filename ='%$photoname%' ";
            $cnt = $wpdbExtra->query( $sqlupdate );
            $msg .= "updated $cnt records with $sqlupdate $eol";

            $sqlupdate = "update $rrw_keywords set keyword = '$newname'
                where keyword ='$photoname' ";
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
        $msg = "
<form action='/displayphotos/' method=POST>
<input type='text' name='photoname' id='searchphoto' value='' />photo<br />
<input type='text' name='directory' id='searchdire' value='' />directory<br />
<input type='text' name='location' id='searchloc' value='' />location<br />
<input type='text' name='people' id='searchpeople' value='' />people<br />
<input type='text' name='source' id='searchsource' value='' />source<br />
<input type='text' name='photographer' id='searchphotographer' value='' />photographer<br />
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
            $msg .= SetConstants( "updateDiretoryOnFileMatch" );
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
            $sqlblank = "select filename from $rrw_photos 
                where direonp = '' or direonp is null ";
            $msg .= "$sqlblank $eol ";
            $recs = $wpdbExtra->get_resultsA( $sqlblank );
            $cntrecs = $wpdbExtra->num_rows;
            $msg .= "Found $cntrecs rows of blank direonp $eol";

            $msg .= "<table> ";
            foreach ( $recs as $rec ) {
                $key = $rec[ "filename" ];
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

        $msg .= SetConstants( "updateDiretoryOnFileMatch" );
        $msg .= direReport( "direonp", "with blank DireOnP" );
        $sql = "select Filename from $rrw_photos where direonp = '' ";
        $msg .= "$sql $eol ";
        $recs = $wpdbExtra->get_resultsA( $sql );
        $cntrecs = $wpdbExtra->num_rows;
        $msg .= "Found $cntrecs photos not marched to directories of data $eol";
        $photos = array();
        foreach ( $recs as $rec )
            array_push( $photos, $rec[ "Filename" ] );
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
                            throw new Exception( "E#485 iinvalid testpas = $testPass" );
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
        $sqlEmpty = "select filename, photo_id from $rrw_photos where direonp = '' 
            and filename like '%_ms%'
            order by substring(filename,7,5) ";
        $recEmptys = $wpdbExtra->get_resultsA( $sqlEmpty );
        $msg .= "</table>\n";
        foreach ( $recEmptys as $recEmpty ) {
            $filename = $recEmpty[ "filename" ];
            $photo_id = $recEmpty[ "photo_id" ];
            if ( strpos( $filename, "_ms" ) === false ) {
                $newname = "";
            } else {
                $newname = "dale0" . substr( $filename, 9, 1 ) . "-img00" .
                substr( $filename, 11, 2 );
            }
            $c1 = "<form action='/fix?task=rename' method='post' >
                <input type='text' value='$filename' name='filename'/>";
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
        $sql = "select * from $rrw_photo where filename = '$photoname'";
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

        $bads = self::BadPhotonameEndings();
        array_push( $bads, "" );
        foreach ( $bads as $dummy => $bad1 ) {
            $bad1 = str_replace( ".", "", $bad1 );
            $sqlMatch = "select searchname from $rrw_photos
                join $rrw_source on replace(searchname, '$bad1', '') = 
                replace(photoname, '-s','') ";
            $msg .= "$sqlMatch $eol";
            $cnt = $wpdbExtra->query( $sqlMatch );
            $msg .= "there are $cnt records where searchname = photoname $eol";
            $recMatchs = $wpdbExtra->get_resultsA( $sqlMatch );
            foreach ( $recMatchs as $recMatch ) {
                $name = $recMatch[ 'searchname' ];
                $wpdbExtra->query( "update $rrw_source set sourcestatus = 'use'
                            where searchname = '$name' " );
            } //  end foreach recmatch
        } // end foreach bad
        // how did we do
        $sqlResults = "select count(*) cnt, sourcestatus from $rrw_source 
                            group by sourcestatus ";
        $recStatuss = $wpdbExtra->get_resultsA( $sqlResults );
        $msg .= "<table>" . rrwFormat::HeaderRow( "status", "Count" );
        foreach ( $recStatuss as $recStatus ) {
            $cnt = $recStatus[ 'cnt' ];
            $sourcestatus = $recStatus[ 'sourcestatus' ];
            $msg .= rrwFormat::CellRow( $sourcestatus, $cnt );
        }
        $msg .= "</table>";
        return $msg;
    } // end SourceSetUseFromPhotos

    private static function forceDatabse2matchexif() {
        global $eol, $errorBeg, $errorEnd;
        $msg = "";

        try {
            $sql = "SELECT * FROM $rrw_photos
                     order by filename limit 1 ";
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
            $photoname = $rec[ "filename" ];
            $databaseCopyright = $rec[ "copyright" ];
            $datebasePhotoDate = $rec[ "PhotoDate" ];
            $databasePhotographer = $rec[ "photographer" ];
            $databaseKeyword =
                freeWheeling_DisplayOne::GetkkeywordList( $photoname );
            $databaseHeight = $rec[ "height" ];
            $databaseWidth = $rec[ "width" ];
            $fullFile = "$photoPath/$photoname" . "_cr.jpg";

            $sqlUpdate = array();
            if ( !file_exists( $fullFile ) )
                throw new Exception( "$errorBeg E#448  file $fullFile not found $errorEnd" );
            if ( $debugForce )$msg .= "
            <a href='display-one-photo?photoname=$photoname' 
            target='one' >$photoname </a>, ";
            // --------------------------------------------- exif
            // https://exiftool.org/TagNames/EXIF.html list most tags
            try {
                $fileExif = rrw_exif_read_data( $fullFile );
            } // end try
            catch ( Exception $ex ) {
                throw new Exception( "$msg E#469 " . $ex->getMessage() . " while        reading exif of '$fullFile' in fixAssumeExifCorrect " );
            }
            if ( empty( $fileExif ) || !is_array( $fileExif ) )
                throw new Exception( "$errorBeg #854 Fetch of exif 
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
                    $sqlUpdate = "sql update $rrw_photos 
                            set Copyright = '$copyrightDefault'
                            where photoname = '$photoname' ";
                }
            }
            // ---------------------------- ------------------ copyright
            //  note: order matters, older jpg will accept only one attribute
            $sofar = "go for copyright";
            if ( array_key_exists( "Copyright", $fileExif ) )
                $fileCopyRight = $fileExif[ "Copyright" ];
            else
                $fileCopyRight = "";
            if ( $debugForce )$msg .= "file: $fileCopyRight , database: $databaseCopyright $eol";
            if ( empty( $fileCopyRight ) && empty( $databaseCopyright ) )
            ; // do nothing
            elseif ( empty( $fileCopyRight ) && !empty( $databaseCopyright ) ) {
                $msg .= pushToImage( $photoname, "copyright", $databaseCopyright );
                $fileCopyRight = $databaseCopyright;
            }
            elseif ( !empty( $fileCopyRight ) && empty( $databaseCopyright ) ) {
                $sqlUpdate[ "copyright" ] = $fileCopyRight;
                $databaseCopyright = $fileCopyRight;
            } elseif ( $databaseCopyright != $fileCopyRight ) { // both have data
                if ( $exifCorrect ) {
                    $sqlUpdate[ "copyright" ] = $fileCopyRight;
                    $databaseCopyright = $fileCopyRight;
                } else {
                    $msg .= pushToImage( $photoname, "copyright", $databaseCopyright );
                    $fileCopyRight = $databaseCopyright;
                }
            }

            //  -------------------------------------------- photographer
            if ( array_key_exists( "Artist", $fileExif ) )
                $fileArtist = $fileExif[ "Artist" ];
            else
                $fileArtist = "";
            if ( $debugForce )$msg .= "Artist file - $fileArtist, 
                    database - $databasePhotographer $eol";
            if ( empty( $fileArtist ) && empty( $databasePhotographer ) )
            ; // do nothing
            elseif ( empty( $fileArtist ) && !empty( $databasePhotographer ) ) {
                $msg .= pushToImage( $photoname, "artist", $databasePhotographer );
                $fileArtist = $databasePhotographer;
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
                        $msg .= pushToImage( $photoname, "artist", $databasePhotographer );
                        $fileArtist = $databasePhotographer;
                    }
                } // end diff test
            $sofar = "phtographer";
            //  --------------------------------------------- datetime
            $FileDateTime = self::getPhotoDateTime( $fileExif );
            if ( empty( $FileDateTime ) && empty( $datebasePhotoDate ) )
            ; // do nothing
            elseif ( empty( $FileDateTime ) && !empty( $datebasePhotoDate ) ) {
                $msg .= pushToImage( $photoname, "datetimeoriginal", $datebasePhotoDate );
                $FileDateTime = datebasePhotoDate;
            } elseif ( !empty( $FileDateTime ) && empty( $datebasePhotoDate ) ) {
                $sqlUpdate[ "PhotoDate" ] = $FileDateTime;
                $datebasePhotoDate = $FileDateTime;
            } elseif ( $datebasePhotoDate != $FileDateTime ) { // both have data
                if ( $exifCorrect ) {
                    $sqlUpdate[ "PhotoDate" ] = $fileArtist;
                    $datebasePhotoDate = $fileArtist;
                } else {
                    $msg .= pushToImage( $photoname, "datetimeoriginal", $datebasePhotoDate );
                    $fileArtist = $datebasePhotoDate;
                }
            }

            // -------------------------------------------- keywodes
            // more code needed here to get the file tags
            $sofar = "keywrods";
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
            $keywordname = "ImageDescription";
            $keywordname = "keyword";
            $keywordname = "keywords";

            //       $keywordName = "";
            if ( array_key_exists( "ImageDescription", $fileExif ) ) {
                $fileKeywords = $fileExif[ "ImageDescription" ];
                $keywordname = "ImageDescription";
            }
            if ( array_key_exists( "XPKeywords", $fileExif ) ) {
                $fileKeywords = $fileExif[ "XPKeywords" ];
                $keywordname = "XPKeywords";
            }
            if ( array_key_exists( "keyword", $fileExif ) ) {
                $fileKeywords = $fileExif[ "keyword" ];
                $keywordname = "keyword";
            }
            if ( array_key_exists( "keywords", $fileExif ) ) {
                $fileKeywords = $fileExif[ "keywords" ];
                $keywordname = "keywords";
            }
            if ( $debugKeywords )$msg .= "fileKeywords - $fileKeywords $eol";
            if ( $debugKeywords )$msg .= "databaseKeyword - $databaseKeyword $eol";
            if ( empty( $fileKeywords ) && empty( $databaseKeyword ) )
            ; // do nothing
            elseif ( empty( $fileKeywords ) && !empty( $databaseKeyword ) ) {
                $msg .= pushToImage( $photoname, $keywordname, $databaseKeyword );
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
                        $msg .= pushToImage( $photoname, "datetimeoriginal", $databaseKeyword );
                        $fileKeywords = $databaseKeyword;
                    }
                }
                // --------------------------------- image height, image width
            $sofar = "go for height, width";
            $imageheight = $fileExif[ "COMPUTED" ][ "Height" ];
            $imagewidth = $fileExif[ "COMPUTED" ][ "Width" ];
            if ( $databaseHeight != $imageheight )
                $sqlUpdate[ "height" ] = $imageheight;
            if ( $databaseWidth != $imagewidth )
                $sqlUpdate[ "width" ] = $imagewidth;

            //  all the checks have been done, Any dtabase to update
            if ( count( $sqlUpdate ) > 0 ) { // now update the database
                $answer = $wpdbExtra->update( $rrw_photos, $sqlUpdate,
                    array( "filename" => $photoname ) );
                if ( $debugForce )$msg .= "had " . count( $sqlUpdate ) .
                " to be updated in $answer record $eol";
            }
            $sofar = "fixAssumeExifCorrect is done";
        } catch ( Exception $ex ) {
            $msg .= "$msg E#440 in fixAssumeExifCorrect:$sofar: " .
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

    public static function getFileList( $dire ) {
        // returns an array, key = filename, value= full path to file
        $files = array();
        $dh = opendir( $dire );
        if ( !is_resource( $dh ) )
            throw new Exception( "the directory '$dire' did not open" );
        while ( $entry = readdir( $dh ) ) {
            if ( false === $entry )
                break;
            if ( !is_file( "$dire/$entry" ) )
                continue;
            $files[ $entry ] = "$dire/$entry";
        }
        ksort( $files );
        return $files;
    }

} // end class
?>