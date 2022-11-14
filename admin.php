<?php
ini_set( "Display_errors", true );
class_alias( "freewhilln_Administration_Pictures", "admin" );
class freewhilln_Administration_Pictures {
    static public function user_can_update_picsxx( $photoname ) {
        // more code to come to selectively allow edits
        if ( current_user_can( "edit_posts" ) )
            return true;
        else
            return false;
    }
    static public function administrationPicures( $attr ) {
        global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_keywords, $rrw_trails;
        global $rrw_photographers;
        global $eol;
        global $photoUrl, $photoPath, $thumbUrl, $thumbPath, $highresUrl, $highresPath;
        global $displaykey;
        global $httpSource;
        ini_set( "display_errors", true );
        $msg = "";
        try {
            $loadDate = '2022-03-10';
            $setdisplaykey = rrwUtil::fetchparameterString( "setdisplaykey" );
            if ( "on" == $setdisplaykey )
                $displaykey = true;
            if ( "off" == $setdisplaykey )
                $displaykey = false;
            //  ----------------------------------------------- various counts
            $sql = "select count(*) from $rrw_photos"; // total photos
            $msg .= ( "<!-- sql is $sql -->\n" );
            $photoRowCnt = $wpdbExtra->get_var( $sql );;
            $sql = "select distinct keywordFilename from $rrw_keywords"; // total keyword
            $msg .= ( "<!-- sql is $sql -->\n" );
            $withKeysQuery = $wpdbExtra->get_resultsA( $sql );;
            $keyWithCnt = $wpdbExtra->num_rows;
            $sql = "select photoname from $rrw_photos 
                        where photoname not in (
                                select keywordFilename 
                                from $rrw_keywords ) 
                                and photostatus = 'use' "; // photo not in keyword
            $msg .= ( "<!-- sql is $sql -->\n" );
            $photoNokey = $wpdbExtra->get_resultsA( $sql );;
            $keyMissingCnt = $wpdbExtra->num_rows;
            $keyMissingList = "";
            foreach ( $photoNokey as $recset ) {
                $key = $recset[ "photoname" ];
                $keyMissingList .= "<a href='/display-one-photo?photoname=$key'
                 target='one' > $key</a> &nbsp ";
            }
            // keyword not in photo
            $sql = "select keywordFilename from $rrw_keywords 
                            where keywordFilename not in (
                                select photoname from $rrw_photos ) ";
            $msg .= ( "<!-- sql is $sql -->\n" );
            $KeyNoPhoto = $wpdbExtra->get_resultsA( $sql );;
            $photoMissingCnt = $wpdbExtra->num_rows;
            $photoMissingList = "";
            $photo = ""; // later used in the rename form
            foreach ( $KeyNoPhoto as $recset ) {
                $photo = $recset[ "keywordFilename" ];
                $photoMissingList .= "<a href='/display-one-photo?photoname=$photo'  target='one' >$photo</a>  &nbsp; ";
            }
            $sqlPhotog = "select count(*) from 
                    (select distinct photographer from $rrw_photos ) ph1 ";
            $msg .= "<!-- $sqlPhotog \n-->";
            $photogUsedCnt = $wpdbExtra->get_var( $sqlPhotog );
            $sqlAllPhotog = "select count(*) from $rrw_photographers";
            $PhotogTotalCnt = $wpdbExtra->get_var( $sqlAllPhotog );
            $sqlKeyWordcnt = "select distinct keyword from $rrw_keywords";
            $recKeywords = $wpdbExtra->get_resultsA( $sqlKeyWordcnt );
            $cntKeywords = $wpdbExtra->num_rows;
            // --------------------------------------------------------- column 1
            $msg .= "<table><tr>
                    <td style='vertical-align:top'>\n";
            if ( rrwUtil::AllowedToEdit( "see complete administratin display",
                    "", false ) )
                $msg .= self::ColumnOne( $loadDate );

            //  -----------------------------------------------   column 2
            $msg .= "\n</td><td style='vertical-align:top'>\n
            <strong>Missing data counts</strong>$eol";
            $msg .= self::EmptyCount( "trail_name", "trail name" );
            $msg .= self::EmptyCount( "photographer", "Photographers" );
            $msg .= self::EmptyCount( "location", "location" );
            $msg .= self::EmptyCount( "PhotoDate", "Photo Date" );
            $msg .= self::SQLcount( " Keywords ",
                "photoname not in " .
                "  (select distinct keywordFilename from  $rrw_keywords )" );
            $msg .= self::EmptyCount( "copyright", "copyright" );
            $msg .= self::EmptyCount( "DireOnP", "source directory" );
            $msg .= self::SQLcount( "~load before $loadDate and like -s",
                "uploaddate < '$loadDate' and photoname like '%-s' " );
            $msg .= self::cntMissingImage( false ) . " database entry missing the image$eol";
            $msg .= self::EmptyCount( "height", "height" );
            $msg .= self::EmptyCount( "width", "width" );
            $msg .= self::SQLcount( "~keywords not on phototable",
                "not keywordfilename in(" .
                " select photoname from $rrw_photos)", $rrw_keywords );
            $msg .= self::SQLcount( "~keywords pairs duplicated",
                " 1 = 1", "(select keywordFilename from $rrw_keywords group by keyword, keywordFilename having count(*) > 1) xx " );
            /*          $msg .= self::SQLcount ("~parnoramas not label paramanrama",
                                         " height / width < .3 and not photoname in " .
                                         " (select keywordFilename from $rrw_keywords " .
                                                 " where keyword = 'panorama' )");*/
            //  -------------------------------------------  column 3
            $msg .= "</td><td style='vertical-align:top'>
            <strong>Specilized search </strong>$eol " .
            freewheeling_fixit::searchform();
            $msg .= "</td></tr></table>";
            //  -------------------------------------------  next section
            // trails photographers, keyword, display between
            $msg .= "
To Modify/Add <a href='/display-trail/' target='modify'>Trail Names</a><br />
To Modify/Add <a href='/display-photographer' target='modify'>
        Phographer Names</a><br />";
            if ( rrwUtil::AllowedToEdit( "see complete administratin display",
                    "", false ) ) {
                $msg .= "
To Modify/Merge <a href='/fix?task=keywordform' target='modify'>Keywords</a>
";
                $today = date( "Y-m-d" );
                $msg .= "
<form action='/fix?task=bydate' method='post' >
    Display all photos uploaded between
    <input type=text name=startdate value='1/1/2022' >
    and
    <input type=text name=enddate value='$today' >
    <input type=submit value='Go Display'>
</form>";
                /*
Display keywords with the photos <a href='/admin?setting=on' >On</a> &nbsp; 
<a href='/admin?setting=off' >Off</a> &nbsp;<br />
<a href='/notindatabase.php?doupdate=please'> 
*/
                $msg .= "
<form method='post' action='/fix?task=deletephoto' >
    Delete photo named
    <input type='text' name='del3' id='del3 '>
    repeat
    <input type='text' name='del2' id='del2'>
    <input type='radio' name='why' id='why' value='duplicate'>duplicte
    <input type='radio' name='why' id+'why' value='reject'>reject 
    <input type='submit' value='Delete the photo from database' >
</form>";
                $msg .= freewheeling_fixit::updateRename( "", "" );
                $msg .= "
            <br />
    [ <a href='/fix/?task=highresmissing' > high resolution image
                                            missing </a> ]  
    [ <a href='/fix/?task=photomissing' > photo information missing</a> ]  
    [  <a href='/fix/?task=filesmissing' > highres missng _cr, _tmb </a> ]
   ";
            }
            $msg .= "
</div>";
        } catch ( Exception $ex ) {
            $msg .= "E#409 administraion over all error " . $ex->getMessage();
        }
        return $msg;
    } // end function

