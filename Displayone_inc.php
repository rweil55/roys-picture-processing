<?php

ini_set( "display_errors", true );


class freeWheeling_DisplayOne {
    function DisplayOne( $attr ) {
        // display full size and thumbnail and the meta data infomaion
        global $photoUrl, $photoPath, $thumbUrl, $site_url;
        global $photoDB, $rrw_photos;
        global $eol;
        $msg .= "";
        $debugPath = false;
        $debugKeywords = false;

        try {
            $photname = rrwUril::fetchparameterString( "photobane", $attr );
            setConstants( "DisplayOne" );
            $tableprefix = $_SESSION[ 'tableprefix' ];
            if ( $debugPath )$msg .= "**** 1 ******displayOne:photoUrlPath $photoUrlPath <br/>";
            //	if thumbs is true them display thumbnail, else display fill size
            $debug = false;
            if ( $debug )$msg .= " DisplayOne ($photoname )   ... ";
            error_reporting( E_ALL | E_STRICT );
            $sql = "Select * from $rrw_photos where filename = '$photoname'";
            $msg .= ( "\n<!-- sql is $sql -->\n" );
            $recset_query = rwsql_query( $sql, $photoDB );
            $recset = rwsql_fetch_array( $recset_query );
            if ( $debugPath )$msg .= "*** 2 *******displayOne:photoUrlPath $photoUrlPath <br/>";
            if ( empty( $recset ) ) {
                $msg .= ( "No photo available with the id of '$photoname' $eol
             <!-- $sql --> $eol" );
                return true;
            }
            $photoname = $recset[ "filename" ];
            $photographer = $recset[ "photographer" ];
            $direonp = $recset[ "DireOnP" ];
            $trail_name = $recset[ "trail_name" ];
            if ( $debugPath )$msg .= "**** 5 ******displayOne:photoUrl $photoUrlPath <br/>";

            if ( $debugPath )$msg .= "**** 3 ******displayOne:photoUrl $photoUrlPath <br/>";
            $fullfilename1 = "$photoUrl/{$photoname}_cr.jpg";
            list( $width, $height, $type, $attr ) = getimagesize( $fullfilename1 );
            $maxHeight = 400;
            if ( $height > $maxHeight )
                $displayHeight = $maxHeight;
            else
                $displayHeight - $height;
            $fullfilename2 = "$thumbUrl/{$photoname}_tmb.jpg";
            list( $h_tmb, $w_tmb, $type_tmb, $attr_tmb ) = getimagesize( $fullfilename2 );
            $msg .= "<table><tr >
        <td rowspan='2'><img src='$fullfilename1' alt='Trail Photo' 
        height='$displayHeight'
        id='bigImage' /></td>
        <td valign='top' width='$w_tmb px'>filesize = $height X $width</td>
        </tr><tr>
        <td valign='bottom'><img src='$fullfilename2' 
            alt='Thumbnail size of Trail Photo' />$eol $eol $eol</td>
            </tr></table>
            ";
            DisplayTableData( $recset );
            $msg .= "
        <script type='javascript' >
        var photo =document.getElementById('bigImage');
        photo.scrollIntoView();
        </script>
        ";
            // End of display data section

            if ( !array_key_exists( 'admin', $_SESSION ) ) {
                return true;
            }
            $msg .= "<hr><br />";
            // --------------------------------------------------- update section
            $msg .= "<br /><form action='/update.php' method='post' >";
            if ( $debugPath )$msg .= "***** 4 *****displayOne:photoUrlPath $photoUrlPath <br/>";

            $sql = "Select ph.*, trailName from ${tableprefix}photos ph, 
            ${tableprefix}trails tr " .
            " where filename = '$photoname' ";
            $msg .= "\n<!-- photo == $sql \n-->";
            $rec_photo_query = rwsql_query( $sql, $photoDB );
            $rec_photo = rwsql_fetch_array( $rec_photo_query );
            if ( $rec_photo == 0 ) {
                $msg .= "<h2>Major error did not find the photo a second time.</h2>";
                exit( 0 );
            }
            #listbox2( $db, $table, $field, $oldvalue, $sortField )
            listbox2( $photoDB, "${tableprefix}trails", "trailName", $trail_name, "trailName" );
            listbox2( $photoDB, "${tableprefix}photographers", "photographer", $photographer, "photographer" );
            $msg .= "\n" . "Date:<input type='text' name='photodate' size='50' value=\"" . $rec_photo[ "PhotoDate" ] . "\">" . "\n" .
            "<br>Location: <input type='text' name='location' size='50' 
        value=\"" . $rec_photo[ "location" ] . "\">" . "\n" .
            "<br>People: <input type='text' name='People' size='50' value=\"" . $rec_photo[ "people" ] . "\">" . "\n" .
            "<br>Comments:<textarea name='comment' rows='5' cols='50' >" . $rec_photo[ "comment" ] . "</textarea>\n" .
            "<br>Source Directory: $direonp  
        <a href='http://127.0.0.1/pict/sub.php?direname=$direonp' target='submit' >
        new picture file</a>
        <input type='hidden' name='photoname' id='photoname' value='$photoname' />
        <br /><input type='submit' value=\"Commit all changes\" id='submit1' name='submit1' > ";
            // --------------------------------------------------  keyword list

            $msg .= "<p>Enter additional keywords in a comma separated list or use the check boxes below. " .
            " List and checked boxes will be merged to produce final list. <br>" .
            "<input size=150 type=text name=commalist><br>";

            $cntChk = 0;
            $cntTot = 0;

            $sql = "select distinct keyword from ${tableprefix}keywords order by keyword";
            $recset_query = rwsql_query( $sql, $photoDB );
            $recset = rwsql_fetch_array( $recset_query );
            while ( ( !$recset == 0 ) ) {
                $ctemp = $recset[ "keyword" ];
                $keyall[ $ctemp ] = 0; // set not checked
                $recset = rwsql_fetch_array( $recset_query );
                $cntTot++;
            }
            $sql = "select distinct keyword from ${tableprefix}keywords 
                where keywordfilename = '$photoname' ";
            if ( $debugKeywords )$msg .= "Keyword search: $sql $eol";
            $recset_query = rwsql_query( $sql, $photoDB );
            $recset = rwsql_fetch_array( $recset_query );
            while ( ( !$recset == 0 ) ) {
                $ctemp = $recset[ "keyword" ];
                $keyall[ $ctemp ] = 1; // set checked
                $cntChk++;
                $recset = rwsql_fetch_array( $recset_query );
            }
            if ( $debugKeywords )$msg .= "there are $cntChk keywords checked";
            $keyWord = array_keys( $keyall ); // get all the words
            $msg .= "
    
    <ul class='rrwKeywordGrid' role='list'>
        ";
            for ( $jj = 0; $jj < count( $keyWord ); $jj++ ) {
                $ctemp = $keyWord[ $jj ];
                $msg .= " <li> <input type='checkbox' name='keyword$jj' value='$ctemp'
            width='300px' ";
                if ( 1 == $keyall[ $ctemp ] ) {
                    $msg .= " checked";
                }
                $msg .= " >" . $ctemp . "</li>\n";
            }
            $msg .= "</ul></table>\n";
            $msg .= "<tr>
  			<td>
 				<input type='hidden' name='keywordcnt' value='$cntTot' />
				<input type='hidden' name='photoname' value='$photoname' />
 				<input type='submit' value=\"Commit all changes\"> &nbsp; 
						<input type='reset' >
			</td>
  			<td>
			<a href='//pictures.shaw-weil.com/search.php?submit=Photos+Without+any+keyword' > No keywords</a>
			</td>
		</tr>
	</table>
  </form>";
            $meta = exif_read_data( $fullfilename1 );
            if ( false !== $meta ) {
                $msg .= "<pre>";
                $msg .= _r( $meta );
                $msg .= "</pre>";
            }

        } catch ( Exception $ex ) {
            $msg .= "E#496 DisplayOne";
        }
        return $msg;
    } // end function 


    function DisplayTableData( $recset ) {
        global $photoUrl, $site_url;
        global $eol;
        $msg = "";

        $photoname = $recset[ "filename" ];
        $msg .= "<TABLE cellSpacing='1' cellPadding='2' border='1'>\n";
        $msg .= "<tr><td valign='top' colspan=5>
            File is <a href='$photoUrl/{$photoname}_cr.jpg' >\n" .
        $photoname . "</a></td></tr>\n";

        if ( is_null( $recset[ "trail_name" ] ) ) {
            $trailDisplay = "&lt;&lt;Missing&gt;&gt;";
        } else {
            $trailDisplay = htmlspecialchars( $recset[ "trail_name" ] );
            if ( is_null( $recset[ "photographer" ] ) ) {
                $photographerDisplay = "&lt;&lt;Missing&gt;&gt;";
            } else {
                $photographerDisplay = $recset[ "photographer" ] . "</td>\n";
            }

        }
        $msg .= rrwFormat::CellRow( "Trail:", $trailDisplay,
            "Photographer:", $photographerDisplay );
        $msg .= rrwFormat::CellRow( "Location:", $recset[ "location" ],
            "Photo Date :", $recset[ "PhotoDate" ] );
        if ( ( strlen( $recset[ "comment" ] ) > 1 ) ) {

            $msg .= "<tr><td valign='top' colspan=5>" . $recset[ "comment" ] . "</td></tr>\n";
        }

        $msg .= "</table>";
        # --------------------  keywords
        $msg .= "<strong>Existing keywords:</strong>\n";
        $words = explode( ",", $recset[ "photoKeyword" ] );
        foreach ( $words as $keyWordItem ) {
            if ( empty( $keyWordItem ) )
                continue;
            $http = "$site_url/search.php?keyword=$keyWordItem";
            $msg .= "<a href='" . htmlspecialchars( $http ) .
            "' >" . $keyWordItem . ",</a>";
        }
        $msg .= "$eol<strong>Identifiable People:</strong>" . $recset[ "people" ] . "
    <div id='missedClassifi'  onclick='openMissedClassifi(this,$photoname);'>
    if any of thie infomation is incorrect or missing. Please let us know E#863,
    so we can fix it $eol";
        return $msg;
    } // end display table

    function listbox2( $db, $table, $field, $oldvalue, $sortField ) {
        extract( $GLOBALS );
        $msg = "";

        $msg .= "\n" . "\n" . "<select name='$field'>
    <option value=''>&nbsp;</option>\n";

        $sql = "select $field from $table order by $sortField ";
        $recset_query = rwsql_query( $sql, $photoDB );

        while ( $recset = rwsql_fetch_array( $recset_query ) ) {
            $FieldValue = $recset[ $field ];
            $msg .= "<option value='$FieldValue'";
            if ( 0 == strcmp( $FieldValue, $oldvalue ) ) {
                $msg .= " selected ";
            }
            $msg .= "> " . str_replace( "&", "&amp;", $recset[ $sortField ] ) . "</option>" . "\n";

        }
        $msg .= "</select>";

        return $msg;
    }
} // ed class 
?>
