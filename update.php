<?php 
// called by the Commit all changes button via action='update'8/11
require_once "rrw_util_inc.php";
require_once "updatedatabase.php";

class freeWheeling_DisplayUpdate {

    static public function DisplayUpdate( $attr ) {
     global $eol, $errorBeg, $errorEnd;
       global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_photographer, $rrw_keywords;
        global $photoUrl, $photoPath, $thumbUrl, $thumbPath, $highresUrl, $highresPath;
        $msg ="";
        ini_set( "display_errors", true );

        try {
            $debug = false;
            //   if ( $debug ) print rrwUtil::print_r( $_POST, true, "Post input" );
            $photoname = rrwUtil::fetchparameterString( "photoname", $attr );
            if (empty($photoname))
                throw new Exception ("$msg $errorBeg Missing parameters to update routine $errorEnd");
            $trailName = rrwUtil::fetchparameterString( "trailName", $attr );
            $photographer = rrwUtil::fetchparameterString( "photographer", $attr );
            $keywordcnt = rrwUtil::fetchparameterString( "keywordcnt", $attr );
            $commalist = rrwUtil::fetchparameterString( "commalist", $attr );
            $location = rrwUtil::fetchparameterString( "location", $attr );
            $photodate = rrwUtil::fetchparameterString( "photodate", $attr );
            $uploaddate = rrwUtil::fetchparameterString( "uploaddate", $attr );
            $comment = rrwUtil::fetchparameterString( "comment", $attr );
            $people = rrwUtil::fetchparameterString( "People", $attr );
            $direonp = rrwUtil::fetchparameterString( "direonp", $attr );

            if ( ( strlen( $location ) < 1 ) )
                if ( ( strlen( $location ) < 1 ) )
                    $location = "Unspecified";
            if ( ( strlen( $photodate ) < 1 ) )
                $photodate = "Unknown";

            $sql = "update $rrw_photos set trail_Name = '$trailName', 
                photographer = '$photographer', location = '$location',
                photodate = '$photodate', comment ='$comment',
                people ='$people', uploaddate = '$uploaddate'";
            if ( !empty( $direonp ) ) {
                $direonp = str_replace( "\\\\", "/", $direonp );
                $sql .= ", direonp = '$direonp' ";
            }
            $sql .= " where filename = '$photoname' ";
            if ( $debug ) print "$sql<br>$eol";
            $wpdbExtra->query( $sql );
            if ( $debug ) print "checking for $keywordcnt items<br>\r";
            $keywordList = "";
            $seperator = "";
            $cnt = 0;
            if ( $debug ) print rrwUtil::print_r( $_POST, true, "Post" );
            for ( $jj = 0; $jj < $keywordcnt; $jj++ ) {
                $cnt++;
                if ( $cnt > 300 )
                    break;
                $key = "keyword$jj";
                $keywordnew = rrwUtil::fetchparameterString( "$key" );
                if ( empty( $keywordnew ) )
                    continue;
                $keywordList .= "$keywordnew,";
            }
            if ( $debug ) print "keywordList: $keywordList $eol";
            if ( $debug ) print "commalist: $commalist $eol";
            $msg .= keywordEmpty( $photoname );
            $msg .= keywordCommaList( $keywordList, $photoname );
            $msg .= keywordCommaList( $commalist, $photoname );
            $msg .= keyword2photo( $photoname ); // musst be called agter updating keywords
            if ( $debug ) {
                $sqlCheck = "select * from $rrw_photos where filename ='$photoname'";
                $rec = $wpdbExtra->get_resultsA( $qlChexk );
                $msg .= rrwUtil::print_r( $rec, true, "Check redord" );
                $msg .= "<h3>Update completed</h3>
                <a href='/display-one-photo/?photoname=$photoname' >$photoname</a>";
            } else
                $msg .= freeWheeling_DisplayOne::DisplayOne( null );
        } catch ( Exception $ex ) {
            print "E#490" . $ex->getMessage();
        }
        return $msg;

    } // end DisplayUpdate
} // end class freeWheeling_DisplayUpdate
?>