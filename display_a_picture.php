<?php
/*
class display_a_picture {

    static public function DisplayApisture( $attr ) {
        global $gallery, $photoUrl, $eol;
        global $wpdbExtra, $rrw_photos, $rrw_keywords;
        $msg = "";
        try {
            $msg .= SetConstants( "Display a picture" );
            $debug = false;
            $debugPath = false;

            error_reporting( E_ALL | E_STRICT );
            ini_set( "display_errors", true );


            $photoname = rrwUtil::fetchparameterString( "photoname" );
            if ( empty( $photoname ) )
                throw new Exception( "E#487 No photo selected " );

            $photoname = str_replace( "_tmb.jpg", "", $photoname );
            $sql = "Select * from $rrw_photos ph where filename = '$photoname'";
            $msg .= "\n<!-- sql is $sql -->\n";
            $recset_query = $wpdbExtra->get_resultsA( $sql );
            if ( 1 != $wpdbExtra->num_rows )
                throw new Exception( "$msg E#486 got " . $wpdbExtra->num_rows .
                    "expected only one $eol $sql $eol" );
            $recset = $recset_query[ 0 ];

            if ( $debugPath )$msg .= "*** 2 *******displayOne:photoUrl $photoUrl <br/>";

            $filename = $recset[ "filename" ];
            $photoKeyword = $recset[ "photoKeyword" ];
            $photo_id = $recset[ "photo_id" ];
            if ( $debugPath )$msg .= "**** 5 ******displayOne:photoUrl $photoUrl <br/>";

            $filename_cr = "{$filename}_cr.jpg";
            $msg .= "<img src='$photoUrl/$filename_cr' alt='Trail Photo' />\n";
            $filename_tmb = "/thumbs/{$filename}_tmb.jpg";

            $msg .= "&nbsp; &nbsp; <img src='$photoUrl/$filename_tmb' 
                alt='Thumbnail size of Trail Photo' /> \n";
            $msg .= display_a_picture::DisplayTableData
                        ( $recset, $filename );

            // End of display data section
            /*      */
        } catch ( Exception $ex ) {
            $msg .= $ex->getMessage();
        }
        return $msg;
    } // end function DisplayApisture ($attr)

    private static function DisplayTableData( $recset, $filename ) {
        global $gallery, $photoUrl, $eol;
        global $wpdbExtra, $rrw_photos, $rrw_keywords;
        $msg = "";

        try {
            $msg .= SetConstants( "Display a picture" );
            $debug = false;
            $photographer = $recset[ "photographer" ];
            $traiil = $recset[ "trail_Name" ];
            $Location = $recset[ "Location" ];
            $PhotoDate = $recset[ "PhotoDate" ];
            $photo_id = $recset[ "photo_id" ];
            $People = $recset[ "People" ];
            $photoKeyword = $recset[ "photoKeyword" ];
            
            $keyword = explode(",", $photoKeyword);
            $cnt = 0;
            for ($ii = 0; $ii < count($keyword); $ii++) {
                $cnt ++; if ($cnt > 50) break;  // assert no more then 50 times
                $key = trim($keyword[$ii]);
                $keyUrl = "/photos?nohead=please&keyword=$key";
                $keyword[$ii] = "<a href='$keyUrl' >$key</a>";
            }
            $photoKeyword = implode(", ", $keyword);

            $msg .= "<TABLE cellSpacing='1' cellPadding='2' border='2'>\n";
            $msg .= "<tr><td valign='top' colspan=10>
            File is <a href='$photoUrl/$filename' >\n" .
            $filename . "</a>, $photo_id</td></tr>\n";
            $msg .= rrwFormat::CellRow( "style='border:2px'", "Trail", $traiil, "&nbsp;" ,
                "Photographer", $photographer );
            $msg .= rrwFormat::CellRow( "Location", $Location, "&nbsp;" ,
                                     "Date", $PhotoDate );
            $msg .= "\n</table>\n";
            $msg .= "<Strong>Existing Keywords:</strong> $photoKeyword $eol";
            $msg .= "<Strong>Identifiable People:</strong> $People $eol";
            $msg .= "If this information is incorrect, Please 
            <a href ='/webmaster-feedback/' >let us know</a>.";
                
            /*
            # --------------------  keywords
            $msg .= "<strong>Existing keywords:</strong>" . "\n";
            $cnt = 0;
            $photoId = $recset[ "photo_id" ];
            $sql = "select distinct keyword from ${tableprefix}keywords where photo_id = $photoId";
            $msg .= "\n<!-- sql is $sql -->\n";
            $reckey_query = rwsql_query( $sql, $photoDB );
            $reckey = rwsql_fetch_array( $reckey_query );

            while ( ( !$reckey == 0 ) ) {
                if ( ( $cnt != 0 ) ) {
                    $msg .= ", ";
                }
                $cnt = $cnt + 1;
                if ( ( $cnt > 6 ) ) {
                    $msg .= "\n";
                    $cnt = 1;
                }
                $keyWordItem = $reckey[ "keyword" ];
                $http = "//pictures.shaw-weil.com/search.php" .
                "?trailid=0&locationID=Any&submit=Keyword%2FTrail+Search&keyword=$keyWordItem";
                $msg .= "<a href='" . htmlspecialchars( $http ) .
                "' >" . $keyWordItem . "</a>";
                $reckey = rwsql_fetch_array( $reckey_query );
            }
            */
           
        } catch ( Exception $ex ) {
            $msg .= $ex->getMessage();
        }
        return $msg;

    } // rnd function
} // end class

*/