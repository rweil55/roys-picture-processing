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
            $msg .= SetConstants( "updateDiretoryOnFileMatch" );
            $task = rrwUtil::fetchparameterString( "task" );
            switch ( $task ) { // those tasks which do not reqire a line in
                case "jsondate":
                    $msg .= freewheeling_fixit::jsonDate();
                    return $msg;
                case "listing":
                    $msg .= freewheeling_fixit::listing();
                    return $msg;
                default:
                    break;
                case "exif":
                    $msg .= self::displayExif( $attr );
                    return $msg;
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
                case "close":
                    $msg .= freewheeling_fixit::direonpOnFileMatch();
                    $msg .= freewheeling_fixit::updateDiretoryCloseFileMatch();
                    break;
                case "deletephoto":
                    $msg .= freewheeling_fixit::deletePhoto();
                    break;
                case "direonp":
                    $msg .= freewheeling_fixit::direonpOnFileMatch();
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
                case "extrakeyword":
                    $msg .= freewheeling_fixit::extraKeyword();
                    break;
                case "filelike":
                    $msg .= freewheeling_fixit::filelike();
                    break;
                case "filesmissing":
                    $msg .= self::filesmissing();
                    break;
                case "jsondate":
                    $msg .= freewheeling_fixit::jsonDate();
                    break;
                case "jsonreject":
                    $msg .= freewheeling_fixit::jsonReject();
                    break;
                case "keyworddups":
                    $msg .= freewheeling_fixit::keywordDups();
                    break;
                case "keywordform":
                    $msg .= freewheeling_fixit::keywordForm();
                    break;
                case "nocopyright":
                    $msg .= freewheeling_fixit::noCopyright();
                    break;
                case "notbike":
                    $msg .= freewheeling_fixit::setUseStatus( "notbike" );
                    break;
                case "photog":
                    $msg .= freewheeling_fixit::DisplayPhotogaphers( $attr );
                    break;
                case "rename":
                    $msg .= freewheeling_fixit::updateRename( "", "", "" );
                    break;
                case "rejectphoto":
                    $msg .= freewheeling_fixit::setUseStatus( "reject" );
                    break;
                case "tag":
                    $msg .= freewheeling_fixit::tag();
                    break;
                case "removecreditline":
                    $msg .= freewheeling_fixit::removeCreditline();
                    break;
                case "test":
                    $msg .= freewheeling_fixit::test();
                    break;
                case "wipedireonp":
                    $msg .= freewheeling_fixit::WipeDireOnP();
                    break;
                default:
                    $msg .= "#498 unkown task of'$task' $eol Try bydate,cleanup,
copyright,filelike, jsondate, mosource, rename, removecreditline --
future direonp, close, keyword, meta
copyright, keywords, meta, rename $eol";
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
        foreach ( $dirlistRes as $file => $fileFull ) {
            if ( !array_key_exists( $file, $dirlist_cr ) )
                $msg .= "$fileFull is not in $photoPath $eol ";
        }
        foreach ( $dirlist_cr as $file => $fileFull ) {
            if ( !array_key_exists( $file, $dirlistRes ) )
                $msg .= "$fileFull is not in $highresPath $eol ";
        }
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
            $msg .= pushToImage( $fileFull, "copyright", $copyRight );
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
        $msg = "";
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
            $sql = "SELECT sourceFullName, searchname from $rrw_source 
                    where not searchname in 
                        (select filename from $rrw_photos) 
                        and sourcestatus = 'use' and sourceFullName like '%$shortname%'
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
                $direonp = $recPhoto[ "sourceFullName" ];
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
                [ <a href='fix?task=exif'> copyright</a> $eol";

        foreach ( $sqllist as $sql ) {
            $cnt = $wpdbExtra->query( $sql );
            $msg .= "$cnt &nbsp; $sql $eol";
        }
        return $msg;
    }
    public static function addsearch( $fields ) {
        global $eol;
        global $rrw_photos, $rrw_source;
        $msg = "";

        $sql = "select $fields from $rrw_source where sourcestatus = 'use' and
                    (sourcefullname like '%w-pa-trails%' or
                     sourcefullname like '%ytrek%' ) and 
                    not searchname in (select filename from $rrw_photos)";
        return $sql;
    }
    private static function addList() {
        // display a list of field that might added
        global $eol;
        $msg = "";
        $sql = freewheeling_fixit::addsearch( " searchname, sourcefullname" ) .
        " order by sourcefullname, searchname"; // a sql to find addtional files to add
        $msg .= freewheeling_fixit::rrwFormatDisplayPhotos( $sql,
            "photos that might be uploaded", 20 );
        return $msg;
    }

    private static function deletePhoto() {

        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_keywords;
        global $photoPath, $thumbPath, $highresPath, $rejectPath;
        $msg = "";

        $debug = false;

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

        $sqlreject = "update $rrw_source set sourcestatus = 'reject' 
                        where searchname = '$filename'";
        $answer = $wpdbExtra->query( $sqlreject );
        $msg .= "$answer source record, &nbsp; ";

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
            $msg .= "Update $cnt keywords in keyword table from $old to $new $eol ";
            $sqlChange2 = " update $rrw_photos 
                set photoKeyword = replace (photoKeyword, '$old', '$new') 
                where photoKeyword like '%$old%' ";
            $msg .= "$sqlChange2 $eol ";
            $cnt2 = $wpdbExtra->query( $sqlChange2 );
            $msg .= "Update $cnt2 keywords in photo table from $old to $new $eol ";
        }
        return $msg;
    }

    private static function listPhotog() {
        global $rrw_photos;
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
        $enddate = rrwUtil::fetchparameterString( "enddate" );
        $sql = "select direonp, filename, photostatus from $rrw_photos where 
    '$startdate' <= uploaddate and uploaddate < '$enddate'"; // missng source
        print( "<!-- sql is $sql -->\n" );
        $msg .= freewheeling_fixit::rrwFormatDisplayPhotos( $sql,
            "photos uploaded between $startdate and $enddate" );
        return $msg;
    }
    private static function badCopyright() {
        global $eol;
        global $rrw_photos;
        $msg = "";

        $sql = "SELECT filename, copyright, photostatus FROM $rrw_photos
                        where not copyright like 'copyright%' ";
        $msg .= freewheeling_fixit::rrwFormatDisplayPhotos( $sql,
            "photos copyright does not start with 'copyright'" );
        $msg .= "Run <a href='/fix/?task=exif' target='admin'>update something</a> $eol";
        return $msg;

    }

    private static function fileLike() {
        global $eol;
        global $photoDB, $rrw_source;
        $msg = "";

        $partfile = rrwUtil::fetchparameterString( "partfile" );
        $sql = "select sourcefullName direonp, searchname filename, photostatus
                from $rrw_source
                where searchname like '%$partfile%'
                 order by searchname, sourcefullname "; // missng source
        $msg .= ( "<!-- sql is $sql -->\n" );
        $msg .= freewheeling_fixit::rrwFormatDisplayPhotos( $sql,
            "source with file name like '$partfile'" );
        return $msg;
    }


    private static function listing() {
        global $eol;
        global $rrw_photos;
        $msg = "";
        $sqlWhere = rrwUtil::fetchparameterString( "where" );
        $sqlWhere = str_replace( "xxy", "'", $sqlWhere );
        $description = rrwUtil::fetchparameterString( "description" );
        //  item is  trail_name, direonp
        $sql = "select filename, trail_name, photographer, photostatus, 
                        photokeyword
                from $rrw_photos 
                where $sqlWhere  order by filename "; // missng source
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

    private static function tag() {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photos;
        $msg = "";
        /*
                $sql = "select filename, trail_name, photographer
                    from $rrw_photos where photoKeyword = '' ";
                $recs = $wpdbExtra->get_resultsA( $sql );
                if ( 0 == $wpdbExtra->num_rows )
                    return "No photos with out keywords";
                $photoname = $recs[ 0 ][ "filename" ];
                $_GET[ "photoname" ] = $photoname;
            */
        $_POST[ "photoname" ] = "nokey";
        $msg = freeWheeling_DisplayOne::DisplayOne( null );
        return $msg;
    }

    private static function updateRename( $filename, $newname, $sourceFullName ) {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_keywords;
        global $photoPath, $thumbPath, $highresPath;
        $msg = "";

        $debug = true;
        if ( $debug )$msg .= "updateRename( $filename, $newname, $sourceFullName ) ";

        if ( empty( $filename ) ) {
            $filename = trim( rrwUtil::fetchparameterString( "filename" ) );
            $newname = trim( rrwUtil::fetchparameterString( "new" ) );
            $sourceFullName = trim( rrwUtil::fetchparameterString( "source" ) );
        }
        if ( empty( $sourceFullName && "dale" == substr( $newname, 0, 4 ) ) ) {
            $sourceFullName = "P:/film-scan/" . substr( $newname, 0, 6 ) . "/IMAGES/$newname.jpg";
        }
        if ( empty( $newname ) ) {
            $msg .= "<form >
        <input type='text' name='filename' diabled value='$filename' />
        <input type='text' name='new' value='$filename' />
        <input type='submit' value='rename dataase/files' />
        <input type='hidden' name='task' value='rename' />
        </form>";
            return $msg;
        } //  ---------------------------------------------- move three files
        $msg .= freewheeling_fixit::checkAndRename( "$photoPath/${filename}_cr.jpg",
            "$photoPath/{$newname}_cr.jpg" );
        $msg .= freewheeling_fixit::checkAndRename( "$thumbPath/{$filename}_tmb.jpg",
            "$thumbPath/{$newname}_tmb.jpg" );
        $msg .= freewheeling_fixit::checkAndRename( "$highresPath/{$filename}.jpg",
            "$<strong>thumbPath</strong>/{$newname}.jpg" );
        //  ------------------------------------------------- things in rrw_photos
        $sqlExist = "select filename from $rrw_photos where filename = '$newname'";
        $recExists = $wpdbExtra->get_resultsA( $sqlExist );
        if ( 0 != $wpdbExtra->num_rows )
            $msg .= "$errorBeg E#420 file $newname is already in the photo table,
                    not replaced $errorEnd";
        else {
            $sqlupdate = "update $rrw_photos set filename = '$newname',
                direOnP = '$sourceFullName' where filename ='$filename' ";
            $rec = $wpdbExtra->query( $sqlupdate );
            $msg .= "$sqlupdate $eol";
        } // ------------------------------------------------  direonp
        $sqldireonp = "update $rrw_photos set direonp =
                replace(direonp, '$filename', '$newname') where filename = '$newname'";
        $rec = $wpdbExtra->query( $sqldireonp );
        /// --------------------------------------------------- cause to be fetched
        $sqlDate = "update $rrw_photos set uploaddate = '1990-01-01' 
                    where filename = '$newname'";
        $msg .= "$sqlDate $eol";
        $rec = $wpdbExtra->query( $sqlDate );
        // --------------------------------------------------------- keywords
        $sqlupdate = "update $rrw_keywords set keywordfilename = '$newname'
                        where keywordfilename ='$filename' ";
        $rec = $wpdbExtra->query( $sqlupdate );
        $msg .= "$sqlupdate $eol";
        $msg .= " [ <a href='/display-one-photo?photoname=$newname' 
                    target='one'>$newname</a> ]
        [ <a href='http://127.0.0.1/pict/sub.php?direname=' target='submit' >
                upload picture file </a> ] ";
        return $msg;

    } // end  updateRename( $filename, $newname, $sourceFullName ) {

    private static function checkAndRename( $fileFrom, $fileTo ) {
        global $eol;
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

    private static function direonpOnFileMatch() {
        // used to relate the entries in photo datta database to the file location on spoke
        // if file name matchs then update the diterctory field
        global $eol;
        global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_photographers;
        $msg = "";
        $debug = false;
        try {
            $msg .= SetConstants( "updateDiretoryOnFileMatch" );
            $msg .= direReport( "direonp", "with blank DireOnP" );
            //      return $msg;
            $sql = "select direonp, sourcefullname, searchname from $rrw_photos 
            left join $rrw_source on filename=searchname 
                where searchname is not null and not direonp = sourcefullname 
                    and sourcestatus ='use' order by searchname";
            if ( $debug );
            $msg .= "$sql $eol";
            $recs = $wpdbExtra->get_resultsA( $sql );
            $cntrecs = $wpdbExtra->num_rows;
            $msg .= "Found $cntrecs rows of data - 
                    probably duplicate sourcefilename entries $eol";
            $cntInsert = 0;
            $sqlList = array(); // list of the flippers
            foreach ( $recs as $rec ) {
                $cntInsert++;
                if ( $cntInsert > 500 )
                    break;
                $direonp = $rec[ "direonp" ];
                $sourcefullname = $rec[ "sourcefullname" ];
                $searchname = $rec[ "searchname" ];
                $sqlins = "update $rrw_photos set direonp = '$sourcefullname'
                    where filename = '$searchname' ";
                $updateCnt = $wpdbExtra->query( $sqlins );
                if ( $debug )$msg .= "$updateCnt -- $sqlins $eol ";
                $sqlList[ $searchname ] = $sourcefullname;

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
            $msg .= "E3494 in direonpOnFileMatch $sourcefullname" . $ex->getMessage();
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
        $sqlSource = "select sourceFullName, searchName from $rrw_source 
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
            $searchName = $recPC[ "searchName" ];
            $sourceFullName = $recPC[ "sourceFullName" ];

            for ( $ii = 0; $ii < count( $photos ); $ii++ ) {
                if ( strpos( $photos[ $ii ], $searchName ) !== false ) {
                    // got a match update databse rename files
                    switch ( $testPass ) {
                        case 1:
                            $filename = $photos[ "$ii" ];
                            $pattern = '/\d*_*(.+)_\d*/';
                            $newname = preg_replace( $pattern, '${1}', $filename );
                            break;
                        case 2:
                            $filename = $photos[ "$ii" ];
                            $newname = $searchName;
                            break;
                        case 3:
                            $filename = $photos[ "$ii" ];
                            $pattern = '/\d*_*(.+)_\d*/';
                            $newname = preg_replace( $pattern, '${1}', $filename );
                            $newname = $searchName;
                            break;
                        default:
                            throw new Exception( "E#485 iinvalid testpas = $testPass" );
                    }
                    if ( $newname != $searchName ) {
                        $match = "** adjust ***";
                    } else
                        $match = "";
                    $color = rrwUtil::colorSwap( $color );
                    $msg .= rrwFormat::CellRow( $color, $sourceFullName, $filename,
                        $searchName, $newname, $match );
                    if ( $newname != $searchName )
                        continue;

                    $msg .= updateRename( $filename, $newname, $sourceFullName );
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
                $msg .= Do_forceDatabse2matchexif( $rec );
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

    public static function Do_forceDatabse2matchexif( $rec ) {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_keywords,
        $rrw_photographers;
        global $photoPath, $thumbPath, $highresPath;
        $msg = "";
        $debugForce = false;
        try {
            $photoname = $rec[ "filename" ];
            $databaseCopyright = $rec[ "copyright" ];
            $datebasePhotoDate = $rec[ "PhotoDate" ];
            $databasePhotographer = $rec[ "photographer" ];
            $databaseKeyword = $rec[ "photoKeyword" ];
            $databaseHeight = $rec[ "height" ];
            $databaseWidth = $rec[ "width" ];
            $fullFile = "$photoPath/$photoname" . "_cr.jpg";
            $sqlUpdate = array();
            if ( !file_exists( $fullFile ) )
                throw new Exception( "$errorBeg E#448  file $fullFile not found $errorEnd" );
            $msg .= "<a href='display-one-photo?photoname=$photoname' target='one'          >$photoname </a>, ";
            // --------------------------------------------- exif
            // https://exiftool.org/TagNames/EXIF.html list most tags
            $fileExif = rrw_exif_read_data( $fullFile );
            if ( empty( $fileExif ) || !is_array( $fileExif ) )
                throw new Exception( "$errorBeg #854 Fetch of exif 
                                from $fullFile failed $errorEnd" );
            if ( $debugForce )$msg .= rrwUtil::print_r( $fileExif, true, "$fullFile exif " );
            //  --------------------------------------------- datetime
            $FileDateTime = self::getPhotoDateTime( $fileExif );
            if ( empty( $FileDateTime ) && empty( $datebasePhotoDate ) )
            ; // do nothing
            elseif ( empty( $FileDateTime ) && !empty( $datebasePhotoDate ) )
                $msg .= pushToImage( $photoname, "datetimedigitized", $datebasePhotoDate );
            elseif ( empty( $FileDateTime ) && !empty( $datebasePhotoDate ) )
                $sqlUpdate[ "PhotoDate" ] = $FileDateTime;
            else { // both have data 
                if ( $datebasePhotoDate != $FileDateTime )
                    $sqlUpdate[ "PhotoDate" ] = $FileDateTime;
            }
            // ---------------------------- ------------------ copyright
            if ( array_key_exists( "Copyright", $fileExif ) )
                $fileCopyRight = $fileExif[ "Copyright" ];
            else
                $fileCopyRight = "";
            if ( empty( $databaseCopyright ) && !empty( $databasePhotographer ) ) {
                $sqlDefault = "select copyrightDefault from $rrw_photographers
                            where photographer = 'databasePhotographer' ";
                $databaseCopyright = $wpdbExtra->get_var( $sqlDefault );
                $sqlUpdate[ "Copyright" ] = $databaseCopyright;
            }
            if ( $fileCopyRight != $databaseCopyright )
                $sqlUpdate[ "Copyright" ] = $fileCopyRight;

            // --------------------------------- image height, image width
            $imageheight = $fileExif[ "COMPUTED" ][ "Height" ];
            $imagewidth = $fileExif[ "COMPUTED" ][ "Width" ];
            if ( $databaseHeight != $imageheight )
                $sqlUpdate[ "height" ] = $imageheight;
            if ( $databaseWidth != $imagewidth )
                $sqlUpdate[ "width" ] = $imagewidth;
            // -------------------------------------------- keywodes
            // more code needed here to get the file tags
            if ( array_key_exists( "XPKeywords", $fileExif ) )
                $fileKeywords = $fileExif[ "XPKeywords" ];
            else
                $fileKeywords = "";
            if ( empty( $fileKeywords ) && empty( $databaseKeyword ) )
            ; // do nothing
            elseif ( empty( $fileKeywords ) && !empty( $databaseKeyword ) )
                $msg = pushToImage( $photoname, "XPKeywords", $databaseKeyword );
            elseif ( !empty( $fileKeywords ) && empty( $databaseKeyword ) ) {
                $sqlUpdate[ "photoKeyword" ] = $fileKeywords;
                // update keyword table
            } else { // both have data 
                if ( $databaseKeyword != $fileKeywords ) {
                    $sqlUpdate[ "photoKeyword" ] = $fileKeywords;
                    // update keyword table
                }
            }

            //  -------------------------------------------- photographer
            //  exif has no place for photographer

            if ( count( $sqlUpdate ) > 0 ) { // now update the database
                $answer = $wpdbExtra->update( $rrw_photos, $sqlUpdate,
                    array( "filename" => $photoname ) );
                if ( $debugForce )$msg .= "had " . count( $sqlUpdate ) .
                " to be updated in $answer record $eol";
            }
        } catch ( Exception $ex ) {
            $msg .= "$msg E#440 in exif " . $ex->getMessage();
        }
        return $msg;
    } // end function exif

    private static function getPhotoDateTime( $fileExif ) {
        global $eol;

        $debugTime = false;
        // -----------------------------------------------------------  datetime
        // return the photo data in the formst YYYY-MM-DD
        // or return blank
        $pictureDate = ""; // date not present in photo
        if ( !is_array( $fileExif ) )
            return $pictureDate;
        foreach ( array( /* all times are strings  */
                "datetimeoriginal", "DateTimeOriginal",
                "datetimedigitized",
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
            return $pictureDate;
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
        // TimeZoneOffset
    } // end  getPhotoDateTime function

    private static function WipeDireOnP() {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photos;
        $msg = "";
        $photoname = rrwUtil::fetchparameterString( "photoname" );
        if ( Empty( $photoname ) )
            return "$msg $errorBeg E#468 no spphotonamee specified ";
        $sql = "update $rrw_photos set direonp = '' 
                    where filename ='$photoname'";
        $cnt = $wpdbExtra->query( $sql );
        $msg .= "$cnt &nbsp; $sql $eol";
        return $msg;
    }

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
        sort( $files );
        return $files;
    }

} // end class
?>