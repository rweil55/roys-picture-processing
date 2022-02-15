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

            $sql = "select filename from $rrw_photos where uploaddate < '2021-03-17' ";
            $msg .= ( "<!-- sql is $sql -->\n" );
            $missngsource = $wpdbExtra->get_var( $sql );;
            $before3_17 = $wpdbExtra->num_rows;

            $sql = "select filename from $rrw_photos where uploaddate >= '2021-03-17' ";
            $msg .= ( "<!-- sql is $sql -->\n" );
            $missngsource = $wpdbExtra->get_var( $sql );;
            $after3_17 = $wpdbExtra->num_rows;

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

            $sqlMore = "select count(*) from $rrw_source where sourcestatus = 'use' and
                    (sourcefullname like '%w-pa-trails%' or
                     sourcefullname like '%ytrek%' ) and 
                    not searchname in (select filename from $rrw_photos)";
            $cntMore = $wpdbExtra->get_var( $sqlMore );

            $sqlBadCopyright = "SELECT count(*) FROM $rrw_photos
                        where not copyright like 'copyright%'
                        or copyright = '' ";
            $cntBadCopyright = $wpdbExtra->get_var( $sqlBadCopyright );

            $sqlmissingExif = "SELECT count(*) FROM $rrw_source
                        where originalexif = ''";
            $cntmissingExif = $wpdbExtra->get_var( $sqlmissingExif );
            $sqlmissingPhotog = "SELECT count(*) FROM $rrw_photos ph
                   left join $rrw_photographers pers 
                        on ph.photographer = pers.photographer
                        where pers.photographer is null or ph.photographer = ''";
            $cntmissingPhotog = $wpdbExtra->get_var( $sqlmissingPhotog );
            $sqlKeyWordcnt = "select distinct keyword from $rrw_keywords";
            $recKeywords = $wpdbExtra->get_resultsA($sqlKeyWordcnt);
            $cntKeywords = $wpdbExtra->num_rows;


            // --------------------------------------------------------- column 1
            $msg .= "<table><tr>
                    <td style='vertical-align:top'>\n
                    <strong> Information Counts </strong>$eol";
            $msg .= "$photoRowCnt photos in the photo database$eol";
            $msg .= "$photorejectCnt photos rejected/duplicates $eol";
            $msg .= "$keyWithCnt distinct photos in the keyword database$eol";
            $msg .= freewhilln_Administration_Pictures::EmptyCount( "direonp" ) .
            " with <a href='/fix?task=nosource' target='list' > no source file</a> $eol
                $before3_17 with load dates before 3/17 $eol
                $after3_17 with load dates after 3/17 $eol";
            $msg .= "$photogUsedCnt <a  href='/display-photographers/'
                    target='list' >
                    distinct photographers</a> used of $PhotogTotalCnt 
                    in the photo tables $eol";
            $msg .= "$cntMore approximately <a href='/fix?task=addlist' target='list'>
                        photos to be added</a> $eol";
            $msg .= "$cntKeywords distinct keywords$eol";
            //  -----------------------------------------------   column 2
            $msg .= "\n</td><td style='vertical-align:top'>\n
            <strong>Missing data counts</strong>$eol";
            $msg .= freewhilln_Administration_Pictures::EmptyCount( "trail_name" ) .
            " photos with <a href='/fix?task=notrailname'  target='list'>
                    no trail name</a> $eol";
            $msg .= "$cntmissingPhotog photos with <a href='/fix?task=nophotog'
             target='list' > no/incorrect Photographers</a> $eol";
            $msg .= freewhilln_Administration_Pictures::EmptyCount( "location" ) . " photos with <a href='/fix?task=nokey'  target='list'> 
                    with no Location</a> $eol";
            $msg .= freewhilln_Administration_Pictures::EmptyCount( "PhotoDate", true ) .
            " photos with <a href='/fix?task=nodate'  target='list'> 
                    no Photo date</a> $eol";
            $msg .= freewhilln_Administration_Pictures::EmptyCount( "photoKeyword" ) . " photos with <a href='/fix?task=nokey' > 
                    with no associated keyword</a> $eol";
            $msg .= "$cntBadCopyright with <a href='/fix/?task=badcopyright'
                     target='list'> incorrect copyright</a> $eol";
            $msg .= "$keyMissingCnt photos in photo table - not in keyword table $keyMissingList $eol
            $photoMissingCnt keywords in keyword table - 
                    not in photo table $photoMissingList $eol  
            $cntKeywordDup duplicate  <a href='/fix?task=keyworddups' target='list'>
                    photoName-Keywords </a> $eol
            ";
            //  -------------------------------------------  column 3
            $msg .= "</td><td style='vertical-align:top'>
            <strong>Specilized search </strong>$eol 
