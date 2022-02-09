<?php

private static function SetConstants( $whocalled ) {
    global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_keywords, $rrw_trails;
    global $rrw_photographer, $rrw_photographers, $rrw_access;
    global $eol, $errorBeg, $errorEnd;
    global $photoUrl, $photoPath, $thumbUrl, $thumbPath, $highresUrl, $highresPath;
    global $uploadPath;
    global $gallery;
    global $freewheel_table_prefix;

    error_reporting( E_ALL | E_STRICT );
    ini_set( "display_errors", true );
    try {
        $eol = "<br />";
        $errorBeg = "<span style='text-color:red' >";
        $errorEnd = "</span>";

        if ( !is_object( $wpdbExtra ) )
            $wpdbExtra = new wpdbExtra;
        $tablePrefix = $wpdbExtra->prefix . "0";
        $rrw_access = $tablePrefix . "access";
        $rrw_photos = $tablePrefix . "photos";
        $rrw_source = $tablePrefix . "source";
        $rrw_keywords = $tablePrefix . "keywords";
        $rrw_photographers = $tablePrefix . "photographers";
        $rrw_trails = $tablePrefix . "trails";

        $gallery = "/home/pillowan/www-shaw-weil-pictures-dev/wp-content/gallery/testing";
        $photoUrl = "https://pictures.shaw-weil.com/photos";
        $thumbUrl = "$photoUrl/thumbs";
        $highresUrl = "$photoUrl/high_resolutiom";
        $photoPath = "/home/pillowan/www-shaw-weil-pictures/photos";
        $thumbPath = "$photoPath/thumbs";
        $highresPath = "$photoPath/high_resolution";
        $uploadPath = "$photoPath/upload";
    } catch ( Exception $ex ) {
        throw new Exception ( "$errorBeg E#499 xxx SetConstants " . 
                    $ex->get_message() . $errorEnd);
    }

}

?>
