<?php
class freewheeling_displayPhotos {
    static public function displayPhotos( $attr = "" ) {
        global $eol;
        global $wpdbExtra, $rrw_photos;
        global $photoUrl, $photoPath, $thumbUrl, $thumbPath,
        $highresUrl, $highresPath;
        $msg = "";

        $debugProgress = false;

        $msg .= SetConstants( "displayphotos" );
        if ( $debugProgress )$msg .= "photos go here$eol";
        /*
                if ( array_key_exists( "selectionIs", $_GET ) )
                    $selectionIs = $_GET[ "selectionIs" ];
                else
                    $selectionIs = "";
        */
        $sqlFind = self::buildSql( $attr );
        if ( empty( $sqlFind ) )
            return "$eol $msg - No Selection made $eol ";
        $recFiles = $wpdbExtra->get_resultsA( $sqlFind );
        $msg .= " -- found " . $wpdbExtra->num_rows . " photos to display. Click photo to enlage. ";
        if ( $debugProgress )$msg .= "$eol $sqlFind $eol";
        $msg .= "
    <ul class='rrwPhotoGrid' role='list'>
        ";
        $trailermsg = "";
        foreach ( $recFiles as $recFile ) {
            $photoname = $recFile[ "photoname" ];
            $thumbname = "{$photoname}_tmb.jpg";
            $imageTmb = "$thumbUrl/$thumbname";
            //   $msg .= "image file is at imageTmb $eol ";;
            $msg .= "\n<li> ";
            if ( !file_exists( "$thumbPath/$thumbname" ) )
                $msg .= "<!-- <a href='display-one-photo?photoname=$photoname' > 
                $photoname </a> was not found.  $eol -->";
            else {
                $meta = getimagesize( "$thumbPath/$thumbname" );
                $onePhotoUrl = site_url( "/display-one-photo" );
                $msg .= "
                <img src='$imageTmb' alt='thumb nail image' $meta[3]
                onclick='openOnePhotopage(\"$photoname\")' />";
                if ( current_user_can( "edit_posts" ) )
                    $msg .= "<br /><span class='rrwCaption'> $photoname</span>";
            }
            $msg .= "</li>";
        }
        //onmouseover='this.width=800'  onmouseout='this.width=150' 
        //           onmouseclick='openOnePhotopage(\"$onePhotoUrl\")'
        //         <a href = '$onePhotoUrl?nohead=please&photoname=$filename'> 
        $msg .= "\n</ul>$eol";
        if ( $wpdbExtra->num_rows > 8 ) {
            $msg .= "
            <script >
              ifram = document.getElementById('photohere');
              ifram.scrollIntoView();
        </script>
            ";
        }
        return $msg;
    }

    private static function buildSql( $attr ) {
        global $rrw_keywords, $rrw_photos;
        global $eol;

        $debug = false;
        $msg = "";

        if ( $debug ) print "query " . $_SERVER[ 'QUERY_STRING' ] . $eol;
        if ( $debug ) print rrwUtil::print_r( $_POST, true, "_POST" );
        $searchdropdown = rrwPara::String( "searchdropdown", $attr );
        if ( $debug ) print "found searchdropdown of $searchdropdown $eol";
        if ( strpos( $searchdropdown, "random" ) !== false ) {
            $sql = "SELECT photoname FROM $rrw_photos
                        where photostatus = 'use' order by rand() limit 21";
            return $sql;
        }
        // not a request for random images
        if ( !empty( $searchdropdown ) ) {
            $sqlSearch = "select keywordfilename photoname from $rrw_keywords 
                where keyword = '$searchdropdown' ";
            if ( !empty( $trail ) ) {
                $sqlSearch .= " and trail = '$trail' ";
            }
            if ( $debug ) print " passing  sqlSearch $eol";
            return $sqlSearch;
        }
        $trail = trim( rrwPara::String( "trail", $attr ) );
        if ( $debug ) print "trail = $trail $eol";
        if ( "Any Trail" == $trail )
            $trail = "";

        $directory = rrwUtil::fetchparameterString( "directory", $attr );
        $photoname = rrwUtil::fetchparameterString( "photoname", $attr );
        $keyword = rrwUtil::fetchparameterString( "keyword", $attr );
        $location = rrwUtil::fetchparameterString( "location", $attr );
        $people = rrwUtil::fetchparameterString( "people", $attr );
        $photographer = rrwUtil::fetchparameterString( "photographer", $attr );
        // not a request for from dropdown select
        // must be from "search page"
        $where = ""; // build a complicated or search
        if ( $debug ) print "trail = $trail $eol";
        foreach ( Array(
                "trail_name" => $trail,
                "location" => $location,
                "people" => $people,
                "photographer" => $photographer,
                "direonp" => $directory,
                "photoname" => $photoname,
            ) as $field => $item ) {
            if ( !empty( $item ) )
                $where .= " $field like '%$item%' or";
        }
        if ( $debug ) print "Where = $where $eol";
        if ( "or" == substr( $where, -2, 2 ) )
            $where = substr( $where, 0, strlen( $where ) - 2 );
        $sql = "select photoname from $rrw_photos 
                where $where 
                and photostatus = 'use'  ";
        if ( $debug ) print "$sql $eol ";
        if ( empty( $where ) )
            return ""; // no searchitems found
        if ( $debug ) print "Final sql = $sql $eol ";
        return $sql;
    } // function buildSql()
} // end class

?>
