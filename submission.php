<?php

class rrwPicSubmission {
    public static function showForm( $attr ) {
        global $eol, $errorBeg, $errorEnd;
        $msg = "";

        include "setConstants.php";
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
        global $uploadPath;
        $msg = "";

        try {
            $debugsubmit = false;
            if ($debugsubmit) $msg .= rrwUtil::print_r( $_FILES, true, "Files" );
            $photographer = rrwUtil::fetchparameterString( "photographer" );
            if ( empty( $photographer ) )
                throw new RuntimeException( 'you must selct a photographer.' );

            if ( !isset( $_FILES[ 'inputfile' ][ 'error' ] ) ||
                is_array( $_FILES[ 'inputfile' ][ 'error' ] )
            ) {
                $err .= "Something is wrong with the file paramenters.
                Did you forget to select one? ";
                throw new RuntimeException( $err );
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
            $DataFilename = $matchs[ 0 ];
            $newfile = "$uploadPath/$DataFilename.$ext";
            if ( !move_uploaded_file(
                    $_FILES[ 'inputfile' ][ 'tmp_name' ], $newfile ) ) {
                throw new RuntimeException( 'Failed to move uploaded file.' );
            }
            // ---- file to be created in upload, now create database record
            $Insert = array( "photographer" => $photographer,
                "filename" => $DataFilename,
            );
            $cntchaged = $wpdbExtra->replace( $rrw_photos, $Insert );
            $msg .= rrwUtil::InsertIntoHistory( $DataFilename, "uploaded " );
            $msg .= "File was uploaded successfully to $newfile $eol";
            if ($debugsubmit) $msg .= "lets process upload dire$eol";
            $msg .= uploadProcessDire::upload();

        } catch ( RuntimeException $e ) {
            $msg .= $errorBeg . $e->getMessage() . $errorEnd;
        }
        return $msg;
    }


    private static function displayform() {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_photographers;
        $msg = "";

        $photographer = rrwUtil::fetchparameterString( "photographer" );
        if (empty($photographer)) {
            if (array_key_exists("photographer", $_COOKIE) && 
                                 ! empty($_COOKIE["photographer"]))
            $photographer = $_COOKIE["photographer"];
        }
        $_COOKIE["photographer"] = $photographer;
        $msg .= "<form  method='post' enctype=\"multipart/form-data\" > ";
        $sqltaker = "select photographer from $rrw_photographers
                    order by photographer";
        $recs = $wpdbExtra->get_resultsA( $sqltaker );
        $msg .= "<select name='photographer' id='photographer' 
                    onchange='if (this.selectedIndex) submit();'' >\n
                    <option value=-1> Select the photographer</option>";
        foreach ( $recs as $rec ) {
            $name = $rec[ "photographer" ];
            $msg .= "<option value='$name' ";
            if ( $photographer == $name )
                $msg .= "selected='selected' ";
            $msg .= ">$name</option>\n";
        }
        $msg .= "</select> if not in the list  
                <a href='display-photographers/?action=new' >
                Add a new photographer</a> $eol$eol";
        if ( empty( $photographer ) ) {
            $msg .= "</form>";
            return $msg;
        }
        $msg .= "<input type='file' name='inputfile' id='inputfile' />
                &nbsp; <input type='checkbox' name='replacephoto' id='replacephoto'> repace existing image</input>$eol$eol";

        $msg .= "<input type='submit' name='submit' id='submit'
        value='Upload the picture, Display it so I can select the tags' /> $eol
        </form> $eol;";
        $msg .= "not yet";
        return $msg;
    } // end function
} // end class
?>
