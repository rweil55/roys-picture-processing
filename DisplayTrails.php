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
        columns( "Trail Short Name", "trail_short_name", 69 );
        DropDownSelf( "Corridor", "corridor", 69 );
         
        if ( empty( $action ) ) {
            $msg .= listdata();
            return $msg;
        }
        $msg .= DoAction();
        return $msg;
    }   // end function
} // end class
?>