    private static function ColumnOne( $loadDate ) {
        global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_keywords, $rrw_trails;
        global $eol;
        $msg = "";
        $msg .= " <strong> Information Counts </strong>$eol";
        $msg .= self::SQLcount( "~ accessable photos ",
            " photostatus = 'use' " );
        $msg .= self::SQLcount( "~ photos in the photo database", "1=1" );
        $msg .= self::SQLcount( "~ photos rejected/duplicates",
            "not photostatus = 'use'" );
        $msg .= self::SQLcount( "~ distinct keywords in the keyword database",
            "from (select distinct keywordFilename from $rrw_keywords) xx", "none" );
        $msg .= self::EmptyCount( "direonp", "source file" );
        $msg .= self::SQLcount( "~with load dates before $loadDate",
            "uploaddate < '$loadDate' " );
        $msg .= self::SQLcount( "~with load dates after $loadDate'",
            "uploaddate >= '$loadDate' " );
        $msg .= self::SQLcount( "~distinct photographers",
            " from (select distinct photographer from $rrw_photos) xx", "none" ); 
        $sqlWhere = " sourcestatus = '' and 
                        ( sourcefullname like '%w-pa-trails%' or
                          sourcefullname like '%ytrek%' ) and 
                     not searchname in (select photoname from $rrw_photos)";
        $sqlWhere = freewheeling_fixit::addsearch( "" );
        $iiWhere = strpos( $sqlWhere, "where" );
        $sqlWhere = substr( $sqlWhere, $iiWhere + 5 );

        $msg .= self::SQLcount( "possible adds", $sqlWhere, $rrw_source, 16 );

        $msg .= self::SQLcount( "~distinct keywords",
            " from (select distinct keyword from $rrw_keywords) xx", "none" );
        return $msg;
    } // end ColumnOne

