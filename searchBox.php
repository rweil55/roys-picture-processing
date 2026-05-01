<?php
/*		Freewheeling Easy Mapping Application
 *		A collection of routines for display of trail maps and amenities
 *		copyright Roy R Weil 2019 - https://royweil.com
 */
require_once "setConstants.php";
class rrwPictures_searchBox
{
    // called from inside the header.php routine
    public static function rrwPicturesSearchBox()
    {
        global $eol;
        global $wpdbExtra, $rrw_photos, $rrw_trails, $rrw_keywords;
        global $rrw_portrait, $rrw_landscape;
        $msg = "\n<!-- ================================= begin of the search box -->\n";
        //  return "hi there";
        $tablecss = true;
        $msg .= SetConstants("search box");
        $msg .= "<div class='rrwChangeArea' id='trailddetail' >";
        $msg .= "\n<form action='search.php' method='post' id='form1' name='form1' >";
        if ($tablecss)
            $msg .= "\n<table><tr><td class='rrwHeaderTDleft rrwChangeArea' >\n";
        if (function_exists("rrw_getAccessID"))
            $ip = rrw_getAccessID();
        else
            $ip = -3;
        $msg .= "<input type='hidden' name='session' id='session' value='$ip'\n />";
        // ------------------------------------------ trail selection
        $currentTrail = rrwParam::string("trail", []);
        $sql = "select '%' selection, 'Any Trail' val
        union
            select distinct trailName selection, trailName val
                from $rrw_trails order by selection";
        $msg .= rrwPictures_searchBox::pictureDisplayDropDownOther($wpdbExtra, $rrw_photos, "trailname", $sql, $currentTrail);
        $msg .= "\n\n<span id='and_not'> and </span>\n\n";
        // ---------------------------------------  detail selection
        $currentDetail = rrwParam::string("searchdropdown", []);
        $sql = "select count(*) from $rrw_portrait";
        $portraintCount = $wpdbExtra->get_var($sql);
        $sql = "select count(*) from $rrw_landscape";
        $landscapeCount = $wpdbExtra->get_var($sql);
        $sql = "select '%' selection, 'Any Detail' val
                union  select  'random_21', ' random photos (21)'
                union  select  'landscape', 'landscape format ($landscapeCount)'
                union  select  'portrait', 'portrait format ($portraintCount)'
                union  select keyword, concat(keyword, ' (', count(*), ')') cnt
                        from $rrw_keywords group by keyword ";
        $msg .= rrwPictures_searchBox::pictureDisplayDropDownOther($wpdbExtra, $rrw_keywords, "selection", $sql, $currentDetail);
        if ($tablecss)
            $msg .= "\n</td>\n<td class='rrwHeaderTDleft rrwChangeArea' >";
        //  a few mwnu items on the same line as seletions
        $msg .= "</form><span style='float:left;display: flex;'>&nbsp; <strong>&lt;- Make a selection</strong></span>";
        if ($tablecss)
            $msg .= "</td>\n<td class='rrwChangeArea' > &nbsp; </td>\n<td class='rrwHeaderTDright rrwChangeArea' >";
        $msg .= "[ <a href='/'>Home</a> ] [ <a href='/quality/' >Quality</a> ]
                 [ <a href='/privacy/'> Privacy</a>]
                 [ <a href='/webmaster-feedback/'> Feedback</a>]";
        if (rrwUtil::AllowedToEdit("search", "", false)) {
            $hostUrl = $_SERVER['HTTP_HOST'];
            $msg .= "</td><td rrwChangeArea>
    <form action='https://$hostUrl/displayphotos/' method='POST'
    height='13px' >
<input type='text' name='photoname' id='searchphoto' value='' height='13px' />
</form>";
        }
        if ($tablecss)
            $msg .= "</td></tr>\n</table>\n";
        $msg .= "</form>";
        $msg .= "</div> <!--  end rrwChangeArea -->";
        $msg .= self::adminNavBar();
        $msg .= "\n<!-- ================================= end of the search box -->\n";
        return $msg;
    }
    private static function adminNavBar()
    {
        $httpHost = "https://" . $_SERVER['HTTP_HOST'];
        // common routinue to putput the pictures administrators admin bar

        //     if ( freewheeling_fixit::allowedSubmit( ) ) {
        if (rrwUtil::AllowedToEdit()) {

            $exifUrl = "$httpHost/wp-content/plugins/roys-picture-processing/Exif/html";
            $exifList = "$httpHost/wp-content/plugins/roys-picture-processing/Exif/html/TagNames/EXIF.html";
            $msg = "
            <p> [ <a href='$httpHost/admin' target='admin' >admin </a>]
            [ <a href='$httpHost/submission' target='admin' >submit photo </a> ]
            [ <a href='$httpHost/upload' target='admin' >process upload </a> ]
            [ <a href='$exifUrl' target='one' >EXIF description </a> ]
            [ <a href='$exifList' target='one' >EXIF description </a> ]
            [ <a href='$httpHost/display-one-photo/?photoname=nokey' taarget='one' >no key words </a> ]

            </p>\n ";
        } else
            $msg = "<!-- user can not edit posts -->\n";
        return $msg;
    }
    public static function pictureDisplayDropDownOther($wpdbExtra, $table, $toBeUpdated, $sql, $currentValue)
    {
        global $eol;
        if ($toBeUpdated == "starticon ")
            $debugDisplayDropDownsql = true;
        else
            $debugDisplayDropDownsql = false;
        if ($debugDisplayDropDownsql) print "picture Display Drop Down Other(db_access,
                $table, $toBeUpdated, $sql, $currentValue)$eol";
        $site = get_site_url();
        // Thia test enables us to test 'roys header' 'switchname'
        if (false !== strpos(get_site_url(), "demo"))
            $rows = array();
        else
            $rows = $wpdbExtra->get_resultsA($sql);
        $out = "<select class='rrwChangeArea'name='$toBeUpdated' id='$toBeUpdated' label='$toBeUpdated'  onchange='changed();'> \n";
        foreach ($rows as $row) {
            $rowValue = $row["selection"];
            $rowDisplay = $row["val"];
            $rowValue = str_replace("&", "xxax", $rowValue);
            if ($debugDisplayDropDownsql) print "DisplayDropDownsql:option:
                            $rowDisplay, $rowValue $eol";
            $out .= "<option ";
            if (strcmp($rowValue, $currentValue) == 0)
                $out .= " selected='selected' ";
            $out .= " value=\"$rowValue\"> $rowDisplay</option>\n";
        }
        $out .= "</select>";
        if ($debugDisplayDropDownsql) print "$eol--------------$eol " .
            htmlspecialchars(substr($out, 0, 200)) . $eol;
        return $out;
    } // end pictureDisplayDropDownOther
} // end class
