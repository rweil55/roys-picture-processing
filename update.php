<?php
// called by the Commit all changes button via action='update'8/11
class freeWheeling_DisplayUpdate {
    static public function DisplayUpdate( $attr ) {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photos, $rrw_photographers, $rrw_keywords;
        global $photoUrl, $photoPath, $thumbUrl, $thumbPath, $highresUrl, $highresPath;
        $msg = "";
        ini_set( "display_errors", true );
        try {
            $debug = false;
            //   if ( $debug ) print rrwUtil::print_r( $_POST, true, "Post input" );
            $photoname = rrwUtil::fetchparameterString( "photoname", $attr );
            if ( empty( $photoname ) )
                throw new Exception( "$msg $errorBeg Missing parameters to update routine $errorEnd" );
            $trailName = rrwUtil::fetchparameterString( "trailName", $attr );
            $photographer = rrwUtil::fetchparameterString( "photographer", $attr );
            $copyright = rrwUtil::fetchparameterString( "copyright", $attr );
            $location = rrwUtil::fetchparameterString( "location", $attr );
            $photodate = rrwUtil::fetchparameterString( "photodate", $attr );
            $uploaddate = rrwUtil::fetchparameterString( "uploaddate", $attr );
            $comment = rrwUtil::fetchparameterString( "comment", $attr );
            $people = rrwUtil::fetchparameterString( "people", $attr );
            $direonp = rrwUtil::fetchparameterString( "direonp", $attr );
            list( $msgTemp, $keyWordList ) =
                keywordHandling::fetchParameterKeywordList( $attr );
            $msg .= $msgTemp;
            if ( ( strlen( $location ) < 1 ) )
                $location = "Unspecified";
            if ( ( strlen( $photodate ) < 1 ) )
                $photodate = "Unknown";
            // no direct user input for copyright - use photographer if empty
            if ( empty( $copyright ) && !empty( $photographer ) ) {
                $sqlCopy = "select copyrightDefault from $rrw_photographers
                            where photographer = '$photographer' ";
                $copydefalt = $wpdbExtra->get_var( $sqlCopy );
                if ( 1 == $wpdbExtra->num_rows ) {
                    $sqlupdateCopy = "update $rrw_photos 
                                set copyright = '$copydefalt'
                                where photoname = '$photoname' ";
                    $cnt = $wpdbExtra->query( $sqlupdateCopy );
                    //         $msg .= "$cnt = $cnt,  $sqlupdateCopy $eol ";
                    $copyright = $copydefalt;
                }
            }
            // get old data
            $sqlOld = "select * from $rrw_photos where photoname = '$photoname'";
            $recsold = $wpdbExtra->get_resultsA( $sqlOld );
            if ( 1 < $wpdbExtra->num_rows )
                $msg .= "$errorBeg E#858 caution more than one photos 
                            database. $errorEnd";
            if ( 0 == $wpdbExtra->num_rows )
                throw new Exception( "$errorBeg no photo record found,
                        can not update $errorEnd $sqlOld $eol" );
            $recOld = $recsold[ 0 ];
            // $msg .= rrwUtil::print_r($recOld, true, "Old records");
            $msg .= self::compare( "copyright", $copyright, $recOld );
            $msg .= self::compare( "trail_name", $trailName, $recOld );
            $msg .= self::compare( "photographer", $photographer, $recOld );
            $msg .= self::compare( "location", $location, $recOld );
            $msg .= self::compare( "comment", $comment, $recOld );
            $msg .= self::compare( "people", $people, $recOld );
            $msg .= self::compare( "comment", $comment, $recOld );
            $msg .= self::compare( "DireOnP", $direonp, $recOld );
            $msg .= self::compare( "PhotoDate", $photodate, $recOld );
            // updat the keyword list
            $msg .= keywordHandling::remove( $photoname );
            $msg .= keywordHandling::insertList( $photoname, $keyWordList );
            //        $msg .= "trying user comment $eol";
    //         $msg .= pushToImage( $photoname, "XPComment", "host machine" );
    //        $msg .= "tryidUserComment $eol";
            $sqlCheck = "select * from $rrw_photos where photoname ='$photoname'";
            $rec = $wpdbExtra->get_resultsA( $sqlCheck );
            $msg .= freewheeling_fixit::fixAssumeDatabaseCorrect( $rec[ 0 ] );
            if ( $debug ) {
                $msg .= rrwUtil::print_r( $rec, true, "Check record" );
                $msg .= "<h3>Update completed</h3>
                <a href='/display-one-photo/?photoname=$photoname' >$photoname</a>";
            }
            $msg .= freeWheeling_DisplayOne::DisplayOne( null );
        } catch ( Exception $ex ) {
            $msg .= "E#490" . $ex->getMessage();
        }
        return $msg;
    } // end DisplayUpdate
    public static function compare( $itemName, $newValue, $rec ) {
        //compare old and new value, update if different
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photos, $rrw_photographers, $rrw_keywords;
        $msg = "";
        try {
            ini_set( "display_errors", true );
            error_reporting( E_ALL | E_STRICT );
            if ( "trail_namexxx" == $itemName )
                $dubigCompare = true;
            else
                $dubigCompare = false;
            if ( $dubigCompare )$msg .= " compare( $itemName, $newValue -> ";
            $oldValue = $rec[ $itemName ];
            if ( $dubigCompare )$msg .= " $oldValue $eol";
            if ( $newValue == $oldValue )
                return $msg; // no update neeeded
            $photoname = $rec[ "photoname" ];
            $update = array( $itemName => $newValue );
            $key = array( "photoname" => $photoname );
            $cnt = $wpdbExtra->update( $rrw_photos, $update, $key );
            $hisCom = "$itemName -- $oldValue=> $newValue";
            $msg .= rrwUtil::InsertIntoHistory( $photoname, $hisCom );
            // rrw_photo has one field updated, deal with the related.
            if ( "copyright" == $itemName ) {
                $msg .= pushToImage( $photoname, "Copyright", $newValue );
            } elseif ( "photodate" == $itemName ) {
                $msg .= "$errorBeg E#701 date inside photo not updated $errorEnd";
            } elseif ( "photographer" == $itemName ) {
                $sqlCopy = "select copyrightDefault from $rrw_photographers
                where photographer = '$newValue' ";
                $newcopyright = $wpdbExtra->get_var( $sqlCopy );
                $rec[ "copyright" ] = "forces update";
                $msg .= self::compare( "copyright", $newcopyright, $rec );
            } else
                $msg .= ""; // no specail shove to image
        } // end try
        catch ( Exception $ex ) {
            $msg .= $ex->getMessage() . "$errorBeg  E#469 update:compare:itenname $itemName, newvalue $newValue  $errorEnd";
        }
        return $msg;
    } // end   function compare
} // end class freeWheeling_DisplayUpdate
?>