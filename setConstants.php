<?php
/*		Freewheeling Easy Mapping Application
 *		A collection of routines for display of trail maps and amenities
 *		copyright Roy R Weil 2019 - https://royweil.com
 */

function SetConstants($whocalled)
{
    global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_keywords, $rrw_trails;
    global $rrw_photographer, $rrw_photographers, $rrw_access, $rrw_history;
    global $rrw_portrait, $rrw_landscape;
    global $eol, $errorBeg, $errorEnd;
    global $photoUrl, $photoPath, $thumbUrl, $thumbPath, $highresUrl, $highresPath;
    global $rejectUrl, $rejectPath;
    global $uploadPath;
    //global $gallery;
    global $picrureCookieName;
    global $httpSource;
    global $rrwProcessPhotosConstants;
    global $numberPhotosMax;

    error_reporting(E_ALL);
    ini_set("display_errors", true);
    $msg = "";
    try {
        $eol = "<br />\n";
        $errorBeg = "<span style='color:red;' >";
        $errorEnd = "</span>$eol";
        $cap_submit = "submit_photo";
        $httpSource = "http://128.2.184.148";
        $httpSource = "http://127.0.0.1";
        $numberPhotosMax = 750; // maximun in database used to prevent runaway
        $picrureCookieName = "picture_visit_ip_time";
        // database names
        if (!is_object($wpdbExtra))
            $wpdbExtra = new wpdbExtra;
        $rrw_history = $wpdbExtra->prefix . "1rrw_history";
        $rrw_portrait = $wpdbExtra->prefix . "7rrw_portrait";
        $rrw_landscape = $wpdbExtra->prefix . "7rrw_landscape";
        $tablePrefix = $wpdbExtra->prefix . "0";
        $rrw_access = $tablePrefix . "access";
        $rrw_photos = $tablePrefix . "photos";
        $rrw_source = $tablePrefix . "source";
        $rrw_keywords = $tablePrefix . "keywords";
        $rrw_photographers = $tablePrefix . "photographers";
        $rrw_trails = $tablePrefix . "trails";
        // urls
        $siteUrl = get_site_url();
        $photoUrl = "$siteUrl/wp-content/photos";
        $thumbUrl = "$photoUrl/thumbs";
        $highresUrl = "$photoUrl/high_resolution";
        $rejectUrl = "$photoUrl/reject";
        // directories
        // ABSPATH  = "/home/pillowan/www-shaw-weil-pictures/";
        $photoPath = ABSPATH . "wp-content/photos";
        $thumbPath = "$photoPath/thumbs";
        $highresPath = "$photoPath/high_resolution";
        $uploadPath = "$photoPath/upload";
        $rejectPath = "$photoPath/reject";
    } catch (Exception $ex) {
        throw new Exception("$errorBeg E#144 xxx SetConstants " .
            $ex->getMessage() . $errorEnd);
    }

    return $msg;
}
