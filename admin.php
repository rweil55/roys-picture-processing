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
            $setdisplaykey = rrwUtil::fetchparameterString( "setdisplaykey" );
            if ( "on" == $setdisplaykey )
                $displaykey = true;
            if ( "off" == $setdisplaykey )
                $displaykey = false;
            //  ----------------------------------------------- various counts
            $sql = "select count(*) from $rrw_photos"; // total photos
            $msg .= ( "<!-- sql is $sql -->\n" );
            $photoRowCnt = $wpdbExtra->get_var( $sql );;
            $sql = "select count(*) from $rrw_photos 
                    where not photostatus = 'use' "; // total rejected photos
            $msg .= ( "<!-- sql is $sql -->\n" );
            $photorejectCnt = $wpdbExtra->get_var( $sql );;
            $sql = "select distinct keywordFilename from $rrw_keywords"; // total keyword
            $msg .= ( "<!-- sql is $sql -->\n" );
            $withKeysQuery = $wpdbExtra->get_resultsA( $sql );;
            $keyWithCnt = $wpdbExtra->num_rows;
            $sql = "select filename from $rrw_photos where direonp = '' "; // missng source
            $msg .= ( "<!-- sql is $sql -->\n" );
            $missngsource = $wpdbExtra->get_var( $sql );;
            $missngsourceCnt = $wpdbExtra->num_rows;
            $sql = "select Filename from $rrw_photos 
                        where Filename not in (
                                select keywordFilename 
                                from $rrw_keywords ) 
                                and photostatus = 'use' "; // photo not in keyword
            $msg .= ( "<!-- sql is $sql -->\n" );
            $photoNokey = $wpdbExtra->get_resultsA( $sql );;
            $keyMissingCnt = $wpdbExtra->num_rows;
            $keyMissingList = "";
            foreach ( $photoNokey as $recset ) {
                $key = $recset[ "Filename" ];
                $keyMissingList .= "<a href='/display-one-photo?photoname=$key'
                 target='one' > $key</a> &nbsp ";
            }
            // keyword not in photo
            $sql = "select keywordFilename from $rrw_keywords 
                            where keywordFilename not in (
                                select Filename from $rrw_photos ) ";
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
            $sqlKeywordDups = "select count(*), keyword from $rrw_keywords 
                group by keyword, keywordFilename having count(*) > 1";
            $recKeywordDup = $wpdbExtra->get_resultsA( $sqlKeywordDups );
            $cntKeywordDup = $wpdbExtra->num_rows;
            $sqlMore = "select count(*) from $rrw_source where sourcestatus = 'use' and ( sourcefullname like '%w-pa-trails%' or
                      sourcefullname like '%ytrek%' ) and 
                     not searchname in (select photoname from $rrw_photos)";
            $cntMore = $wpdbExtra->get_var( $sqlMore );
            $sqlBadCopyright = "SELECT count(*) FROM $rrw_photos
                        where not copyright like 'copyright%'
                        or copyright = '' ";
            $cntBadCopyright = $wpdbExtra->get_var( $sqlBadCopyright );
            $sqlmissingPhotog = "SELECT count(*) FROM $rrw_photos ph
                   left join $rrw_photographers pers 
                        on ph.photographer = pers.photographer
                        where pers.photographer is null or ph.photographer = ''";
            $cntmissingPhotog = $wpdbExtra->get_var( $sqlmissingPhotog );
            $sqlKeyWordcnt = "select distinct keyword from $rrw_keywords";
            $recKeywords = $wpdbExtra->get_resultsA( $sqlKeyWordcnt );
            $cntKeywords = $wpdbExtra->num_rows;
            // --------------------------------------------------------- column 1
            $msg .= "<table><tr>
                    <td style='vertical-align:top'>\n
                    <strong> Information Counts </strong>$eol";
            $msg .= "$photoRowCnt photos in the photo database$eol";
            $msg .= "$photorejectCnt photos rejected/duplicates $eol";
            $msg .= "$keyWithCnt distinct photos in the keyword database$eol";
            $msg .= self::EmptyCount( "direonp", "source file" );
            $msg .= self::SQLcount("with load dates before 2002-03-24", 
                             "uploaddate < '2022-03-14' " );
            $msg .= self::SQLcount("with load dates after 2022-03-14'", 
                             "uploaddate > '2022-03-14' " );
            $msg .= "$photogUsedCnt <a  href='/display-photographers/'
                    target='list' >
                    distinct photographers</a> used of $PhotogTotalCnt 
                    in the photo tables $eol";
             $sqlWhere =  " sourcestatus = '' and 
                        ( sourcefullname like '%w-pa-trails%' or
                          sourcefullname like '%ytrek%' ) and 
                     not searchname in (select photoname from $rrw_photos)";
            $sqlWhere = freewheeling_fixit::addsearch("");
            $iiWhere = strpos($sqlWhere, "where");
            $sqlWhere = substr($sqlWhere, $iiWhere + 5);
            
            $msg.= self::SQLcount("possible adds", $sqlWhere, $rrw_source,16);
            
            $msg .= "$cntKeywords distinct keywords$eol";
            //  -----------------------------------------------   column 2
            $msg .= "\n</td><td style='vertical-align:top'>\n
            <strong>Missing data counts</strong>$eol";
            $msg .= self::EmptyCount( "trail_name", "trail name" );
            $msg .= self::EmptyCount( "photographer", "Photographers" );
            $msg .= self::EmptyCount( "location", "location" );
            $msg .= self::EmptyCount( "PhotoDate", "Photo Date" );
            $msg .= self::SQLcount(" Keywords ",
                        "photoname not in " .
                    "(select distinct keywordFilename from  $rrw_keywords )");
           $msg .= self::SQLcount(" Keyword no photo ",
                        "keywordFilename not in (select distinct photoname" .
                                 " from $rrw_photos )", $rrw_keywords);
            $msg .= self::EmptyCount( "copyright", "copyright" );
            $msg .= self::EmptyCount( "DireOnP", "source directory" );
            $msg .= self::SQLcount("source like -s", 
                                   "direonp = '' and filename like '%-s' ");
            $msg .= self::EmptyCount( "height", "height" );
            $msg .= self::EmptyCount( "width", "width" );
             $msg .= self::SQLcount(" mismatched keywords ",
                        "not keywordfilename in(
                        select photoname from $rrw_photos)", $rrw_keywords);
            $msg .= "
            $cntKeywordDup duplicate  <a href='/fix?task=keyworddups' target='list'>
                    photoName-Keywords </a> $eol
            ";
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
        Phographer Names</a><br />
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
</form>"; /*
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
            $msg .= freewheeling_fixit::updateRename("","");
            $msg .= "
            <br />
    [ <a href='/fix/?task=highresmissing' > high resolution image
                                            missing </a> ]  
    [ <a href='/fix/?task=photomissing' > photo information missing</a> ]  
    [  <a href='/fix/?task=filesmissing' > highres missng _cr, _tmb </a> ]
   ";
            /*
        Add photos</a> that are not in the database
(There are $photoRowCnt in database.)<br />
<a href='notindatabase.php' target='list' >Show photos</a> list<br />
<a href='/fix?task=cleanup'  target='list'>Clean up database</a> items<br />
<br />

 
    <br />
Too upload new photos.
<ul>
    <!--    
 	<li>  A) Place big file in the c:\temp\pictures folder. </li>
    <li>  B) Run the local <a href='$httpSource/shawweil/pictures/appendcopyright.pl' >append copyright routine</a>   </li> -->
    <li> A) Upload the large format photo via the <a href='/submission' >submission form </a></li>
    <li> B) Update the list of <a href='/notindatabase.php' target='list' >photos not in the database</a> </li>
    <li> C) Click the link to <a href='/search.php?submit=Photos+Without+any+keyword'
             target='list'> enter keywords, etc</a> </li>
</ul>
*/
            $msg .= "