    public static function cntMissingImage( $showNames ) {
        global $wpdbExtra, $rrw_photos, $photoPath;
        global $eol;
        $msg = "";

        $image = array();
        foreach ( new DirectoryIterator( $photoPath ) as $entry ) {
            $filename = $entry->getfilename();
            $image[ $filename ] = 1;
        }
        //     print rrwUtil::print_r($image, true, "image found");

        $sqlData = "select photoname from $rrw_photos ";
        $recPhotos = $wpdbExtra->get_resultsA( $sqlData );
        $numMissing = 0;
        foreach ( $recPhotos as $recPhoto ) {
            $photoname = $recPhoto[ "photoname" ];
            $filename = "{$photoname}_cr.jpg";
            if ( !array_key_exists( $filename, $image ) ) {
                $numMissing++;
                if ( $showNames )
                    $msg .= "missing image for 
                    <a href='/display-one-photo?photoname=$photoname'
                            targt= 'display'> $photoname </a>$eol";
            }
        }
        $msg .= "$numMissing";
        return $msg;
    }

    private static function EmptyCount( $item, $description ) {
        $msg = "";
        $sqlWhere = "( $item = '' or $item = 'unknown' ) and photostatus = 'use' ";
        $msg .= self::SQLcount( $description, $sqlWhere );
        return $msg;
    } // end enptycount

    private static function SQLcount( $description, $sqlWhere,
        $tablein = "", $limit = "" ) {
        global $wpdbExtra, $rrw_photos;
        global $eol;
        $msg = "";
        $debug = false;

        switch ( $tablein ) {
            case "":
                $table = " from $rrw_photos where ";
                break;
            case "none":
                $table = " ";
                break;
            default:
                $table = " from $tablein where ";
                break;
        }
        $startat = get_option( "sqlCount-$description", 0 );
        $sql = "select count(*) $table $sqlWhere";
        if ( !empty( $limit ) )
            $sql .= " limit $limit";
        if ( $debug ) print( "<-- sql is $sql -->$eol" );
        $cnt = $wpdbExtra->get_var( $sql );
        $query = str_replace( "'", "xxy", $sqlWhere );
        if ( "~" == substr( $description, 0, 1 ) ) {
            $description = substr( $description, 1 );
        } else {
            $description = " no $description";
        }
        $msg .= "$cnt photos with 
            <a href='/fix/?task=listing&amp;where=$query" .
        "&amp;description=$description&amp;table=$tablein&amp;limit=$limit" .
        "&amp;startat=$startat' > $description</a>";
        if ( !empty( Trim( $description ) ) )
            $msg .= $eol;
        return $msg;
    } // end SQLcount
} //end Class 
?>
