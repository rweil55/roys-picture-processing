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
 
  * Version: 1.0.16

 */
// disable direct access

ini_set("display_errors", true);
error_reporting(E_ALL | E_STRICT);


use lsolesen\ pel\ Pel;
use lsolesen\ pel\ PelConvert;
use lsolesen\ pel\ PelCanonMakerNotes;
use lsolesen\ pel\ PelDataWindow;
use lsolesen\ pel\ PelEntryAscii;
use lsolesen\ pel\ PelEntryByte;
use lsolesen\ pel\ PelEntryCopyright;
use lsolesen\ pel\ PelEntryLong;
use lsolesen\ pel\ PelEntryNumber;
use lsolesen\ pel\ PelEntryRational;
use lsolesen\ pel\ PelEntryShort;
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

ini_set( "display_errors", true );
$pel = "/home/pillowan//www-shaw-weil-pictures/wp-content/plugins" .
"/roys-picture-processng/pel-master/src";
require_once "$pel/Pel.php";
require_once "$pel/PelException.php";
require_once "$pel/PelJpegInvalidMarkerException.php";
require_once "$pel/PelJpegContent.php";
require_once "$pel/PelMakerNotes.php";
require_once "$pel/PelCanonMakerNotes.php";
require_once "$pel/PelConvert.php";
require_once "$pel/PelDataWindow.php";
require_once "$pel/PelEntry.php";
require_once "$pel/PelEntryNumber.php";
require_once "$pel/PelEntryAscii.php";
require_once "$pel/PelEntryByte.php";
require_once "$pel/PelEntryCopyright.php";
require_once "$pel/PelEntryLong.php";
require_once "$pel/PelEntryRational.php";
require_once "$pel/PelEntrySLong.php";
require_once "$pel/PelEntrySRational.php";
require_once "$pel/PelEntryShort.php";
require_once "$pel/PelEntryTime.php";
require_once "$pel/PelEntryShort.php";
require_once "$pel/PelEntryUndefined.php";
require_once "$pel/PelEntryUserComment.php";
require_once "$pel/PelEntryVersion.php";
require_once "$pel/PelEntryWindowsString.php";
//require_once "$pel/PelEntryUndefined.php";
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

// commonly used rutines
require_once "freewheelingeasy-wpdpExtra.php";
require_once "rrw_util_inc.php";
require_once "display_stuff_class.php";
require_once "display_tables_class.php";
// picture routines

require_once "admin.php";
require_once "displayone.php";
require_once "DisplayPhotogaphers.php";
require_once "displayphotos.php";
require_once "DisplayTrails.php";
require_once "fix.php";
require_once "keywordHandling.php";
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

    public static function copyMeta( $inputPath, $outputPath ) {
        global $eol, $errorBeg, $errorEnd;
        $msg = "";
        return $msg;
        $debug = true;
        ini_set( "displaoy_errors", true );
        error_reporting( E_ALL | E_STRICT );

        try {
            $inputPel = new PelJpeg( $inputPath );
            $outputPel = new PelJpeg( $outputPath );
            $exif = $inputPel->getExif();
            if ( is_null( $exif ) )
                throw new Exceprion( "$errorBeg e#491 inputPel->getExif() failed $errorEnd" );
            if ( $debug ) print "got exif$eol ";
            $resultr1 = $outputPath->setExif( $exif );
            if ( $debug ) print "called setExif exif$eol ";
            if ( is_null( $resultr1 ) )
                throw new Exceprion( "$errorBeg e#492 outputPath->setExif() failed $errorEnd" );
            $resullt2 = $outputPel->saveFile( $outputPath );
            if ( $debug ) print "got exif$eol ";
            if ( is_null( $resullt2 ) )
                throw new Exceprion( "$errorBeg e#492 outputPel->saveFile(() failed $errorEnd" );
        } catch ( Exception $ex ) {
            $msg .= "E#400 xxx catch " . $ex->get_message();
        }
        return "$msg Meta copied from $eol$inputPath $eol $outputPath $eol";
    }
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
    global $wpdbExtra, $rrw_photos, $rrw_source;
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
/*

function insertdire( $attr ) {
    global $eol;
    global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_keywords;
    $startTime = new DateTime();
    $msg = "";
    $msg .= SetConstants( "insertdire" );
    $msg .= updateDiretoryOnFileMatch();
    $msg .= updatePhotekeywordsFromKeywordTable();
    $endTime = new DateTime();
    $minutes = $endTime->diff( $startTime );
    $minutes = $minutes->format( "%i:%s" );
    $msg .= "totaltime $minutes $eol";
    return $msg;
}

function updatePhotekeywordsFromKeywordTable() {
    // used to eliminate the keyword table
    // moves the keyword entirs into a field in the photo database
    global $eol;
    global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_keywords;
    $msg = "";

    $sql2 = "select keywordFileName, keyword from $rrw_keywords
            order by keywordFileName, keyword ";
    $msg .= " -----------------  $sql2 $eol";
    $recs = $wpdbExtra->get_resultsA( $sql2 );
    $cntrecs = $wpdbExtra->num_rows;
    $msg .= "Found $cntrecs rows of data $eol";
    $cntInsert = 0;
    $keywordlist = "";
    $pastFileName = $recs[ 0 ][ "keywordFileName" ];
    foreach ( $recs as $rec ) {
        $keyword = $rec[ "keyword" ];
        $keywordFileName = $rec[ "keywordFileName" ];
        if ( empty( $keyword ) )
            continue;
        if ( $pastFileName != $keywordFileName ) {
            $cntInsert++;
            if ( $cntInsert > 3000 )
                break;
            $keywordlist = substr( $keywordlist, 1 ); // remove leading coma
            $sqlup = "update $rrw_photos set photoKeyword = '$keywordlist'
                    where keywordFileName = $pastFileName ";
            $cntUps = $wpdbExtra->query( $sqlup );
            $msg .= "$cntUps -- $sqlup $eol";
            $pastFileName = $keywordFileName;
            $keywordlist = "";
        }
        $keywordlist .= ", $keyword";
        //       $msg .= "new key = $keyword --- list is $keywordlist $eol";
    }
    return $msg;

} // END updatePhotekeywordsFromKeywordTable

function updateDiretoryOnFileMatch() {
    // used to relate the entries in photo datta database to the file location on spoke
    // if file name matchs then update the diterctory field
    global $eol;
    global $wpdbExtra, $rrw_photos, $rrw_source;
    $msg = "";

    $msg .= SetConstants( "updateDiretoryOnFileMatch" );
    $sql = "select photo_id, sourcedire, directory, sourcefilename, filename 
            from $rrw_source 
            right join $rrw_photos on filename = sourcefilename
            where directory is null";
    $msg .= "$sql $eol";
    return $msg;
    $recs = $wpdbExtra->get_resultsA( $sql );
    $cntrecs = $wpdbExtra->num_rows;
    $msg .= "Found $cntrecs rows of data $eol";
    $cntInsert = 0;
    foreach ( $recs as $rec ) {
        $cntInsert++;
        if ( $cntInsert > 500 )
            break;
        $sourcedire = $rec[ "sourcedire" ];
        $photo_id = $rec[ "photo_id" ];
        $sqlins = "update $rrw_photos set directory = '$sourcedire'
                    where photo_id = $photo_id ";
        $updateCnt = $wpdbExtra->query( $sqlins );
        $msg .= "$updateCnt -- $sqlins $eol ";

    }
    return $msg;
} // wns updateDiretoryOnFileMatch

*/

