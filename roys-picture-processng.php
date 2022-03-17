<?php
/*
 * Plugin Name: Roys picture processng
 * Description: A Selction box heasing for picture selection
 * Author: Guido, Roy Weil
 * Author URI: https://freewheeling.com
 * Donate URI: https://plugins.royweil.com/donate/
 * License: GNU General Public License v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Plugin URI: https://pluggins.roywil.com/details/roys-picture-processng/readme.txt
 * Text Domain: Roys-picture-processng
 * Domain Path: /translation
 
  * Version: 2.1.19
 */
// disable direct access
ini_set( "display_errors", true );
error_reporting( E_ALL | E_STRICT );

use lsolesen\ pel\ Pel;
use lsolesen\ pel\ PelConvert;
use lsolesen\ pel\ PelCanonMakerNotes;
use lsolesen\ pel\ PelDataWindow;
use lsolesen\ pel\ PelEntryException;
use lsolesen\ pel\ PelEntryAscii;
use lsolesen\ pel\ PelEntryByte;
use lsolesen\ pel\ PelEntryCopyright;
use lsolesen\ pel\ PelEntryLong;
use lsolesen\ pel\ PelEntryNumber;
use lsolesen\ pel\ PelEntryRational;
use lsolesen\ pel\ PelEntryShort;
use lsolesen\ pel\ PelEntrySShort;
use lsolesen\ pel\ PelEntrySRational;
use lsolesen\ pel\ PelEntrySLong;
use lsolesen\ pel\ PelEntryTime;
//use lsolesen\pel\PelEntryUndefined;
use lsolesen\ pel\ PelEntryUserComment;
use lsolesen\ pel\ PelEntryUserCopyright;
use lsolesen\ pel\ PelEntryVersion;
use lsolesen\ pel\ PelEntryWindowsString;
use lsolesen\ pel\ PelEntryUndefined;
use lsolesen\ pel\ PelExif;
use lsolesen\ pel\ PelFormat;
use lsolesen\ pel\ PelIfd;
use lsolesen\ pel\ PelIfdException;
use lsolesen\ pel\ PelIllegalFormatException;
use lsolesen\ pel\ PelInvalidDataException;
use lsolesen\ pel\ PelJpeg;
use lsolesen\ pel\ PelJpegComment;
use lsolesen\ pel\ PelJpegContent;
use lsolesen\ pel\ PelJpegInvalidMarkerException;
use lsolesen\ pel\ PelJpegMarker;
use lsolesen\ pel\ PelMakerNotes;
use lsolesen\ pel\ PelTag;
use lsolesen\ pel\ PelTiff;
use lsolesen\ pel\ PelWrongComponentCountException;

