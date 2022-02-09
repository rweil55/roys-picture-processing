<?php

function keywordEmpty( $photoname ) {
    global $wpdbExtra, $photoDB, $rrw_keywords;
    global $eol, $errorBeg, $errorEnd;
    $msg = "";
    
    $debug = false;
    $sql = "delete from $rrw_keywords where keywordfilename = '$photoname'";
    if ( $debug ) $msg .= "keywordEmpty: $sql $eol";
    $rec = $wpdbExtra->query( $sql);
    return $msg;
}

function keywordCommaList( $keywords, $filename ) { 
   global $eol, $errorBeg, $errorEnd;
    $msg = "";
    
   $debug = false;
    if ( $debug ) $msg .= " keywordCommaList( $keywords, $filename ) $eol";
    $k = $keywords;
    $keywordList = explode( ',', $k );
    if ( $debug ) $msg .= rrwUtil::$msg .=_r($keywordList, true, "keywordlist ");
    foreach ( $keywordList as $newkey ) {
        $newkey = trim( $newkey );
        if($debug ) $msg .= "Newkey = $newkey $eol";
        if ( empty( $newkey ) )
            continue;
        $temp = keywordInsert( $filename, $newkey );
        if ( $debug ) $msg .= "after insert $temp $eol ";
    }
    return $msg;
} // end process comma list

function keywordInsert( $filename, $keyword ) {
    global $wpdbExtra, $rrw_keywords, $rrw_photos;
    global $eol, $errorBeg, $errorEnd;
    $msg = "";
    
    $debug = false;
    if ( $debug ) $msg .= "keywordInsertxx( $filename, $keyword ) $eol";
    $sqlAlready = "select * from $rrw_keywords where keyword = '$keyword'
            and keywordfilename = '$filename'";
    if ($debug) $msg .= "$sqlAlready $eol ";
    $recs = $wpdbExtra->query( $sqlAlready );
    if ( 1 != ($wpdbExtra->num_rows ) ) {
        $sql = "insert into $rrw_keywords (keyword, keywordFilename) 
                                    values ('$keyword','$filename' )";
        if ( $debug ) $msg .= "keywordInsert: $sql<br>\r";
        $wpdbExtra->query( $sql );
    } else {
        if ($debug )$msg .= "$keyword was found, cnt = ". $wpdbExtda->num_rows .
            " no insert done $eol";
    }
    if ( $keyword == "panarama" ) {
        $sql = "update $rrw_photos set panaramic = True 
                    where filename = '$filename' ";
        if ( $debug ) $msg .= "\r$sql<br>\r";
        $wpdbExtra->query( $sql );
    }
    return $msg;
} // end keyword insert

function keyword2photo( $filename ) {
    global $wpdbExtra, $phhotoDB, $rrw_keywords, $rrw_photos;
    global $eol, $errorBeg, $errorEnd;
    $debug = false;
    $msg = "";
 
    $sqlkeyList = "select distinct keyword from $rrw_keywords 
                    where  keywordfilename = '$filename' order by keyword ";
    if ($debug) $msg .= "$sqlkeyList $eol";
    $keyList = "";
    $recset_query = $wpdbExtra->get_resultsA( $sqlkeyList );
    foreach ( $recset_query as $rec ) {
        $keyList .= $rec[ "keyword" ] . ",";
    }
    $sql = "update $rrw_photos set photokeyword = '$keyList' 
                    where filename  = '$filename' ";
    if ( $debug ) $msg .= "keyword2photo: $sql $eol";
    $wpdbExtra->query( $sql );
    return $msg;
}