function updateRename( $filename, $newname, $sourceFullName ) {
    global $eol;
    global $wpdbExtra, $rrw_photos, $rrw_source;
    global $photoPath;
    $msg = "";
    //
    if ( empty( $filename ) ) {
        $filename = rrwUtil::fetchparameterString( "filename" );
        $newname = rrwUtil::fetchparameterString( "new" );
        $sourceFullName = rrwUtil::fetchparameterString( "source" );
    }
    if ( empty( $sourceFullName && "dale" == substr( $newname, 0, 4 ) ) ) {
        $sourceFullName = "P:/film-scan/" . substr( $newname, 0, 6 ) . "/IMAGES/$newname";
    }
    if ( empty( $newname ) ) {
        $msg .= "<form >
        <input type='text' name='filename' diabled value='$filename' />
        <input type='text' name='new' value='$filename' />
        <input type='submit' value='rename dataase/files' />
        <input type='hidden' name='task' value='rename' />
        </form>";
        return $msg;
    }
    $msg .= "updateRename( $filename, $newname, $sourceFullName )";
    rename( "$photoPath/${filename}_cr.jpg", "$photoPath/{$newname}_cr.jpg" );
    rename( "$photoPath/thumbs/{$filename}_tmb.jpg",
        "$photoPath/thumbs/{$newname}_tmb.jpg" );
    $sqlupdate = "update $rrw_photos set filename = '$newname',
                direOnP = '$sourceFullName' where filename ='$filename' ";
    $wpdbExtra->query( $sqlupdate );
    $msg .= "$sqlupdate $eol";
    $msg .= "<a href='http://127.0.0.1/pict/sub.php?direname=' target='submit' >
                upload picture file </a> $eol ";
    return $msg;
}

