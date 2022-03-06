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
 
  * Version: 2.0.88
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
    public static function copyMeta( $filename, $outputPath ) {
        global $eol, $errorBeg, $errorEnd;
        $msg = "";
        $debugExif = true;
        ini_set( "displaoy_errors", true );
        error_reporting( E_ALL | E_STRICT );
        try {
            if ( $debugExif )$msg .= "input file - $filename $eol 
                                    output file - $outputPath $eol";
            // pushto     
            $contents = file_get_contents( $filename );
            if ( $debugExif )$msg .= " got contents of $filename $eol";
            $ss = strlen( $contents );
            if ( $debugExif )$msg .= " contents size is $ss $eol";

            $jpeg = $fileInMemory = new PelJpeg();
            if ( $debugExif )$msg .= " new PelJpeg() worked  $eol";
            $jpeg->loadfile( $filename );
            if ( $debugExif )$msg .= " loadfile worked  $eol";
            //       $exif = $jpeg->getExif();
            if ( $debugExif )$msg .= " getExif worked  $eol";
            $exif = exif_read_data( $filename, 0, true );
            if ( $debugExif )$msg .= " exif_read_data worked  $eol";
            if ( is_null( $exif ) )
                throw new Exceprion( "$errorBeg e#491 inputPel->getExif() failed $errorEnd" );
            $cnt = 0;
            foreach ( $exif as $key => $section ) {
                $cnt++;
                if ( $cnt > 50 )
                    break;
                foreach ( $section as $name => $val ) {
                    $cnt++;
                    if ( $cnt > 50 )
                        break;
                    $msg .= "$key.$name: $val $eol";
                }
            }
            if ( $debugExif )$msg .= " display  $eol";
            $jpeg->clearExif();
            if ( $debugExif )$msg .= " clearExif worked  $eol";
            $fileInMemory->savefile( $outputPath );
            if ( $debugExif )$msg .= " save file worked  $eol";

            return $msg;
            /* fatial error in save
                     if ( $debugExif )$msg .= " display  $eol";
                        $exifNew = new PelExif();
                        if ( $debugExif )$msg .= " new exif  worked  $eol";
                        $jpeg->setExif( $exifNew );
                        if ( $debugExif )$msg .= " setExif worked  $eol";
                        $fileInMemory->savefile( $outputPath );
                */
            $resultr1 = $outputPath->setExif();
            if ( $debugExif ) print "called setExif empty $eol ";
            if ( is_null( $resultr1 ) )
                throw new Exceprion( "$errorBeg e#492 outputPath->setExif() failed $errorEnd" );
            $resullt2 = $outputPel->saveFile( $outputPath );

            if ( $debugExif ) print "got exif$eol ";
            if ( is_null( $resullt2 ) )
                throw new Exceprion( "$errorBeg e#492 outputPel->saveFile(() failed $errorEnd" );
        } catch ( Exception $ex ) {
            $msg .= "E#400 xxx catch ";
        }
        return "$msg Meta copied from $filename $eol $outputPath $eol";
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


function readexifItem( $filename, $item, & $msg ) {
    global $eol, $errorBeg, $errorEnd;
    $debugExif = false;
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
    $exif = rrw_exif_read_data( $filename );
    if ( array_key_exists( $item, $exif ) )
        return $exif[ $tem ];
    return "&lt;Missing&gt;";
}

function pushToImage( $filename, $item, $value ) {
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
        if ( $debugExif )$msg .= dumpMeta( $filename, $tmpfname );
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
    $debug = true;
    try {
        $msg .= SetConstants( "testPel" );
        if ( $debug )$msg .= println( "--------------------- emter rrwPHPel $eol" );
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
        if ( $debug )$msg .= "inputfile - $input, $eol outputfile $output, $eol
              item = $tag, new value - $description ";
        /*
         * We typically need lots of RAM to parse TIFF images since they tend
         * to be big and uncompressed.
         */
        ini_set( 'memory_limit', '120M' );
        /*
         * The input file is now read into a PelDataWindow object. At this
         * point we do not know if the file stores JPEG or TIFF data, so
         * instead of using one of the loadFile methods on PelJpeg or PelTiff
         * we store the data in a PelDataWindow.
         */
        if ( $debug )$msg .= "Reading file $input ...";
        $buffer = file_get_contents( $input );
        if ( $debug )$msg .= println( "got " . strlen( $buffer ) . "bytes from the files $eol" );

        $data = new PelDataWindow( $buffer );
        $msg .= "GOT DATA $eol";

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
            if ( $debug )$msg .= "got the exif $eol ";

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
            if ( $debug )$msg .= println( 'No IFD found, adding new.$eol' );
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
        $textList = array( "copyright", "description", "date", "keyword" ); // "width", "height");
        foreach ( $textList as $text ) {
            $tagL = convertText2EeixID( $text );
            $textThing = $ifd0->getEntry( $tagL );
            if ( is_null( $textThing ) ) {
                if ( $debug )$msg .= println( "E#702 did not find an entry  for $text $eol" );
            } else {
                $textValue = $textThing->getValue();
                if ( $debug )$msg .= println( $textThing . rrwUtil::print_r( $textValue, true, "$text" ) );
            }
        }
        if ( $debug )$msg .= println( "  about to get entry $eol" );
        return $msg;
        $desc = $ifd0->getEntry( $tag );
        if ( $debug )
            if ( $debug )$msg .= println( "for $tag found the $desc $eol " );
            //     We need to check if the image already had a description stored. 
        if ( $desc == null ) {
            //        The was no description in the image. 
            //   * In this case we simply create a new PelEntryAscii object to hold
            //   * the description. The constructor for PelEntryAscii needs to know
            //   * the tag and contents of the new entry.
            $type = findTagtype( $tag );
            if ( $debug )$msg .= println( "Adding new $tag of type $type with $description $eol " );
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
            if ( $debug )$msg .= println( "IMAGE_DESCRIPTION entry from $oldDescription" ) . $eol;
            if ( $debug )$msg .= println( "IMAGE_DESCRIPTION entry TO &nbsp; " . $description ) . $eol;
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
        $file->saveFile( $output ) . $eol;
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
    // print " convertText2EeixID( $text )  $eol ";
    switch ( $text ) {
        case "artist":
            return 0x013b;
        case "comment":
            return 0x9c9c;
        case "copyright":
            print "found copyright $eol ";
            return 0x8298;
        case "ImageDescription":
        case "desc":
        case "decription":
            return 0x010e;
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
    global $eol, $errorBeg, $errorEnd;
    // print " findTagtype( $tag ) $eol";
    switch ( $tag ) {
        // foramt names in class PelFormat, 
        //   should match switch ( $type ) abour line 326
        case 0x8298: // copyright
            return "Copyright";
        case 0x13B: // artist
        case 0x010e: // Image Description
        case 0x0132: // modify date
        case 0x9003: //Datetime original
        case 0x9004: //CreateDate
            return "ascii"; // string
            //     case "keywords":
        case 0x9c9c: // comment
        case 0x9c9d: // Author
        case 0x9c9e: // (WindowsXPKeywords)
        case 0x9c9f: // Subject
            return "byte"; // int8u
            //       case PelTag::GPS_LATITUDE:
            //        case PelTag::GPS_LONGITUDE:
            //        case GPS_ALTITUDE:
            //            return "rational";
            /*    case "date":
            return "int"; // Peltag::DATETIMEOROGINAL ;  // 0x0132 //  0x9003;
        case "width":
        case "height":
            return "no";
        case "COMPUTED":
            return "COMPUTED"; // 0xbc81
        case "datetimedigitized":
            return "Ascii";
*/
        default:
            $hex = dechex( $tag );
            throw new Exception( "E#695 looking for typeof exif[$tag]
            of exif[$hex]" );
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
    global $photoPath, $eol;
    $msg = "";
    $msg .= SetConstants( "testPel" );
    $output_file = "$photoPath/a5.jpg";
    $output_file6 = "$photoPath/a6.jpg";
    foreach ( array( "$photoPath/a3.jpg", ) as $input_file ) {
        $output_file = str_replace( ".jpg", "", $input_file ) . "new.jpg";
        $msg .= changeItem( $input_file, $output_file, "artist",
            "a change in description" );
        $msg .= dumpMeta( $input_file, $output_file );
        $msg .= changeItem( $input_file, $output_file, "copyright",
            "a change in copyright" );
        $msg .= dumpMeta( $input_file, $output_file );
        $msg .= changeItem( $input_file, $output_file, "keyword",
            "a change in keyword in a5" );
        $msg .= dumpMeta( $input_file, $output_file );
        $msg .= changeItem( $output_file, $output_file, "keyword",
            "a change in keyword in a6" );
        $msg .= dumpMeta( $output_file, $output_file );
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
    $temp1 = rrw_exif_read_data( $file1 );
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
        $temp2 = rrw_exif_read_data( $file2 );
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

function rrw_exif_read_data( $file ) {
    error_reporting( E_ALL ^ E_WARNING );
    $exifArray = exif_read_data( $file );
    error_reporting( E_ALL || E_STRICT );
    return $exifArray;
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
add_shortcode( "fixtasklist", array( "fixTaskList", "showlist" ) );
add_shortcode( "rrwPicSubmission", array( "rrwPicSubmission", "showForm" ) );
add_shortcode( "upload", array( "uploadProcessDire", "upload" ) );
add_shortcode( "insertdire", "insertdire" );
add_shortcode( "perl", "testperl" );
add_shortcode( "addtag", "addtag" );
add_shortcode( "reada3a4", "reada3a4" );
add_shortcode( "writea5", "writea5" );
require 'plugin_update_check.php';
$MyUpdateChecker = new PluginUpdateChecker_2_0(
    'http://pluginserver.royweil.com/roys-picture-processng.php',
    __FILE__,
    'roys-very-picture-processng',
    1
);
?>