<form action='/displayphotos' method=POST>
<input type='text' name='photo' id='searchphoto' value='' />photo<br />
<input type='text' name='directory' id='searchdire' value='' />directory<br />
<input type='text' name='location' id='searchloc' value='' />location<br />
<input type='text' name='people' id='searchpeople' value='' />people<br />
<input type='submit' name='submit' id='submit' value='Search any' />
</form>";
            $msg .= "</td></tr></table>";
            //  -------------------------------------------  next section

            $msg .= "
To Modify/Add <a href='/display_table_trail/' target='modify'>Trail Names</a><br />
To Modify/Add <a href='/display-photographer' target='modify'>
        Phographer Names</a><br />
To Modify/Merge <a href='/fix?task=keywordform' target='modify'>Keywords</a>
";
            $today = date( "Y-m-d" );
            $msg .= "
<form action='/fix?task=bydate' method='post' >
    Display all photos uploaded between
    <input type=text name=startdate value='1/1/2021' >
    and
    <input type=text name=enddate value='$today' >
    <input type=submit value='Go Display'>
</form>
<!-- Display keywords with the photos <a href='/admin?setting=on' >On</a> &nbsp; 
<a href='/admin?setting=off' >Off</a> &nbsp;<br />-->
<a href='/notindatabase.php?doupdate=please'> 
        Add photos</a> that are not in the database
(There are $photoRowCnt in database.)<br />
<a href='notindatabase.php' target='list' >Show photos</a> list<br />
<a href='/fix?task=cleanup'  target='list'>Clean up database</a> items<br />
<br />
<form method='post' action='/fix?task=deletephoto' >
    Delete photo named
    <input type='text' name='del3' id='del3 '>
    repeat
    <input type='text' name='del2' id='del2'>
    <input type='submit' value='Delete the photo from database' >
</form>

    <form method='post' action='/fix' >
        rename photo <input type='text' name='filename' size='40' value='$photo' />
        <input type='text' name='new' size='40' value='$photo' />
        <input type='submit' value='rename dataase/files' />
        <input type='hidden' name='task' value='rename' />
        </form>
    <br />
Too upload new photos.
<ul>
    <!--    
 	<li>  A) Place big file in the c:\temp\pictures folder. </li>
    <li>  B) Run the local <a href='//127.0.0.1/shawweil/pictures/appendcopyright.pl' >append copyright routine</a>   </li> -->
    <li> A) Upload the large format photo via the <a href='/submission' >submission form </a></li>
    <li> B) Update the list of <a href='/notindatabase.php' target='list' >photos not in the database</a> </li>
    <li> C) Click the link to <a href='/search.php?submit=Photos+Without+any+keyword'
             target='list'> enter keywords, etc</a> </li>
</ul>
</div>
";
        } catch ( Exception $ex ) {
            $msg .= "E#409 administraion over all error " . $ex->getMessage();
        }
        return $msg;
    } // end function

    private static function EmptyCount( $item, $unknown = false ) {
        global $wpdbExtra, $rrw_photos;
        $sql = "select count(*) from $rrw_photos where $item = '' 
                and photostatus = 'use' ";
        if ( $unknown )
            $sql .= " or $item = 'unknown' ";
        //       print ( "<!-- sql is $sql -->\n" );
        $cnt = $wpdbExtra->get_var( $sql );;
        return $cnt;
    }
   

} //end Class 

?>
