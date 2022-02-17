<?php
class freewheeling_displayPhotos {
    static public function displayPhotos( $attr ) {
        global $eol;
        global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_keywords;
        global $photoUrl, $photoPath, $thumbUrl, $thumbPath,
        $highresUrl, $highresPath;
        $msg = "";
        $directory = rrwUtil::fetchparameterString( "directory", $attr );
        $filename = rrwUtil::fetchparameterString( "photo", $attr );
        $keyword = rrwUtil::fetchparameterString( "keyword", $attr );
        $location = rrwUtil::fetchparameterString( "location", $attr );
        $people = rrwUtil::fetchparameterString( "people", $attr );
        $where = rrwUtil::fetchparameterString( "where", $attr );
        
        $debugPotos = true;

        $msg .= SetConstants( "displayphotos" );
        $debugProgress = false;
        if ( $debugProgress )$msg .= "photos go here$eol";
        /*
                if ( array_key_exists( "selectionIs", $_GET ) )
                    $selectionIs = $_GET[ "selectionIs" ];
                else
                    $selectionIs = "";
        */
        $selectionIs = rrwUtil::fetchparameterString( "selectionIs" );
        $selectionIs = str_replace( "xxax", "&", $selectionIs );
        if ( empty( $where ) )
            $where = " where ";
        foreach ( Array(
                "location" => $location,
                "people" => $people,
                "direonp" => $directory,
                "filename" => $filename,
                "keyword" => "$keyword", ) as $field => $item ) {
            if ( !empty( $item ) )
                $where .= " $field like '%$item%' or";
        }
        $where = str_replace( "xxax", "&", $where );
        $where = str_replace( "xxbx", "'%", $where );
        $where = str_replace( "xxex", "%'", $where );
        $where = str_replace( "xxqx", "'", $where );
        $where = str_replace( "\\", "", $where );
        //   $msg .= "substr( where, -2, 2 ) )" .   substr( $where, -2, 2 ) . $eol;
        if ( "or" == substr( $where, -2, 2 ) )
            $where = substr( $where, 0, strlen( $where ) - 2 );
        $msg .= updateAccessTable("", $where);
        $sql = "select filename from $rrw_photos $where and photostatus = 'use'  ";
        if ( $debugProgress )$msg .= "$sql $eol ";
        if (" where " == $where) {
            // no selection made
            return $msg;            
        }
  //      if ($debugPotos) $msg .= "beofre random where is '$where' $eol";
        if (strpos($where, "random") !== false)
            $sql = "SELECT photo_id, filename FROM $rrw_photos
                        where photostatus = 'use' order by rand() limit 21";
        $recFiles = $wpdbExtra->get_resultsA( $sql );
        $msg .= "Selection is $selectionIs -- found " . $wpdbExtra->num_rows . " photos to display. Click photo to enlage.";
        $msg .= "
    <ul class='rrwPhotoGrid' role='list'>
        ";
        $trailermsg = "";
        foreach ( $recFiles as $recFile ) {
            $photoname = $recFile[ "filename" ];
            $filename = "{$photoname}_tmb.jpg";
            $imageTmb = "$thumbUrl/$filename";
            //   $msg .= "image file is at imageTmb $eol ";;
            $msg .= "\n<li> ";
            if ( !file_exists( "$thumbPath/$filename" ) )
                $msg .= "<a href='display-one-photo?photoname=$photoname' > 
                $photoname </a> was not found.  $eol ";
            else {
                $meta = getimagesize( "$thumbPath/$filename" );
                $onePhotoUrl = site_url( "/display-one-photo" );
                $msg .= "
                <img src='$imageTmb' alt='$filename' $meta[3]
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
}

?>
