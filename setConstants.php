<?php

global $wpdbExtra, $rrw_photos, $rrw_keywords, $rrw_trails;
global $rrw_photographer, $rrw_photographers, $rrw_access, $rrw_history;
global $rrw_digipix;
global $eol, $errorBeg, $errorEnd;
global $photoUrl, $photoPath, $thumbUrl, $thumbPath, $highresUrl, $highresPath;
global $rejectUrl, $rejectPath;
global $uploadPath;
global $gallery;
global $freewheel_table_prefix;
global $picrureCookieName;


error_reporting( E_ALL | E_STRICT );
ini_set( "display_errors", true );
try {
    $eol = "<br />\n";
    $errorBeg = "<span style='color:red;' >";
    $errorEnd = "</span>$eol";

    $gallery = "/home/pillowan/www-shaw-weil-pictures-dev/wp-content/gallery/testing";
    $picrureCookieName = "picture_visit_ip_time";

   if ( !is_object( $wpdbExtra ) )
        $wpdbExtra = new wpdbExtra;
    $rrw_history = $wpdbExtra->prefix . "1rrw_history";
    
    $tablePrefix = $wpdbExtra->prefix . "0";
    $rrw_access = $tablePrefix . "access";
    $rrw_photos = $tablePrefix . "photos";
    $rrw_keywords = $tablePrefix . "keywords";
    $rrw_photographers = $tablePrefix . "photographers";
    $rrw_trails = $tablePrefix . "trails";
    $rrw_digipix = $tablePrefix . "digipix";

    $siteUrl = get_site_url();
    $photoUrl = "$siteUrl/photos";
    $thumbUrl = "$photoUrl/thumbs";
    $highresUrl = "$photoUrl/high_resolution";
    $rejectUrl  = "$photoUrl/reject";

    $photoPath = ABSPATH . "photos";
    //       $photoPath = "/home/pillowan/www-shaw-weil-pictures/photos";
    $thumbPath = "$photoPath/thumbs";
    $highresPath = "$photoPath/high_resolution";
    $uploadPath = "$photoPath/upload";
    $rejectPath = "$photoPath/reject";
 
} catch ( Exception $ex ) {
    throw new Exception( "$errorBeg E#499 xxx SetConstants " . $ex->get_message() . $errorEnd );
}
?>
