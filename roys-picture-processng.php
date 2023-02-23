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
 
  * Version: 2.1.80
 */
// disable direct access
ini_set("display_errors", true);
error_reporting(E_ALL | E_STRICT);

use lsolesen\pel\Pel;
use lsolesen\pel\PelConvert;
use lsolesen\pel\PelCanonMakerNotes;
use lsolesen\pel\PelDataWindow;
use lsolesen\pel\PelEntryException;
use lsolesen\pel\PelEntryAscii;
use lsolesen\pel\PelEntryByte;
use lsolesen\pel\PelEntryCopyright;
use lsolesen\pel\PelEntryLong;
use lsolesen\pel\PelEntryNumber;
use lsolesen\pel\PelEntryRational;
use lsolesen\pel\PelEntryShort;
use lsolesen\pel\PelEntrySShort;
use lsolesen\pel\PelEntrySRational;
use lsolesen\pel\PelEntrySLong;
use lsolesen\pel\PelEntryTime;
//use lsolesen\pel\PelEntryUndefined;
use lsolesen\pel\PelEntryUserComment;
use lsolesen\pel\PelEntryUserCopyright;
use lsolesen\pel\PelEntryVersion;
use lsolesen\pel\PelEntryWindowsString;
use lsolesen\pel\PelEntryUndefined;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelFormat;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelIfdException;
use lsolesen\pel\PelIllegalFormatException;
use lsolesen\pel\PelInvalidDataException;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelJpegComment;
use lsolesen\pel\PelJpegContent;
use lsolesen\pel\PelJpegInvalidMarkerException;
use lsolesen\pel\PelJpegMarker;
use lsolesen\pel\PelMakerNotes;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelTiff;
use lsolesen\pel\PelWrongComponentCountException;

$pel = "/home/pillowan/www-shaw-weil-pictures-dev/wp-content/plugins" .
    "/roys-picture-processng/pel-master/src";
$pel = "pel-master/src";
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
// picture routines
require_once "rrw_admin.php";
require_once "displayone.php";
require_once "DisplayPhotogaphers.php";
require_once "displayphotos.php";
require_once "DisplayTrails.php";
require_once "rrw_fix.php";
require_once "fixTaskList.php";
require_once "keywordHandling.php";
require_once "rrwExif.php";
require_once "setConstants.php";
require_once "submission.php";
require_once "rrw_update-picture.php";
require_once "uploadProcessDire.php";
/*
*/
class FreewheelingCommon
{
    public static function missingImageMessage($from, $photoname = "")
    {
        $msg = "This is somewhat embarrassing. The image that you
                requested ";
        if (!empty($photoname))
            $msg .= " ( $photoname )";
        $msg .= " was not found. Please remember your selection,
                what you did, copy this message 
                and go to the <a href='webmaster-feedback' >feedback form</a>
                and tell us what you did to get here ($from) ";
        return $msg;
    } // end funvtion missingImageMessage

} // end of class
function rrw_getAccessID()
{
    $current_user = wp_get_current_user();
    if (!($current_user instanceof WP_User))
        $id = "Guest";
    else
        $id = $current_user->display_name;
    $id .= "-" . rrwUtil::fetchparameterString("session");
    $id .= "-" . rrwUtil::fetchparameterString("REMOTE_ADDR");
    $id .= "-" . rrwUtil::fetchparameterString("USERDOMAN");
    $id .= "-" . rrwUtil::fetchparameterString("USERNAME");
    return $id;
}
function updateAccessTable($photoname, $search)
{
    global $wpdbExtra, $rrw_access;
    global $picrureCookieName;
    global $eol;
    $msg = "";
    $userid = rrw_getAccessID();
    $update = array("accessIP" => $userid);
    if (!empty($user))
        $update["accessuser"] = $user;
    if (!empty($photoname))
        $update["accessphotoname"] = $photoname;
    if (!empty($search))
        $update["accessSearch"] = $search;
    $answer = $wpdbExtra->insert($rrw_access, $update);
    return $msg;
}
function direReport($field, $report = "")
{
    // count the number of items for a particular field
    global $wpdbExtra, $rrw_photos;
    global $eol;
    $msg = "";
    $sqlTotal = "select count(*) from $rrw_photos";
    $total = $wpdbExtra->get_var($sqlTotal);
    $sqlBlank = "select count(*) from $rrw_photos where $field is null or
                    trim($field) = ''";
    $blank = $wpdbExtra->get_var($sqlBlank);
    $msg .= "there are $blank blank $field out of $total photos $report $eol";
    return $msg;
}
function pushToImage($filename, $item, $value)
{

    $msg = rrwExif::pushToImage($filename, $item, $value);
    return $msg;
}

function println($fmt, $value = "")
{
    // a printf() variant that appends a newline to the output. 
    global $eol;
    return $fmt;
    if (!empty($value))
        $fmt = sprintf($fmt, $value);
    return "$fmt $eol";
}
// picture tasks
SetConstants("by the short codes");
add_shortcode("author2copyright", array("freewheeling_fixit", "Author2Copyright"));
add_shortcode("adminpictures", array(
    "freewhilln_Administration_Pictures",
    "administrationPicures"
));
add_shortcode("displayphotos", array("freewheeling_displayPhotos", "displayPhotos"));
add_shortcode(
    "display-one-photo",
    array("freeWheeling_DisplayOne", "DisplayOne")
);
add_shortcode("display-photographer", array("DisplayPhotographers", "Display"));
add_shortcode("displaytrail", array("DisplayTrails", "Display"));
add_shortcode("displayupdate", array("freeWheeling_DisplayUpdate", "DisplayUpdate"));
add_shortcode("fix", array("freewheeling_fixit", "fit_it"));
add_shortcode("fixtasklist", array("fixTaskList", "showlist"));
add_shortcode("rrwPicSubmission", array("rrwPicSubmission", "showForm"));
add_shortcode("upload", array("uploadProcessDire", "upload"));
add_shortcode("testingexif", array("rrwErrwExif", "test11"));
require_once 'plugin_update_check.php';
$MyUpdateChecker  = new PluginUpdateChecker_2_0(
    'http://pluginserver.royweil.com/roys-picture-processng.php',
    __FILE__,
    'roys-very-picture-processng',
    1
);