$pel = "/home/pillowan/www-shaw-weil-pictures-dev/wp-content/plugins" .
"/roys-picture-processng/pel-master/src";
require_once "$pel/Pel.php";
require_once "$pel/PelException.php";
require_once "$pel/PelInvalidArgumentException.php";
require_once "$pel/PelJpegInvalidMarkerException.php";
require_once "$pel/PelJpegContent.php";
require_once "$pel/PelMakerNotes.php";
require_once "$pel/PelCanonMakerNotes.php";
require_once "$pel/PelConvert.php";
require_once "$pel/PelDataWindow.php";
require_once "$pel/PelEntry.php";
require_once "$pel/PelEntryException.php";
require_once "$pel/PelEntryNumber.php";
require_once "$pel/PelEntryAscii.php";
require_once "$pel/PelEntryByte.php";
require_once "$pel/PelEntryCopyright.php";
require_once "$pel/PelEntryLong.php";
require_once "$pel/PelEntryRational.php";
require_once "$pel/PelEntrySLong.php";
require_once "$pel/PelEntrySRational.php";
require_once "$pel/PelEntryShort.php";
require_once "$pel/PelEntrySShort.php";
require_once "$pel/PelEntryTime.php";
require_once "$pel/PelEntryShort.php";
require_once "$pel/PelEntryUndefined.php";
require_once "$pel/PelEntryUserComment.php";
require_once "$pel/PelEntryVersion.php";
require_once "$pel/PelEntryWindowsString.php";
require_once "$pel/PelExif.php";
require_once "$pel/PelFormat.php";
require_once "$pel/PelIfd.php";
require_once "$pel/PelIfdException.php";
require_once "$pel/PelIllegalFormatException.php";
require_once "$pel/PelInvalidDataException.php";
require_once "$pel/PelJpeg.php";
require_once "$pel/PelJpegComment.php";
require_once "$pel/PelJpegMarker.php";
require_once "$pel/PelTag.php";
require_once "$pel/PelTiff.php";
require_once "$pel/PelWrongComponentCountException.php";
// commonly used rutines
require_once "freewheelingeasy-wpdpExtra.php";
require_once "rrw_util_inc.php";
require_once "display_stuff_class.php";
require_once "display_tables_class.php";
require_once "display_tables_inc.php";
// picture routines
require_once "admin.php";
require_once "displayone.php";
require_once "DisplayPhotogaphers.php";
require_once "displayphotos.php";
require_once "DisplayTrails.php";
require_once "fix.php";
require_once "fixTaskList.php";
require_once "keywordHandling.php";
require_once "rrwExif.php";
require_once "submission.php";
require_once "update.php";
require_once "uploadProcessDire.php";
class FreewheelingCommon {
    public static function missingImageMessage( $from, $photoname = "" ) {
        $msg = "This is somewhat embarrassing. The image that you
                requested ";
        if ( !empty( $photoname ) )
            $msg .= " ( $photoname )";
        $msg .= " was not found. Please remember your selection,
                what you did, copy this message 
                and go to the <a href='webmaster-feedback' >feedback form</a>
                and tell us what you did to get here ($from) ";
        return $msg;
    } // end funvtion missingImageMessage
   
} // end of class
function SetConstants( $whocalled ) {
    include "setConstants.php";
}

function rrw_getAccessID() {
    $current_user = wp_get_current_user();
    if ( !( $current_user instanceof WP_User ) )
        $id = "Guest";
    else
        $id = $current_user->display_name;
    $id .= "-" . rrwUtil::fetchparameterString( "session" );
    $id .= "-" . rrwUtil::fetchparameterString( "REMOTE_ADDR" );
    $id .= "-" . rrwUtil::fetchparameterString( "USERDOMAN" );
    $id .= "-" . rrwUtil::fetchparameterString( "USERNAME" );
    return $id;
}

function updateAccessTable( $photoname, $search ) {
    global $wpdbExtra, $rrw_access;
    global $picrureCookieName;
    global $eol;
    $msg = "";
    $userid = rrw_getAccessID();
    $update = array( "accessIP" => $userid );
    if ( !empty( $user ) )
        $update[ "accessuser" ] = $user;
    if ( !empty( $photoname ) )
        $update[ "accessphotoname" ] = $photoname;
    if ( !empty( $search ) )
        $update[ "accessSearch" ] = $search;
    $answer = $wpdbExtra->insert( $rrw_access, $update );
    return $msg;
}

function direReport( $field, $report = "" ) {
    // count the number of items for a particular field
    global $wpdbExtra, $rrw_photos;
    global $eol;
    $msg = "";
    $msg .= SetConstants( "direReport" );
    $sqlTotal = "select count(*) from $rrw_photos";
    $total = $wpdbExtra->get_var( $sqlTotal );
    $sqlBlank = "select count(*) from $rrw_photos where $field is null or
                    trim($field) = ''";
    $blank = $wpdbExtra->get_var( $sqlBlank );
    $msg .= "there are $blank blank $field out of $total photos $report $eol";
    return $msg;
}