/*
function doCopyright( $item ) {
    global $eol;
    global $wpdbExtra, $rrw_photos, $rrw_source, $rrw_photographer, $rrw_keywords;
    global $photoUrl, $photoPath, $thumbUrl, $thumbPath, $highresUrl, $highresPath;
    $msg = "";
    try {
        print direReport( $item, "with blank $item" );
        $msg .=  rrwUtil::deltaTimer() . $eol;
        switch ( $item ) {
            case "copyright":
                $sql = "select ph.copyright item, filename, copyrightDefault defaultitem
                from $rrw_photos ph
                left join $rrw_photographer pres 
                        on ph.photographer = pres.photographer
                 where ph.copyright = ''";
                $exifItem = $item;
                break;
            case "photoKeyword":
                $sqlMatch = "select distinct $rrw_photos.photo_id, filename 
                        from $rrw_photos 
                        join $rrw_keywords 
                            on Not $rrw_keywords.photo_id = $rrw_photos.photo_id ";
                $recsIds = $wpdbExtra->get_resultsA($sqlMatch);
                $cnt =0;
                foreach($recsIds as $recId) {
                    $cnt++;
                    $photoid = $recId["photo_id"];
                    $filename = $recId["filename"];
                    $sqlFix = "update $rrw_keywords 
                        set keywordFilename = '$filename'
                            where photo_id = $photoid";
                    $wpdbExtra->query($sqlFix);
                }
                $msg .= "update $cnt filenames in keyword $eol";
                $sql = "select photoKeyword item, filename, photoKeyword defaultitem
                from $rrw_photos ph
                 where photoKeyword = ''";
                $exifItem = "keyword";
                 break;
            default:
                $msg .= "E#497 Unknown item of  '$item' $eol ";
                return $msg;
        }
        $msg .= "$sql $eol";
        $recs = $wpdbExtra->get_resultsA( $sql );
        $cntrecs = $wpdbExtra->num_rows;
       print "Found $cntrecs rows of data $eol";
        $msg .= "Found $cntrecs rows of data $eol";
        $cntInsert = 0;
        $sqlList = array(); // list of the flippers
        $cnt = 0;
        $msg .= "<table>";  
        $msg .= rrwFormat::headerRow( "file", "database", "meta data", "defalut" );
        $exifdata = "";
        foreach ( $recs as $rec ) {
            $cntInsert++;
            if ( $cntInsert > 2 )
                break;
            $searchname = $rec[ "filename" ];
            $defaultitem = $rec[ "defaultitem" ];
            $dbItem = $rec["item"];
            $sqlkeys = "select keyword from $rrw_keywords 
                    where keywordfilename = '$searchname'";
            $reckeys = $wpdbExtra->get_resultsA($sqlkeys);
             $defaultitem = "";
            foreach($reckeys as $reckey) {                
                $defaultitem .= $reckey["keyword"] . ",";
            }
            $msg .= "keys are $defaultitem $eol";
            foreach ( array( $photoPath => "_cr.jpg", $thumbPath => "_tmb.jpg" ) as $path => $ext ) {
                $fullFilename = "$path/$searchname{$ext}";
                $FilenameDisplay = "<a href='https://pictures.shaw-weil.com" .
                "/display-one-photo?photoname=$searchname'  target ='one' >
                    $searchname</a>";
                if ( !file_exists( $fullFilename ) ) {
                    $msg .= rrwFormat::CellRow( $FilenameDisplay, "E#493 file not exist",
                        "**************" );
                    break;
                }
                $debugExif = true;
                
                print "$FilenameDisplay - " . filesize( $fullFilename ) . "bytes, 
                <a href='/fix?task=rename&amp;filename=$searchname' 
                        target='rename' > rename </a> ";
                //         continue;
                if ($debugExif) print "about  exif_read_data( $fullFilename $eol)"; 
                $meta = exif_read_data( $fullFilename );
                if ($debugExif) print "looking for key $item $eol";
                if ( array_key_exists( "$exifItem", $meta ) )
                    $metaItem = $meta[ "$exifItem" ];
                else
                    $metaItem = "&lt;&lt; Missing &gt;&gt;";
                $msg .= rrwFormat::CellRow( $FilenameDisplay, $dbItem, $metaItem,
                    $defaultitem );
                if ( $metaItem != $dbItem ) {
                    $tmpfname = str_replace( "jpg", "_copyright.jpg", $fullFilename );
                     if ($debugExif) print "changeItem( $fullFilename, $tmpfname, $exifItem, $defaultitem ) $eol";
                    $msg .= changeItem( $fullFilename, $tmpfname, $exifItem, $defaultitem );
                    $sqlins = "update $rrw_photos set $item = '$defaultitem'
                        where filename = '$searchname' ";
                    $updateCnt = $wpdbExtra->query( $sqlins );
                    //              $msg .= "$updateCnt -- $sqlins $eol ";
                    //              $msg .= dumpMeta( "$fullFilename", $tmpfname);
                    unlink( $fullFilename );
                    rename( $tmpfname, $fullFilename );
                }
            } // end of checking _cr and _rmb
        } // end of looking for records
        //        
        $msg .= "</table>";
        $msg .= direReport( "copyright", "with blank copyright" );
        foreach ( $sqlList as $sql )
            $msg .= "$sql $eol";
     } catch ( Exception $ex ) {
        $msg .= "E#491 ";
    }
    $msg .=  rrwUtil::deltaTimer() . $eol;
   return $msg;
} // end of updatdoCopyright
*/
function readexifItem( $filename, $item, & $msg ) {
    global $eol, $errorBeg, $errorEnd;
    $debugExif = true;
    ini_set( "display_errors", true );
    error_reporting( E_ALL | E_STRICT );

    if ( !file_exists( $filename ) ) {
        $tr = rrwFormat::backtrace( 5 );
        throw new Exception( "$msg $errorBeg E#431 readexifItem: $filename 
                    does not exists $errorEnd $tr $eol" );
    }
    if ( filesize( $filename > 10000 ) )
        throw new Exception( "$msg $errorBeg E#432 readexifItem: $filename 
                    is to big " . round( filesize( $filename ) / 1024, 0 ) . " $errorEnd" );
    $exif = exif_read_data( $filename );
    if ( array_key_exists( $item, $exif ) )
        return $exif[ $tem ];
    return "&lt;Missing&gt;";
}
/* reading item done by readexifItem (...)
function readexifPhoto( $filename, $item, & $msg ) {
    global $eol, $errorBeg, $errorEnd;
    $debugExif = true;

    ini_set( 'memory_limit', '32M' );
    error_reporting( E_ALL | E_STRICT );
    if ( $debugExif )$msg .= "readexifItem( $filename, $item ) $eol";
    if ( !file_exists( $filename ) ) {
        $msg .= "$msg $errorBeg E#468 $filename was not founnd $errorEnd ";
        return "";
    }
    $contents = file_get_contents( $filename );
    //   return $msg;
    $data = new PelDataWindow( $contents );
    if ( $debugExif )$msg .= " new PelDataWindow worked  $eol";
    //    return $msg;

    $jpeg = $fileInMemory = new PelJpeg();
    if ( $debugExif )$msg .= " new PelJpeg() worked  $eol";
    //    return $msg;
    $jpeg->load( $data );
    if ( $debugExif )$msg .= "load worked  $eol";
    //  return $msg;
    $exif = $jpeg->getExif(); // get the desired data
    if ( $debugExif )$msg .= "exif isloaded $eol";
    //  return $msg;
    if ( is_null( $exif ) )
        return "";
    $tiff = $exif->getTiff();
    if ( is_null( $tiff ) )
        return "";
    $ifd0 = $tiff->getIfd();
    if ( $debugExif )$msg .= "ifd0 isloaded $eol";
    if ( $ifd0 == null )
        return "";
    $tag = convertText2EeixID( $item ); // item name into tag integer
    if ( $debugExif )$msg .= "tag = $tag $eol";
    $textThing = $ifd0->getEntry( $tag );
    if ( $debugExif )$msg .= "textThing isloaded $eol";
    if ( is_null( $textThing ) )
        return "";
    $textValue = $textThing->getValue();
    if ( $debugExif )$msg .= "returning value $value $eol";

    return $textValue;
}
*/

function pushToImage( $filename, $item, $value ) {
    global $eol;
    global $photoPath;
    $msg = "";
    $debugExif = false;

    if ( false === strpos( $filename, "home" ) )
        $filename = "$photoPath/$filename" . "_cr.jpg";
    ini_set( 'memory_limit', '32M' );
    if ( $debugExif )$msg .= "( $filename, $item, $value ) $eol";
    $tmpfname = str_replace( "jpg", "_copyright.jpg", $filename );
    if ( $debugExif )$msg .= "tempfile is $tmpfname $eol";
    $contents = file_get_contents( $filename );
    //   return $msg;
    $data = new PelDataWindow( $contents );
    if ( $debugExif )$msg .= " new PelDataWindow worked  $eol";
    //    return $msg;

    $jpeg = $fileInMemory = new PelJpeg();
    if ( $debugExif )$msg .= " new PelJpeg() worked  $eol";
    //    return $msg;
    $jpeg->load( $data );
    if ( $debugExif )$msg .= "load worked  $eol";
    //  return $msg;
    $exif = $jpeg->getExif(); // get the desired data
    if ( $debugExif )$msg .= "exif isloaded $eol";
    //  return $msg;
    if ( $exif == null ) {
        if ( $debugExif )$msg .= println( 'No APP1 section found, added new.' );
        $exif = new PelExif(); // create on
        $jpeg->setExif( $exif ); // insert it into the memory version

        /* We then create an empty TIFF structure in the APP1 section. */
        $tiff = new PelTiff();
        $exif->setTiff( $tiff );
    } else {
        if ( $debugExif )$msg .= println( 'Found existing APP1 section.' );
        $tiff = $exif->getTiff();
    }
    /*
     * TIFF data has a tree structure much like a file system. There is a
     * root IFD (Image File Directory) which contains a number of entries
     * and maybe a link to the next IFD. The IFDs are chained together
     * like this, but some of them can also contain what is known as
     * sub-IFDs. For our purpose we only need the first IFD, for this is
     * where the image description should be stored.
     */
    $ifd0 = $tiff->getIfd();

    if ( $ifd0 == null ) {
        if ( $debugExif )$msg .= println( 'No IFD found, adding new.' );
        $ifd0 = new PelIfd( PelIfd::IFD0 );
        $tiff->setIfd( $ifd0 );
    }
    if ( $debugExif )$msg .= "That compleates setup, get/adjust the data $eol ";

    $tag = convertText2EeixID( $item ); // item name into tag integer
    $textThing = $ifd0->getEntry( $tag );
    if ( is_null( $textThing ) ) {
        $msg .= "tag did not exist, creae a new one $eol";
        $type = findTagtype( $tag );
        if ( $debugExif )$msg .= println( "Adding new I$tag of type $type with value $value" );
        switch ( $type ) {
            case "copyright":
                $textThing = new PelEntryCopyright( $value );
                break;
            case "ascii":
                $textThing = new PelEntryAscii( $tag, value );
                break;
            case "byte":
                $textThing = new PelEntryByte( $tag, $value );
                break;
            default:
                throw new Exception( "E#488 Unknown findtagtype for $tag" );
                break;
        }
        $ifd0->addEntry( $textThing );
        $textValue = "NULL";
    } else {
        $textValue = $textThing->getValue();
        if ( $debugExif ) $msg .= rrwUtil::print_r( $textValue, true, "found old value of $item" );
        $textThing->setValue( $value );
        if ( $debugExif )$msg .= "setting new value -- $value $eol";
    }
    if ( $debugExif )$msg .= println( "Writing file $tmpfname" );
    $fileInMemory->saveFile( $tmpfname );
    $sizeOld = filesize( $filename );
    $sizeNew = filesize( $tmpfname );
    $msg .= dumpMeta( $filename, $tmpfname );
    if ( abs( $sizeOld - $sizeNew ) < 500 ) {
        unlink( $filename );
        rename( $tmpfname, $filename );
    } else {
        if ( true ) {
            unlink( $filename );
            rename( $tmpfname, $filename );
        }
        throw new Exception( " $msg E#461 old size is $sizeOld, new size is $sizeNew,
                difference is more than 500 please check$eol file not written $eol" );
    }
    $ii = strrpos( $filename, "/" );
    $basename = substr( $filename, $ii );
    $itemname = str_replace( ".jpg", "", $basename );
    $itemname = str_replace( "_cr", "", $itemname );
    $itemname = str_replace( "_tmb", "", $itemname );
    // copyright and comment may be stored as an arrray
    $oldValue = rrwUtil::print_r( $textValue, true, "old value" );
    $comment = "$basename -- $oldValue -> $value";
    $msg .= rrwUtil::InsertIntoHistory( $itemname, $comment );

    return $msg;
}

function testperl() {
    global $gallery, $eol;
    ini_set( 'display_errors', 1 );
    ini_set( 'display_startup_errors', 1 );
    error_reporting( E_ALL );
    $msg = "";
    $msg .= SetConstants( "writeA4" );
    $msg .= "constarnts set";
    /** Create an object */
    $filename = "$gallery/a3.jpg";
    $last_line = system( 'ls', $retval );
    $msg .= "$eol last line $last_line $eol";

    $exifLastLine = system( "Exif/exiftool -s $filename" );
    $msg .= "$eol exif last line -- $exifLastLine  $eol";
    $msg .= "$eol perhaps";
    return $msg;
}

function writea5( $attr ) {
    ini_set( "display_errors", true );
    global $gallery, $eol;
    $msg = "";
    $msg .= SetConstants( "writeA4" );
    $msg .= "constarnts set";
    /** Create an object */
    $filename = "$gallery/a3.jpg";
    $filenameNew = "$gallery/a5.jpg";
    if ( file_exists( $filenameNew ) )
        unlink( $filenameNew );
    $msg .= "file unlinked $eol";
    $er = new phpExifWriter( $filename );
    $msg .= "phpExifWriter( $filename ); involed $eol";
    //    return $msg;
    $er->debug = true;
    /*
     * Add comments to the file
     */
    $er->addComment( "This is the commentss" );
    $msg .= "phpExifWriter( comment added $eol";

    /**
     * Write back the content to a file
     */
    $er->writeImage( $filenameNew );
    $msg .= "file written $eol";
    $msg .= reada3a4( $attr );
    return $msg;
}


/**
 * PEL: PHP Exif Library.
 * A library with support for reading and
 * writing all Exif headers in JPEG and TIFF images using PHP.
 *
 * Copyright (C) 2004, 2005, 2006, 2007 Martin Geisler.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program in the file COPYING; if not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor,
 * Boston, MA 02110-1301 USA
 */

/* a printf() variant that appends a newline to the output. */

function println( $fmt, $value = "" ) {
    global $eol;
    return $fmt;
    if ( !empty( $value ) )
        $fmt = sprintf( $fmt, $value );
    return "$fmt $eol";
}

function rrwPHPel( $argv ) {
    /*
     * Store the name of the script in $prog and remove this first part of
     * the command line.
     */
    global $eol;
    ini_set( 'display_errors', 1 );
    ini_set( 'display_startup_errors', 1 );
    error_reporting( E_ALL | E_STRICT );

    $msg = "";
    $debug = false;
    try {
        $msg .= SetConstants( "testPel" );
        if ( $debugExif )$msg .= println( "--------------------- emter rrwPHPel $eol" );

        $prog = array_shift( $argv );
        $error = false;

        /*
         * The next argument could be -d to signal debug mode where lots of
         * extra information is printed out when the image is parsed.
         */
        if ( isset( $argv[ 0 ] ) && $argv[ 0 ] == '-d' ) {
            Pel::setDebug( true );
            array_shift( $argv );
        }

        /* The mandatory input filename. */
        if ( isset( $argv[ 0 ] ) ) {
            $input = array_shift( $argv );
        } else {
            $error = true;
        }

        /* The mandatory output filename. */
        if ( isset( $argv[ 0 ] ) ) {
            $output = array_shift( $argv );
        } else {
            $error = true;
        }
        /* The mandatory item to be updated filename. */
        if ( isset( $argv[ 0 ] ) ) {
            $tag = array_shift( $argv );
        } else {
            $error = true;
        }
        /*
         * Usage information is printed if an error was found in the command
         * line arguments.
         */
        if ( $error ) {
            if ( $debug )$msg .= println( 'Usage: %s [-d] <input> <output> [desc]', $prog );
            if ( $debug )$msg .= println( 'Optional arguments:' );
            if ( $debug )$msg .= println( '  -d    turn debug output on.' );
            if ( $debug )$msg .= println( '  desc  the new description.' );
            if ( $debug )$msg .= println( 'Mandatory arguments:' );
            if ( $debug )$msg .= println( '  input   the input file, a JPEG or TIFF image.' );
            if ( $debug )$msg .= println( '  output  the output file for the changed image.' );
            return $msg;
        }

        /* Any remaining arguments are considered the new description. */
        $description = implode( ' ', $argv );
        if ( $debug )$msg .= println( "the new desrription '$description'$eol" );

        /*
         * We typically need lots of RAM to parse TIFF images since they tend
         * to be big and uncompressed.
         */
        ini_set( 'memory_limit', '32M' );

        /*
         * The input file is now read into a PelDataWindow object. At this
         * point we do not know if the file stores JPEG or TIFF data, so
         * instead of using one of the loadFile methods on PelJpeg or PelTiff
         * we store the data in a PelDataWindow.
         */
        if ( $debug )$msg .= println( 'Reading file "%s".', $input );
        $buffer = file_get_contents( $input );
        if ( $debug )$msg .= println( "got " . strlen( $buffer ) . "bytes from the files" );
        $data = new PelDataWindow( $buffer );

        /*
         * The static isValid methods in PelJpeg and PelTiff will tell us in
         * an efficient maner which kind of data we are dealing with.
         */
        if ( PelJpeg::isValid( $data ) ) {
            /*
             * The data was recognized as JPEG data, so we create a new empty
             * PelJpeg object which will hold it. When we want to save the
             * image again, we need to know which object to same (using the
             * getBytes method), so we store $jpeg as $file too.
             */
            $jpeg = $file = new PelJpeg();

            /*
             * We then load the data from the PelDataWindow into our PelJpeg
             * object. No copying of data will be done, the PelJpeg object will
             * simply remember that it is to ask the PelDataWindow for data when
             * required.
             */
            $jpeg->load( $data );

            /*
             * The PelJpeg object contains a number of sections, one of which
             * might be our Exif data. The getExif() method is a convenient way
             * of getting the right section with a minimum of fuzz.
             */
            $exif = $jpeg->getExif();

            if ( $exif == null ) {
                /*
                 * Ups, there is no APP1 section in the JPEG file. This is where
                 * the Exif data should be.
                 */
                if ( $debug )$msg .= println( 'No APP1 section found, added new.' );

                /*
                 * In this case we simply create a new APP1 section (a PelExif * object) and adds it to the PelJpeg object.
                 */
                $exif = new PelExif();
                $jpeg->setExif( $exif );

                /* We then create an empty TIFF structure in the APP1 section. */
                $tiff = new PelTiff();
                $exif->setTiff( $tiff );
            } else {
                /*
                 * Surprice, surprice: Exif data is really just TIFF data! So we
                 * extract the PelTiff object for later use.
                 */
                if ( $debug )$msg .= println( 'Found existing APP1 section.' );
                $tiff = $exif->getTiff();
            }
        } elseif ( PelTiff::isValid( $data ) ) {
                /*
                 * The data was recognized as TIFF data. We prepare a PelTiff
                 * object to hold it, and record in $file that the PelTiff object is
                 * the top-most object (the one on which we will call getBytes).
                 */
                $tiff = $file = new PelTiff();
                /* Now load the data. */
                $tiff->load( $data );
            } else {
                /*
                 * The data was not recognized as either JPEG or TIFF data.
                 * Complain loudly, dump the first 16 bytes, and exit.
                 */
                if ( $debug )$msg .= println( 'Unrecognized image format! The first 16 bytes follow:', "" );
                PelConvert::bytesToDump( $data->getBytes( 0, 16 ) );
                return $msg;;
            }
            /*
             * TIFF data has a tree structure much like a file system. There is a
             * root IFD (Image File Directory) which contains a number of entries
             * and maybe a link to the next IFD. The IFDs are chained together
             * like this, but some of them can also contain what is known as
             * sub-IFDs. For our purpose we only need the first IFD, for this is
             * where the image description should be stored.
             */
        $ifd0 = $tiff->getIfd();

        if ( $ifd0 == null ) {
            /*
             * No IFD in the TIFF data? This probably means that the image
             * didn't have any Exif information to start with, and so an empty
             * PelTiff object was inserted by the code above. But this is no
             * problem, we just create and inserts an empty PelIfd object.
             */
            if ( $debug )$msg .= println( 'No IFD found, adding new.' );
            $ifd0 = new PelIfd( PelIfd::IFD0 );
            $tiff->setIfd( $ifd0 );
        }

        if ( $debug )$msg .= println( "That compleates setup, get/adjust the data $eol " );
        /*
         * Each entry in an IFD is identified with a tag. This will load the
         * ImageDescription entry if it is present. if the IFD does not
         * contain such an entry, null will be returned.
         */
        if ( $debug )$msg .= println( "  -------------------------------- $eol" );
        $textList = array( "copyright", "desc", "date", "keyword" ); // "width", "height");
        foreach ( $textList as $text ) {
            $tagL = convertText2EeixID( $text );
            $textThing = $ifd0->getEntry( $tagL );
            if ( is_null( $textThing ) ) {
                if ( $debug )$msg .= println( "did not find an entry  for $text" );
            } else {
                $textValue = $textThing->getValue();
                if ( $debug )$msg .= println( $textThing . rrwUtil::print_r( $textValue, true, "$text" ) );
            }
        }
        if ( $debug )
            if ( $debug )$msg .= println( "  -------------------------------- $eol" );

        $desc = $ifd0->getEntry( $tag );
        if ( $debug )
            if ( $debug )$msg .= println( "for $tag found the $desc $eol " );
            //   return $msg;
            //     We need to check if the image already had a description stored. 
        if ( $desc == null ) {
            //        The was no description in the image. 


            //   * In this case we simply create a new PelEntryAscii object to hold
            //   * the description. The constructor for PelEntryAscii needs to know
            //   * the tag and contents of the new entry.
            $type = findTagtype( $tag );
            if ( $debug )$msg .= println( "Adding new I$tag of type $type with $description" );
            switch ( $type ) {
                case "copyright":
                    $desc = new PelEntryCopyright( description );
                    break;
                case "ascii":
                    $desc = new PelEntryAscii( $tag, $description );
                    break;
                case "byte":
                    $desc = new PelEntryByte( $tag, $description );
                    break;
                default:
                    throw new Exception( "E#488 Unknown findtagtype for $tag" );
                    break;
            }


            //    * This will insert the newly created entry with the description
            //    * into the IFD.

            $ifd0->addEntry( $desc );
            //        if ("copyright" == $type)
            //          $desc->setValue($description );

        } else {
            //     An old description was found in the image. 
            $oldDescription = rrwUtil::print_r( $desc->getValue(), true, "existng" );
            if ( $debug )$msg .= println( "IMAGE_DESCRIPTION entry from $oldDescription" );
            if ( $debug )$msg .= println( "IMAGE_DESCRIPTION entry TO &nbsp; " . $description );

            // The description is simply updated with the new description. 

        }
        /*
         * At this point the image on disk has not been changed, it is only
         * the object structure in memory which represent the image which has
         * been altered. This structure can be converted into a string of
         * bytes with the getBytes method, and saving this in the output file
         * completes the script.
         */
        if ( $debug )$msg .= println( 'Writing file "%s".', $output );
        $file->saveFile( $output );
        if ( $debug )$msg .= println( "finished with test Pel $eol" );
        if ( $debug )$msg .= println( "  -------------------------------- end PHPel  $eol" );

    } catch ( Exception $ex ) {
        $msg .= "E#400 xxx catch "; // . $ex->get_message();
    }
    return $msg;
}

function convertText2EeixID( $text ) {
    // codes from https://www.exiftool.org/TagNames/EXIF.html
    global $eol, $errorBeg, $errorEnd;
    switch ( $text ) {
        case "copyright":
            return PelTag::COPYRIGHT;
        case "desc":
            return 0x010e; // PelTag::IMAGEDESCRIPTION;
            //    ImageDescription
        case "datetimeoriginal":
            return 0x0132; // Peltag::DATETIMEOROGINAL ;  // 0x0132 //  0x9003;
        case "keyword":
            return 0x9c9e; // (WindowsXPKeywords)
        case "width":
            return 0x0100; //"IMAGEWIDTH";  
        case "height":
            return 0x0101; // 0xbc81
        case "COMPUTED":
            return "COMPUTED"; // 0xbc81
        default:
            throw new Exception( "$errorBeg E#484 Invalid Tag Nameof '$text' $errorEnd" );
    }
    // assert never  get here.
}

function findTagtype( $tag ) {
    switch ( $tag ) {
        case PelTag::COPYRIGHT:
            return "copyright";
        case 0x010e: // PelTag::IMAGEDESCRIPTION;
            return "ascii";
        case 0x9c9e: // (WindowsXPKeywords)
            return "byte";

            //       case PelTag::GPS_LATITUDE:
            //        case PelTag::GPS_LONGITUDE:
            //        case GPS_ALTITUDE:
            //            return "rational";
        case "date":
            return "int"; // Peltag::DATETIMEOROGINAL ;  // 0x0132 //  0x9003;
        case "width":
        case "height":
            return "no";
        case "COMPUTED":
            return "COMPUTED"; // 0xbc81
        default:
            throw new Exception( "E#484 Invalid Tag Nameof '$text'" );
    }
}


function testpel2( $attr ) {
    global $gallery, $eol;
    $msg = "";
    $msg .= SetConstants( "testPel" );
    // lnowntags for the 5th parameter 
    $input_file = "/home/pillowan/www-shaw-weil-pictures/photos/028682_ms5-73_011798_cr.jpg";
    //  $output_file = "/home/pillowan/www-shaw-weil-pictures/photos/028682_ms5-73_011798_cr.jpg";

    //  $input_file = "$gallery/a3.jpg";
    $output_file = "$gallery/a5.jpg";
    // Copyright info to add
    $copyright = "Mary Shaw";
    $msg .= changeCopyRight( $input_file, $output_file, $copyright );
    $msg .= dumpMeta( $input_file, $output_file );
    return $msg;
}

function changeItem( $input_file, $output_file, $item, $newVale ) {
    global $gallery, $eol;
    $msg = "";

    $tag = convertText2EeixID( $item );
    $argv = array( "rrwPHPel",
        //        "-d",
        $input_file,
        $output_file,
        $tag,
        $newVale,
    );
    $msg .= rrwPHPel( $argv );
    return $msg;
}

function testpel() {
    global $gallery, $eol;
    $msg = "";
    $msg .= SetConstants( "testPel" );
    $output_file = "$gallery/a5.jpg";
    $output_file6 = "$gallery/a6.jpg";
    foreach ( array( "$gallery/a3.jpg",
            "$gallery/US 422 underpass 422-142235.jpg" ) as $input_file ) {
        $msg .= changeItem( $input_file, $output_file, "desc",
            "a change in description" );
        $msg .= dumpMeta( $input_file, $output_file );
        $msg .= changeItem( $input_file, $output_file, "copyright",
            "a change in copyright" );
        $msg .= dumpMeta( $input_file, $output_file );
        $msg .= changeItem( $input_file, $output_file, "keyword",
            "a change in keyword in a5" );
        $msg .= dumpMeta( $input_file, $output_file );
        $msg .= changeItem( $output_file, $output_file6, "keyword",
            "a change in keyword in a6" );
        $msg .= dumpMeta( $output_file, $output_file6 );
    }
    return $msg;
}


function testpel3() {
    global $photoPath, $eol;
    $msg = "";
    try {
        $msg .= SetConstants( "testPel" );
        $output_file = "$photoPath/a5.jpg";
        $input_file = "$photoPath/a3.jpg";
        $tag = convertText2EeixID( "desc" ); // works
        $tag = convertText2EeixID( "copyright" ); // works on a2=4
        $tag = convertText2EeixID( "keyword" ); // works
        $tag = convertText2EeixID( "desc" ); // works
        $tag = convertText2EeixID( "keyword" ); // fails
        $tag = convertText2EeixID( "copyright" ); // works

        $argv = array( "rrwPHPel",
            //       "-d",
            $input_file,
            $output_file,
            $tag,
            "a change in the data ",
        );
        $msg .= rrwPHPel( $argv );
    } catch ( Exception $ex ) {
        $msg .= "E#400 testpel3 catch " . $ex->get_message();
    }

    $msg .= dumpMeta( $input_file, $output_file );
    return $msg;
}

function dumpMeta( $file1 = "", $file2 = "" ) {
    global $eol, $errorBeg, $errorEnd;
    $msg = "";
    if ( "" == $file1 )
        $file1 = fetchparameterString( "file" );
    if ( "" == $file2 )
        $file2 = fetchparameterString( "file2" );
    $temp1 = exif_read_data( $file1 );
    $temp1 = rrwUtil::print_r( $temp1, true, " file $file1" );
    $iiComment = strpos( $temp1, "UserCommentEncoding" );
    if ( $iiComment > 1 )
        $temp1 = substr( $temp1, 0, $iiComment + 25 );
    $temp1size = filesize( $file1 );
    if ( !file_exists( $file2 ) ) {
        $msg .= "E#460 file does not exist $eol";
        $temp2 = "$eol $file2 $eol $errorBeg E#460 file does not exist $errorEnd";
        $temp2Size = 0;
    } else {
        $temp2 = exif_read_data( $file2 );
        $temp2 = rrwUtil::print_r( $temp2, true, " file $file2" );
        $iiComment = strpos( $temp2, "UserCommentEncoding" );
        if ( $iiComment > 1 )
            $temp2 = substr( $temp2, 0, $iiComment + 25 );
        $temp2size = filesize( $file2 );
    }
    $msg .= "<table>" . rrwFormat::HeaderRow( "$file1 - $temp1size
                        bytes", "$file2 - $temp2size bytes" );
    $msg .= rrwFormat::CellRow( $temp1, $temp2 );
    $msg .= "</table>";
    return $msg;
}

function reada3a4( $attr ) {
    // reads and displays the meta data for file f1, f2
    ini_set( "display_errors", true );
    global $gallery, $eol;
    $msg = "";
    $msg .= SetConstants( "testPel" );
    $input_file = "$gallery/a3.jpg";
    $output_file = "$gallery/a5.jpg";
    $msg .= dumpMeta( $input_file, $output_file );
    return $msg;
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
add_shortcode( "rrwPicSubmission", array( "rrwPicSubmission", "showForm" ) );
add_shortcode( "upload", "uploadProcessDire" );

add_shortcode( "insertdire", "insertdire" );
add_shortcode( "perl", "testperl" );
add_shortcode( "addtag", "addtag" );
add_shortcode( "reada3a4", "reada3a4" );
add_shortcode( "writea5", "writea5" );
add_shortcode( "testPel", "testPel3" );

require 'plugin_update_check.php';
$MyUpdateChecker = new PluginUpdateChecker_2_0(
    'http://pluginserver.royweil.com/roys-picture-processng.php',
    __FILE__,
    'roys-very-picture-processng',
    1
);


?>
