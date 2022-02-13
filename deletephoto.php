<?php
function doit() {
    $eol = "<br />\n";
    $db = openDatabase_ezsql();
    $photoTable = $_SESSION[ 'tableprefix' ] . "photos";
    $keywordTable = $_SESSION[ 'tableprefix' ] . "keywords";

    $filename = fetchParameter( "del2" );
    $del3 = fetchParameter( "del3" );
    if ( strcmp( $filename, $del3 ) != 0 ) {
        print "input parameters do not match. photo not deleted";
        return;
    }
    if ( $filename == "" ) {
        print "Missing or invalid photo name";
        return;
    }
        $sql = "select photo_id, filename from $photoTable 
                    where filename = '$filename'";
    print( "\n<!-- sql is $sql -->\n" );
    $recs = $db->query($sql);
    $recset = $recs->fetch_array( MYSQLI_ASSOC );
    $deleteTest = $recset[ "filename" ];
    if ( empty( $filename ) ) {
        print "Sql $sql did not find file name in the database $eol";
    }

    $sql = "delete from $keywordTable where keywordFilename = '$filename'";
    print( "$sql  $eol" );
    $db->query( $sql );

    $sql = "delete from $photoTable where Filename ='$filename'";
    print( "$sql  $eol" );
     $db->query( $sql );

  
    $filename1 = $_SERVER[ 'DOCUMENT_ROOT' ] . "/photo/thumbs/${filename}_tmb.jpg";
    $filename2 = $_SERVER[ 'DOCUMENT_ROOT' ] . "/photo/$filename.jpg";
    if ( true ) {
    print "photo '$filename' deleted from database. $eol 
            Can be recovered by doing Add Photos, if not deleted from server$eol 
            $filename1 $eol $filename2 $eol ";
    } else {
        print "$eol Deleting file $filename1 $eol";
        print "Deleting file $filename2 $eol";
        unlink( "$filename1" );
        unlink( "$filename2" );
        print "Did <strong>not</strong> delete the high resolution version if present on server$eol";
    }

}

?>
