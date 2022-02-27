<?php

class rrwPictures_searchBox {

    // called from inside the header.php routine

    public static function rrwPicturesSearchBox() {
        global $eol;
        global $wpdbExtra, $rrw_photos, $rrw_trails, $rrw_keywords;
        $msg = "\n<!-- ================================= begin of the search box -->\n";
        //  return "hi there";
        $tablecss = true;
        include "/home/pillowan/www-shaw-weil-pictures/wp-content/plugins/roys-picture-processng/setConstants.php";
        $msg .= "<div class='rrwChangeArea' id='trailddetail' onmouseout=changed(); >";
        $msg .= "\n<form action='search.php' method='post' id='form1' name='form1' >";
        if ( $tablecss )
            $msg .= "\n<table><tr><td class='rrwHeaderTDleft' >\n";
        if ( function_exists( "rrw_getAccessID" ) )
            $ip = rrw_getAccessID();
        else
            $ip = -3;
        $msg .= "<input type='hidden' name='session' id='session' value='$ip'\n />";
        // ------------------------------------------ trail selection
        $sql = "select ' Any Trail' selection, 'Any Trail' val union 
            select distinct trailName selection, trailName val 
                from $rrw_trails order by selection";
        $msg .= rrwPictures_searchBox::pictureDisplayDropDownsql( $wpdbExtra, $rrw_photos, "trailname", $sql, "" );
        $msg .= "\n\n<span id='and_not'> and </span>\n\n";
        // ---------------------------------------  detail selection

        $sql = "select ' Any Detail' selection, 'Any Detail' val 
                union  select  'random', ' random photos (21)ccc' 
                union  select keyword, concat(keyword, ' (', count(*), ')') cnt 
                        from $rrw_keywords group by keyword ";
        $msg .= rrwPictures_searchBox::pictureDisplayDropDownsql( $wpdbExtra, $rrw_keywords, "selection", $sql, "" );
        if ( $tablecss )
            $msg .= "\n</td>\n<td class='rrwHeaderTDleft' >";
        //  a few mwnu items on the same line as seletions
        $msg .= " &nbsp; <strong>&lt;- Make a selection</strong>";
        if ( $tablecss )
            $msg .= "</td>\n<td> &nbsp; </td>\n<td class='rrwHeaderTDright' >";
        $msg .= "[ <a href='/'>Home</a> ] [ <a href='/quality/' >Quality</a> ]
                 [ <a href='/webmaster-feedback/'> Feedback</a>]";
        if ( $tablecss )
            $msg .= "</td></tr>\n</table>\n";
        $msg .= "</form>";
        $msg .= "</div> <!--  end rrwChangeArea -->";
        $msg .= self::adminNavBar();
        $msg .= "\n<!-- ================================= end of the search box -->\n";
        return $msg;
    }

    private static function adminNavBar() {
        // common routinue to putput the pictures administrators admin bar
        if ( current_user_can( 'edit_posts' ) ) {
            $exifUrl = "/wp-content/plugins/roys-picture-processng/Exif/html";
            $exifList = "https://devpictures.shaw-weil.com/wp-content/plugins/roys-picture-processng/Exif/html/TagNames/EXIF.html";
            $msg = "
            <p>[ <a href='/admin' target='admin' >admin </a>]
            [ <a href='/submission' target='admin' >submit photo </a> ]
            [ c:\_e &nbsp; php sub.php ]
            [ <a href='/upload' target='admin' >process upload </a> ]
            [ <a href='$exifUrl/' target='one' >EXIF description </a> ]
            [ <a href='$exifList/' target='one' >EXIF description </a> ]
            
            </p>\n ";
        } else
            $msg = "<!-- user can not edit posts -->\n";
        return $msg;
    }

    public static function pictureDisplayDropDownsql( $wpdbExtra, $table, $toBeUpdated, $sql, $currentvalue ) {
        global $eol;
        if ( $toBeUpdated == "starticon xxx" )
            $debugDisplayDropDownsql = true;
        else
            $debugDisplayDropDownsql = false;
        if ( $debugDisplayDropDownsql ) print "DisplayDropDownsql(db_access, 
                $table, $toBeUpdated, $sql, $currentvalue)$eol";
        $site = get_site_url();
        // Thia test enables us to test 'roys header' 'switchname'
        if ( false !== strpos( get_site_url(), "demo" ) )
            $rows = array();
        else
            $rows = $wpdbExtra->get_resultsA( $sql );
        $out = "<select name='$toBeUpdated' id='$toBeUpdated' label='$toBeUpdated'> \n";
        foreach ( $rows as $row ) {
            $rowValue = $row[ "selection" ];
            $rowDisplay = $row[ "val" ];
            $rowValue = str_replace( "&", "xxax", $rowValue );
            if ( $debugDisplayDropDownsql ) print "DisplayDropDownsql:option:
                            $rowDisplay, $rowValue $eol";
            $out .= "<option ";
            if ( strcmp( $rowValue, $currentvalue ) == 0 )
                $out .= " selected='selected' ";
            $out .= " value=\"$rowValue\"> $rowDisplay</option>\n";
        }
        $out .= "</select>";
        if ( $debugDisplayDropDownsql ) print "$eol--------------$eol " . htmlspecialchars( substr( $out, 0, 200 ) ) . $eol;
        return $out;
    } // end pictureDisplayDropDownsql

} // end class