function pushToImage( $filename, $item, $value ) {
    
    $msg = rrwExif::pushToImage( $filename, $item, $value ) ;
    return $msg;
    
    
    global $eol, $errorBeg, $errorEnd;
    global $photoPath;
    $msg = "";
    $debugExif = false;
    try {
        if ( $debugExif )$msg .= "---------------------------------------$eol";
        if ( false === strpos( $filename, "home" ) )
            $filename = "$photoPath/$filename" . "_cr.jpg";
        //       ini_set( 'memory_limit', '32M' );
        if ( $debugExif )$msg .= "( $filename, $item, $value ) $eol";
        $tmpfname = str_replace( "jpg", "_copyright.jpg", $filename );
        if ( $debugExif )$msg .= "tempfile is $tmpfname $eol";
        $contents = file_get_contents( $filename );
        if ( $debugExif )$msg .= " got contents of $filename $eol";
        $ss = strlen( $contents );
        if ( $debugExif )$msg .= " contents size is $ss $eol";
        $data = new PelDataWindow( $contents );
        if ( $debugExif )$msg .= " new PelDataWindow worked  $eol";
        $jpeg = $fileInMemory = new PelJpeg();
        if ( $debugExif )$msg .= " new PelJpeg() worked  $eol";
        try {
            //          $jpeg->load( $data );
            $jpeg->loadfile( $filename );
        } // end try
        catch ( Exception $ex ) {
            throw new Exception( "$errorBeg E#703 working on $item, 
                    jprg->load(data) failed" . $ex->getMessage() . $errorEnd );
        }
        if ( $debugExif )$msg .= "load worked  $eol";
        $pelExif = $jpeg->getExif(); // get the desired data
        if ( $debugExif )$msg .= "pelExif is loaded $eol";
        if ( $pelExif == null ) {
            if ( $debugExif )$msg .= println( 'No exif found, create new.' );
            $pelExif = new PelExif(); // create on
            $jpeg->setExif( $pelExif ); // insert it into the memory version
        }
        $pelTiff = $pelExif->getTiff();
        if ( $pelTiff == null ) {
            if ( $debugExif )$msg .= println( 'No Tiff found, create new.' );
            $pelTiff = new PelTiff();
            $pelExif->setTiff( $pelTiff );
        }

        $pelIfd0 = $pelTiff->getIfd();
        if ( $pelIfd0 == null ) {
            if ( $debugExif )$msg .= println( 'No ifdo found, create new.' );
            $pelIfd0 = new PelIfd( PelIfd::IFD0 );
            $pelTiff->setIfd( $pelIfd0 );
        }
        
        if ( $debugExif )$msg .= "compleated setup, get/adjust the data $eol ";
        $tag = convertText2EeixID( $item ); // item name into tag integer
        if ( $debugExif )$msg .= "item is $item, tag integer is $tag (" .
            dechex($tag) . ") $eol ";
        $textThing = $pelIfd0->getEntry( $tag );
        if ( is_null( $textThing ) ) {
            if ( $debugExif )$msg .= "tag did not exist, create a new one $eol";
            $type = findTagtype( $tag );
            if ( $debugExif )$msg .= "Adding new $tag (" . dechex( $tag ) . ") of type $type  with value $value";
            switch ( $type ) {
                case "Copyright":
                case "copyright":
                    $textThing = new PelEntryCopyright( $value );
                    break;
                case "Ascii":
                case "ascii":
                    $textThing = new PelEntryAscii( $tag, $value );
                    break;
                case "byte":
                case "Byte":
                    $textThing = new PelEntryByte( $tag, $value );
                    break;
                default:
                    throw new Exception( "E#488 Unknown findtagtype for $tag" );
                    break;
            }
            $pelIfd0->addEntry( $textThing );
            $textThing = $pelIfd0->getEntry( $tag );
            $oldValue = "";
        } else {
            // update an existing tag
            if ( $debugExif )$msg .= "tag thing exits $eol";
            $oldValue = $textThing->getValue();
            if ( $debugExif )$msg .= rrwUtil::print_r( $oldValue, true, "found old value of $item" );
            $textThing->setValue( $value );
        }
        $newValue = $textThing->getValue();
        if ( $debugExif )$msg .= rrwUtil::print_r( $newValue, true, " set new tag value of $item" );
        if ( $debugExif )$msg .= println( "Writing file $tmpfname $eol" );
        $fileInMemory->saveFile( $tmpfname );
        $jpeg->saveFile( $tmpfname );
        $sizeOld = filesize( $filename );
        $sizeNew = filesize( $tmpfname );
        if ( $debugExif )$msg .= rrwExif::dumpMeta( $filename, $tmpfname );
        if ( abs( $sizeOld - $sizeNew ) < 500 ) {
            //      copy($tmpfname, "$filename.jpg");
            //    if ( $debugExif )$msg .= "copy( $tmpfname, $filename.jpg ) $eol";
            //   return $msg;
            unlink( $filename );
            rename( $tmpfname, $filename );
            if ( $debugExif )$msg .= "rename( $tmpfname, $filename ) $eol";
        } else {
            if ( true ) {
                unlink( $filename );
                rename( $tmpfname, $filename );
                if ( $debugExif )$msg .= "rename( $tmpfname, $filename ) $eol";
            }
            $err= " old size is $sizeOld, new size is $sizeNew,
                difference  (" . $sizenew-$sizeold . ") 
                is more than 500 please check$eol" ;
            throw new Exception ("$msg E#444 $err");
        }
        $ii = strrpos( $filename, "/" );
        $basename = substr( $filename, $ii );
        $itemname = str_replace( ".jpg", "", $basename );
        $itemname = str_replace( "_cr", "", $itemname );
        $itemname = str_replace( "_tmb", "", $itemname );
        // copyright and comment may be stored as an arrray

        if (is_array($newValue))
            $newDisplay = rrwUtil::print_r($newValue, true, "array");
        else
            $nameDisplay = $newValue;
        $comment = "$basename -- $oldValue -> $newDisplay";
        $msg .= rrwUtil::InsertIntoHistory( $itemname, $comment );
        if ( $debugExif )$msg .= "History writin $comment $eol ----- $eol";
    } // end try
    catch ( Exception $ex ) {
        $msg .= "$errorBeg E#963 in pushtoimage: " . $ex->getMessage() . $errorEnd;
    }
    return $msg;
}

/* a printf() variant that appends a newline to the output. */
function println( $fmt, $value = "" ) {
    global $eol;
    return $fmt;
    if ( !empty( $value ) )
        $fmt = sprintf( $fmt, $value );
    return "$fmt $eol";
}
// picture tasks
SetConstants( "by the short codes" );
add_shortcode( "author2copyright", array( "freewheeling_fixit", "Author2Copyright" ) );
add_shortcode( "adminpictures", array( "freewhilln_Administration_Pictures",
    "administrationPicures" ) );
add_shortcode( "displayphotos", array( "freewheeling_displayPhotos", "displayPhotos" ) );
add_shortcode( "display-one-photo",
    array( "freeWheeling_DisplayOne", "DisplayOne" ) );
add_shortcode( "display-photographer", array( "DisplayPhotographers", "Display" ) );
add_shortcode( "displaytrail", array( "DisplayTrails", "Display" ) );
add_shortcode( "displayupdate", array( "freeWheeling_DisplayUpdate", "DisplayUpdate" ) );
add_shortcode( "fix", array( "freewheeling_fixit", "fit_it" ) );
add_shortcode( "fixtasklist", array( "fixTaskList", "showlist" ) );
add_shortcode( "rrwPicSubmission", array( "rrwPicSubmission", "showForm" ) );
add_shortcode( "upload", array( "uploadProcessDire", "upload" ) );
add_shortcode( "testing",array("rrwExif", "test") );

require 'plugin_update_check.php';
$MyUpdateChecker = new PluginUpdateChecker_2_0(
    'http://pluginserver.royweil.com/roys-picture-processng.php',
    __FILE__,
    'roys-very-picture-processng',
    1
);
?>
