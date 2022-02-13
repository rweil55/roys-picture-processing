<?php

class DisplayTrails {
    public static function Display( $attr ) {
        global $eol, $errorBeg, $errorEnd;
        global $rrw_trails;
        $msg = "";

        $action = rrwUtil::fetchparameterString( "action" );
        tablename( "$rrw_trails" );
        sortdefault( "trailName" );
        seqname( "trailName" );
        columns( "Trail Name", "trailName", 69 );
        columns( "Trail Sort", "trail_sort", 69 );
        columns( "Corridor", "corridor", 69 );
         
        if ( empty( $action ) ) {
            $msg .= listdata();
            return $msg;
        }
        $msg .= DoAction();
        return $msg;
    }   // end function
} // end class
?>
