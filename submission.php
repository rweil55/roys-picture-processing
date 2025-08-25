<?php
/*		Freewheeling Easy Mapping Application
 *		A collection of routines for display of trail maps and amenities
 *		copyright Roy R Weil 2019 - https://royweil.com
 */
class rrwPicSubmission {
    // presents a photographer dropdown, a choose file box, replace checkbox
    // rejects if no replace and exiting meta data
    // moves the chosen file to the upload folder
    //
    public static function showForm( $attr ) {
        global $eol, $errorBeg, $errorEnd;
        $msg = "";
        if ( !freewheeling_fixit::allowedSubmit() ) {
            $msg .= "$errorBeg  You must be logined in have the necessary
                priviledges to submit photos $errorEnd";
            return $msg;
        }
        $submit = rrwUtil::fetchparameterString( "submit" );
        if ( empty( $submit ) )
            $msg .= self::displayform();
        else
            $msg .= self::uploadfile();
        return $msg;
    }
    private static function uploadfile() {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photographers, $rrw_photos;
        global $uploadPath, $highresPath, $photoPath;
        $msg = "";
        $debug = false;
        try {
            $debugsubmit = false;
            if ( $debugsubmit )$msg .= rrwUtil::print_r( $_FILES, true, "Files" );
            if ( !freewheeling_fixit::allowedSubmit() ) {
                $msg .= "$errorBeg  You must be logined in have the necessary
                priviledges to submit photos $errorEnd";
                return $msg;
            }
            $photographer = rrwPara::String( "photographer" );
            $replacephoto = rrwPara::String( "replacephoto" );
            if ( empty( $photographer ) )
                throw new RuntimeException( 'you must selct a photographer.' );
            if ( !isset( $_FILES[ 'inputfile' ][ 'error' ] ) ||
                is_array( $_FILES[ 'inputfile' ][ 'error' ] )
            ) {
                $errMsg = "Something is wrong with the file paramenters.
                Did you forget to select one? ";
                throw new RuntimeException( $errMsg );
                return $msg;
            }
            // Check $_FILES['inputfile']['error'] value.
            switch ( $_FILES[ 'inputfile' ][ 'error' ] ) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException( 'No file sent.' );
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException( 'Exceeded filesize limit.' );
                default:
                    throw new RuntimeException( 'Unknown errors.' );
            }
            // You should also check filesize here.
            if ( $_FILES[ 'inputfile' ][ 'size' ] > 10000000 ) {
                throw new RuntimeException( 'Exceeded filesize limit.' );
            }
            // DO NOT TRUST $_FILES['inputfile']['mime'] VALUE !!
            // Check MIME Type by yourself.
            $finfo = new finfo( FILEINFO_MIME_TYPE );
            if ( false === $ext = array_search(
                    $finfo->file( $_FILES[ 'inputfile' ][ 'tmp_name' ] ),
                    array(
                        'jpg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                    ),
                    true
                ) ) {
                throw new RuntimeException( 'file format needs to be .jpg or .png.' );
            }
            $pregResults = preg_match( "/[-a-zA-z0-9 _]*/",
                $_FILES[ 'inputfile' ][ 'name' ],
                $matchs );
            if ( 1 != count( $matchs ) )
                throw new RuntimeException( "file name can consist of only
                letters, numbers, and spaces" );
            $photoname = $matchs[ 0 ];
            $photoname = strtolower( $photoname );
            $photoname = str_replace( "+", "-", $photoname );
            $highresShortname = "$photoname.$ext";
            $Fullfileupload = "$uploadPath/$highresShortname";
            $FullfileHighRes = "$highresPath/$highresShortname";
            if ( false !== strpos( $highresShortname, "_cr.jpg" ) ) {
                $msg .= "$errorBeg _cr.jpg not allowed.
                            $errorEnd select the high resolution verion ";
                $msg .= freewheeling_fixit::filelike(
                    array( "partial" => $photoname ) );
                return $msg;
            }
            // see if metat data exists
            $sqlexists = "select photographer, uploaddate
                from $rrw_photos where photoname = '$photoname' ";
            $recs = $wpdbExtra->get_resultsA( $sqlexists );
            if ( 1 == $wpdbExtra->num_rows ) {
                // have meta date, update it
                if ( 'on' == $replacephoto ) {
                    $msg .= "Per your request existing photo will be replaced $eol";
                } else {
                    $dateMod = $recs[ 0 ][ "uploaddate" ];
                    $msg .= "$errorBeg $eol E#221 $highresShortname 
                    was uploaded previously on $dateMod. 
                    You must check the box to allow the replacement $errorEnd
                    $sqlexists $eol";
                    return $msg;
                }
                $updateData[ "uploaddate" ] = date( "Y-m-d" );
                $updateData[ "highresShortname" ] = $highresShortname;
                $filephotographer = $recs[ 0 ][ "photographer" ];
                if ( $filephotographer != $photographer ) {
                    $updateData[ "photographer" ] = $photographer;
                    $msg .= rrwUtil::InsertIntoHistory( $photoname, "
                                        new photograper photographer" );
                }
                $cntchaged = $wpdbExtra->update( $rrw_photos, $updateData,
                    array( "photoname" => $photoname ) );
                if ( 1 != $cntchaged )
                    $msg .= "$errorBeg E#160 meta data did not update 
                                $errorEnd";
            } else {
                // no meta data
                $Insert = array(
                    "photographer" => $photographer,
                    "photoname" => $photoname,
                    "uploaddate" => date( "Y-m-d" ),
                    "highresShortname" => $highresShortname,
                );
                $cntchaged = $wpdbExtra->insert( $rrw_photos, $Insert );
                $msg .= rrwUtil::InsertIntoHistory( $photoname, "
                                        new photograper photographer" );
            }
            // meta data now set, move the file
            if ( !move_uploaded_file(
                    $_FILES[ 'inputfile' ][ 'tmp_name' ], $Fullfileupload ) ) {
                throw new RuntimeException( 'Failed to move uploaded file.' );
            }
            $msg .= rrwUtil::InsertIntoHistory( $photoname, "uploaded " );
            $msg .= "File was uploaded successfully to $Fullfileupload $eol";
            if ( $debugsubmit )$msg .= "lets process upload dire$eol";
            $attr = array( "uploadshortname" => $highresShortname );
            if ( $debug )$msg .= rrwUtil::print_r( $attr, true, "parameters to upload" );
            $msg .= uploadProcessDire::upload( $attr );
        } catch ( RuntimeException $e ) {
            $msg .= $errorBeg . $e->getMessage() . $errorEnd;
        }
        return $msg;
    }
    public static function displayform() {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photographers;
        $msg = "";
        $photographer = rrwUtil::fetchparameterString( "photographer" );
        $ip = $_SERVER[ 'REMOTE_ADDR' ];
        if ( false )$msg .= "IP address == $ip $eol";
        if ( empty( $photographer ) && ( "72.95.243.124" == $ip ) )
            $photographer = "Mary Shaw";
        if ( empty( $photographer ) ) {
            if ( array_key_exists( "photographer", $_COOKIE ) &&
                !empty( $_COOKIE[ "photographer" ] ) )
                $photographer = $_COOKIE[ "photographer" ];
        }
        $_COOKIE[ "photographer" ] = $photographer;
        $msg .= "<form  method='post' action='/submission/' enctype=\"multipart/form-data\" > ";
        $msg .= "<strong>Photogrpher:</strong>" .
        rrwFormat::selectBox( $wpdbExtra, "$rrw_photographers",
            "photographer", $photographer, "photographer" );
        $msg .= "</select> if not in the list  
                <a href='display-photographers/?action=new' >
                Add a new photographer</a> $eol$eol";
        $msg .= "<input type='file' name='inputfile' id='inputfile' />
                &nbsp; <input type='checkbox' name='replacephoto' id='replacephoto'> repace existing image</input>$eol$eol";
        $msg .= "<input type='submit' name='submit' id='submit'
        value='Upload the picture, Display it so I can select the keywords' /> $eol
        </form> $eol";
        return $msg;
    } // end function
} // end class