</div>";
        } catch ( Exception $ex ) {
            $msg .= "E#409 administraion over all error " . $ex->getMessage();
        }
        return $msg;
    } // end function
    
    private static function EmptyCount( $item, $description ) {
        $msg = "";
        $sqlWhere = "( $item = '' or $item = 'unknown' ) and photostatus = 'use' ";
        $msg .= self::SQLcount( $description, $sqlWhere );
        return $msg;
    } // end enptycount
    
    private static function SQLcount( $description, $sqlWhere, 
                                     $tablein = "", $limit = "") {
        global $wpdbExtra, $rrw_photos;
        global $eol;
        $msg = "";
        if ("" == $tablein )
            $table = $rrw_photos;
        else
            $table = $tablein;
        $startat = get_option ("sqlCount-$description", 0);
        $sql = "select count(*) from $table where $sqlWhere";
        if (! empty($limit))
            $sql .= " limit $limit";
        //       print ( "<!-- sql is $sql -->\n" );"
        $cnt = $wpdbExtra->get_var( $sql );;
        $query = str_replace( "'", "xxy", $sqlWhere );
        $msg .= "$cnt photos with no 
            <a href='/fix/?task=listing&amp;where=$query" .
        "&amp;description=$description&amp;table=$tablein&amp;limit=$limit" .
            "&amp;startat=$startat' > $description</a>";
        if ( !empty( Trim( $description ) ) )
            $msg .= $eol;
        return $msg;
    } // end SQLcount
} //end Class 
?>
