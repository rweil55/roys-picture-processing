<?php

ini_set( "display_errors", true );


class freeWheeling_DisplayOne {
    public static function DisplayOne( $attr ) {
        // display full size and thumbnail and the meta data infomaion
        ini_set( "display_errors", true );
        global $photoUrl, $photoPath, $thumbUrl, $site_url;
        global $wpdbExtra, $rrw_photos, $rrw_trails, $rrw_photographers, 
        $rrw_keywords, $rrw_access;
        global $eol;
        $msg = "";
        $debugPath = false;
         
        try {
            $photoname = rrwUtil::fetchparameterString( "photoname", $attr );
            $photoname = str_replace("_tmb", "", $photoname);
             $photoname = str_replace(".jpg", "", $photoname);
            if ( $debugPath )$msg .= "**** 1 ******displayOne:photoPath $photoPath <br/>";
            //	if thumbs is true them display thumbnail, else display fill size
           if ( $debugPath )$msg .= " DisplayOne ($photoname )   ... ";
            error_reporting( E_ALL | E_STRICT );
            $current_user = wp_get_current_user();
           if ( ! ( $current_user instanceof WP_User ) ) 
               $ser = "Guest";
            else
                $user =  $current_user->name;
 
            $ip = $_SERVER['REMOTE_ADDR'];
            
            $sqlAccess = "Insert into $rrw_access (accessphotoname, accessIP,accessuser)
                values ('$photoname', '$ip', '$user')";
            $answer = $wpdbExtra->query($sqlAccess);
            if ( "nokey" == $photoname ) {
                $sqlNoKey = "select filename from $rrw_photos  where not filename in 
                (select distinct keywordFilename from $rrw_keywords )";
                $recNokey = $wpdbExtra->get_resultsA( $sqlNoKey );
                if ( 0 == $wpdbExtra->num_rows )
                    return "There are no photos with out keywords
                    [ <a href='/fix/?task=add'>find more </a> ]
                    [ <a href='/fix/?task=direonp'>missing source </a> ]
                            ";
                $photoname = $recNokey[ 0 ][ "filename" ];
            }
            $sql = "Select * from $rrw_photos where filename = '$photoname'";
            $msg .= ( "\n<!-- sql is $sql -->\n" );
            $recset_query = $wpdbExtra->get_resultsA( $sql );
            if ( 1 != $wpdbExtra->num_rows ) {
                $msg .= FreewheelingCommon::missingImageMessage( "in display a photo, looking for image $photoname.
                    URL = " . home_url() );
                return $msg;
            }
            $recset = $recset_query[ 0 ];
            $photoname = $recset[ "filename" ];
            $photographer = $recset[ "photographer" ];
            $direonp = $recset[ "DireOnP" ];
            $trail_Name = $recset[ "trail_Name" ];
            if ( $debugPath )$msg .= "**** 5 ******displayOne:photoUrl $photoPath <br/>";
            $htmlfileref1 = "$photoUrl/{$photoname}_cr.jpg";
            $fullfilename1 = "$photoPath/{$photoname}_cr.jpg";
            if ( !file_exists( $fullfilename1 ) ) {
                $msg .= FreewheelingCommon::missingImageMessage( "in display a photo, looking for image file $fullfilename1.
                    URL = " . home_url(), $photoname );
                return $msg;

            }
            list( $w_cr, $h_cr, $type, $attr ) = getimagesize( $fullfilename1 );
            $maxHeight = 400;
            if ( $h_cr > $maxHeight )
                $displayHeight = $maxHeight;
            else
                $displayHeight = $h_cr;
            $fullfilename2 = "$thumbUrl/{$photoname}_tmb.jpg";
            list( $h_tmb, $w_tmb, $type_tmb, $attr_tmb ) = getimagesize( $fullfilename2 );
            if ( false ) {
                $msg .= "<figure class='wp-block-image size-large'><img 
                src='$htmlfileref1' class='wp-image-49'/></figure>";
            } else {
                $h_screen = rrwUtil::fetchparameterInteger( "h_screen" ) - 30;
                $w_screen = rrwUtil::fetchparameterInteger( "w_screen" );
                $aspect = $w_screen / $h_screen;
                $adjust = $h_screen / $h_cr;
                $w_desired = round( $w_cr * $adjust, 0 ) . "px";
                $msg .= "<img src='$htmlfileref1' alt='Trail Photo'
                        height='auto' width='$w_desired' id='bigImage' />$eol";
                //print "adjust = $adjust, width=$w_desired $eol";
            }
            $msg .= freeWheeling_DisplayOne::DisplayTableDataOne( $recset );
            $msg .= "
        <script type='javascript' >
        var photo =document.getElementById('bigImage');
        photo.scrollIntoView();
        </script>
        ";
            // End of display data section

            if ( !current_user_can( 'edit_posts' ) )
                return $msg;

            $msg .= "<hr><br />";
            // --------------------------------------------------- update section
            // --------------------------------------------------- update section
            // --------------------------------------------------- update section
            $msg .= "<br /><form action='/update' method='post' >";
            if ( $debugPath )$msg .= "***** 4 *****displayOne:photoPath $photoPath <br/>";

            $sql = "Select * from $rrw_photos 
                        where filename = '$photoname' ";
            $msg .= "\n<!-- photo == $sql \n-->";
            $rec_photo_query = $wpdbExtra->get_resultsA( $sql );
            if ( 1 != $wpdbExtra->num_rows )
                throw new Exception( "406 not find while looking for trail $eol 
                        $eol $sql $eol $eol" );
            $rec_photo = $rec_photo_query[ 0 ];

            $msg .= $rec_photo[ "filename" ] . " &nbsp; 
            [ <a href='/admin'>admin </a> ] [ <A href='fix?task=tag' >tag </a> ]
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
            [ <a href='/fix?task=deletephoto&del2=$photoname&del3=$photoname'>
            Delete photo ]</a> $eol";
            #listbox2( $db, $table, $field, $oldvalue, $sortField )
            $msg .= freeWheeling_DisplayOne::listbox2( $wpdbExtra, "$rrw_trails",
                "trailName", $trail_Name, "trailName" );
            $msg .= freeWheeling_DisplayOne::listbox2( $wpdbExtra, "$rrw_photographers",
                "photographer", $photographer, "photographer" );
            $msg .= "\n" . "Date:<input type='text' name='photodate' size='50' value=\"" . $rec_photo[ "PhotoDate" ] . "\">" . "\n" .
            "<br>upload darw: <input type='text' name='uploaddate' size='20' 
        value=\"" . $rec_photo[ "uploaddate" ] . "\">" . "\n" .
            "<br>Location: <input type='text' name='location' size='50' 
        value=\"" . $rec_photo[ "Location" ] . "\">" . "\n" .
            "<br>People: <input type='text' name='People' size='50' value=\"" . $rec_photo[ "People" ] . "\">" . "\n" .
            "<br>Comments:<textarea name='comment' rows='5' cols='50' >" . $rec_photo[ "Comment" ] . "</textarea>\n" .
            "<br>Source Directory: $direonp  
                [ <a href='http://127.0.0.1/pict/sub.php?direname=$direonp'
                target='submit' > new picture file</a> ] 
                [ <a href='/display-one-photo/?photoname=nokey'> 
                        No keywords</a>]
        <input type='hidden' name='photoname' id='photoname' value='$photoname' />
        <br /><input type='submit' value=\"Commit all changes\" id='submit1' name='submit1' > ";
            // --------------------------------------------------  keyword list

            $msg .= "<p>Enter additional keywords in a comma separated list or use the check boxes below. " .
            " List and checked boxes will be merged to produce final list. $eol" .
            "<input size=150 type=text name=commalist><br>";

            $cntChk = 0;
            $cntTot = 0;
            $msg .= freeWheeling_DisplayOne::keywodCheckboxes( $photoname ) . "
                <input type='hidden' name='photoname' value='$photoname' />
                <input type='submit' value=\"Commit all changes\"> &nbsp;
                <input type='reset'>
                <a href='/display-one-photo/?photoname=nokey'> 
                        No keywords</a>
            <img src='$fullfilename2' alt='small size image' />
               </form>";
            if ( file_exists( $fullfilename1 ) ) {
                $meta = exif_read_data( $fullfilename1 );
                if ( false !== $meta ) {
                    $msg .= rrwUtil::print_r( $meta, true, "EXIF data for $fullfilename1" );
                } else {
                    $msg .= "exif_read_data returned false on $eol $fullfilename1 $eol";
                }
            } else {
                $msg .= "exif_read_data could not find $eol $fullfilename1 $eol";
            }

        } catch ( Exception $ex ) {
            print "catch";
            $msg .= "E#496 DisplayOne " . $ex->getMessage();
        }
        return $msg;
    } // end function

    private static function keywodCheckboxes( $photoname ) {
        global $wpdbExtra, $rrw_keywords;
        global $eol;
        $msg = "";
        $debugKeywords = false;
        $grid = false;

        $keywordList = array(); // get list all keyword
        $sql = "select distinct keyword from $rrw_keywords order by keyword";
        $recset_query = $wpdbExtra->get_resultsA( $sql );
        $cntTot = 0;
        foreach ( $recset_query as $recset ) {
            $ctemp = $recset[ "keyword" ];
            $keywordList[ $ctemp ] = 0; // set not checked
            $cntTot++;
        }
        $sql = "select distinct keyword from $rrw_keywords
                where keywordfilename = '$photoname' "; // get checked keyword
        if ( $debugKeywords )$msg .= "Keyword search: $sql $eol";
        $cntChk = 0;
        $recset_query = $wpdbExtra->get_resultsA( $sql );
        foreach ( $recset_query as $recset ) {
            $ctemp = $recset[ "keyword" ];
            $keywordList[ $ctemp ] = 1; // set checked
            $cntChk++;
        }
        if ( $debugKeywords )$msg .= "there are $cntChk keywords checked";
        if ( $grid )
            $msg .= "\n<div class='rrwKeywordGrid'>\n";
        else
            $msg .= "\n<div class='rrwKeywordFlex'>\n";
        $jj = -1;
        foreach ( $keywordList as $key => $valu ) {
            $jj++;
            if ( $grid )
                $msg .= " <div class='rrwKeywordGridItem'>";
            else
                $msg .= " <div class='rrwKeywordFlexItem'>";
            $msg .= "<input type='checkbox' name='keyword$jj' value='$key' ";
            if ( 1 == $valu ) {
                $msg .= " checked";
            }
            $msg .= " >" . $key . "</div>\n";
        }
        $msg .= "</div> ";
        $msg .= "<input type='hidden' name='keywordcnt' value='$cntTot' />";
        return $msg;
    }

    private static function DisplayTableDataOne( $recset ) {
        global $photoUrl, $photoPath;
        global $eol;
        $msg = "";

        $photoname = $recset[ "filename" ];

        if ( is_null( $recset[ "trail_Name" ] ) ) {
            $trailDisplay = " & lt; & lt; Missing & gt; & gt;
        ";
        } else {
            $trailDisplay = htmlspecialchars( $recset[ "trail_Name" ] );
        }
        if ( is_null( $recset[ "photographer" ] ) ) {
            $photographerDisplay = " & lt; & lt; Missing & gt; & gt; ";
        } else {
            $photographerDisplay = $recset[ "photographer" ] . "</td>\n ";
        }
        $fullname = "$photoPath/$photoname" . "_cr.jpg";
        $photoSize = getimagesize( $fullname );
        if ( !is_array( $photoSize ) )
            $sizeDisplay = " not found $fullname ";
        else
            $sizeDisplay = $photoSize[ 0 ] . " x " . $photoSize[ 1 ];

        //  -------------------------------------------------- Now the display
       $msg .= " File is <a href='$photoUrl/{$photoname}_cr.jpg'>$photoname</a>
                &nbsp; &nbsp; &nbsp; &nbsp; " . $recset[ 'Comment' ] . "$eol";
        $msg .= "\n<div class='rrwOnePhoto'><table> \n ";
        $msg .= rrwFormat::CellRow( "Trail: ", $trailDisplay,
            "Photographer: ", $photographerDisplay );
        $msg .= rrwFormat::CellRow( "Location: ", $recset[ "Location" ],
            "Photo Date: ", $recset[ "PhotoDate" ] );
        $msg .= rrwFormat::CellRow( "Photo Size ", $sizeDisplay );
        $msg .= "</table>";
        $msg .= $recset[ "copyright" ] . $eol;
    
        # -------------------- keywords
        $msg .= "<strong>Existing keywords:</strong>\n";
        $words = explode( ",", $recset[ "photoKeyword" ] );
        foreach ( $words as $keyWordItem ) {
            $keyWordItem = trim( $keyWordItem );
            if ( empty( $keyWordItem ) )
                continue;
            //        $http = "/search.php?keyword=$keyWordItem";
            //       $msg .= "<a href='" . htmlspecialchars( $http ) .
            //       "'>" . $keyWordItem . ",</a>";
            $msg .= " <a onclick='onClickKeyword(\"$keyWordItem\");' >
                        $keyWordItem, </a> ";
        }
        $msg .= "$eol<strong>Identifiable People:</strong>" . $recset[ "People" ] . "
<div id='missedClassifi' onclick='openMissedClassifi(this,$photoname);'>
    if any of this infomation is incorrect or missing. Please
    <a href='/webmaster-feedback'>let us know</a>$eol";
        return $msg;

    } // end display table

    private static function listbox2( $db, $table, $field, $oldvalue, $sortField ) {

        global $wpdbExtra;
        $msg = "";
        $msg .= "\n\n<select name='$field'>
        <option value=''>&nbsp;</option>\n";
        $sql = "select $field from $table order by $sortField ";
        $recset_query = $wpdbExtra->get_resultsA( $sql );
        foreach ( $recset_query as $recset ) {
            $FieldValue = $recset[ $field ];
            $msg .= "<option value='$FieldValue'";
            if ( 0 == strcmp( $FieldValue, $oldvalue ) ) {
                $msg .= " selected ";
            }
            $msg .= "> " . str_replace( "&", "&amp;", $recset[ $sortField ] ) . "</option>" . "\n";
        }
        $msg .= "
    </select>";

        return $msg;

    }
} // ed class
?>
