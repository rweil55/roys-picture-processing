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
        if ( $debug )$msg .= rrwUtil::print_r( $items, true, "things to add" );
        foreach ( $items as $item ) {
            if ( empty( $item ) )
                continue;
            $item = trim( $item );
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
                if ( 1 != $cnt )
                    $msg .= "$errorBeg E#621 Something happened with '$item'. 
                                $cnt should one $errorEnd";
            }
        } // end of processing one keyword
        return $msg;
    } // end keyword insert

    
    public static function fetchParameterKeywordList( $attr ) {
        // keyword display list is numbered, return comma seperated list
        $msg = "";
        $debug = false;
        // get the new keywords entered
        $keywordcnt = rrwUtil::fetchparameterString( "keywordcnt", $attr );
        $keywordList = rrwUtil::fetchparameterString( "commalist", $attr );
        $keywordList .= ",";
        $cnt = 0;
        if ( $debug )$msg .= rrwUtil::print_r( $_POST, true, "Post" );
        for ( $jj = 0; $jj < $keywordcnt; $jj++ ) {
            $cnt++;
            if ( $cnt > 300 )
                break;
            $key = "keyword$jj";
            $keywordnew = rrwUtil::fetchparameterString( "$key", $attr );
            if ( empty( $keywordnew ) )
                continue;
            $keywordList .= "$keywordnew,";
        }
        if ( $debug )$msg .= "keywordList: $keywordList $eol";
        if ( $debug )$msg .= "commalist: $commalist $eol";
        return array( $msg, $keywordList );
    }    
} // end class
?>