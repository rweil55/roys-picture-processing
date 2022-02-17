<?php
class keywordHandling {

    public static function remove( $photoname ) {
        global $wpdbExtra, $photoDB, $rrw_keywords;
        global $eol, $errorBeg, $errorEnd;
        $msg = "";

        $debug = false;
        $sql = "delete from $rrw_keywords where keywordfilename = '$photoname'";
        if ( $debug )$msg .= "keywordEmpty: $sql $eol";
        $rec = $wpdbExtra->query( $sql );
        return $msg;
    }

    public static function insertList( $photoname, $keywords ) {
        global $wpdbExtra, $rrw_keywords, $rrw_photos;
        global $eol, $errorBeg, $errorEnd;
        $msg = "";

        $debug = false;
        if ( $debug )$msg .= "keywordInsert( $photoname, $keywords ) $eol";
        $items = explode( ",", $keywords );
        if ($debug) $msg .= rrwUtil::print_r($items, true, "things to add");
        foreach ( $items as $item ) {
            if ( empty( $item ) )
                continue;
            $item = trim($item);
            if ( $debug )$msg .= "adding keyword $item $eol ";
            $insertItem = array( "keywordfilename" => $photoname,
                "keyword" => $item );
            $cnt = $wpdbExtra->insert( $rrw_keywords, $insertItem );
            if ( 1 != $cnt ) {
                $msg .= "$errorBeg E#620 Something happened with '$item'. 
                                $cnt should one $errorEnd";
            }
            if ( $item == "panarama" ) {
                $sql = "update $rrw_photos set panaramic = true 
                    where filename = '$photoname' ";
                if ( $debug )$msg .= "\r$sql<br>\r";
                $cnt = $wpdbExtra->query( $sql );
                if (1 != $cnt)
                    $msg.= "$errorBeg E#621 Something happened with '$item'. 
                                $cnt should one $errorEnd";
            }
        } // end of processing one keyword
        return $msg;
    } // end keyword insert

    public static function keyword2photo( $filename ) {
        global $wpdbExtra, $phhotoDB, $rrw_keywords, $rrw_photos;
        global $eol, $errorBeg, $errorEnd;
        $debug = false;
        $msg = "";

        $sqlkeyList = "select distinct keyword from $rrw_keywords 
                    where  keywordfilename = '$filename' order by keyword ";
        if ( $debug )$msg .= "$sqlkeyList $eol";
        $keyList = "";
        $recset_query = $wpdbExtra->get_resultsA( $sqlkeyList );
        foreach ( $recset_query as $rec ) {
            $keyList .= $rec[ "keyword" ] . ",";
        }
        $sql = "update $rrw_photos set photokeyword = '$keyList' 
                    where filename  = '$filename' ";
        if ( $debug )$msg .= "keyword2photo: $sql $eol";
        $wpdbExtra->query( $sql );
        return $msg;
    } // end 
} // end class