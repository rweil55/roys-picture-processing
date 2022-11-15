<?php

require_once "display_tables_class.php";

class DisplayTrails {
    public static function Display( $attr ) {
        global $eol, $errorBeg, $errorEnd;
        global $rrw_trails;
        $msg = "";
        
        $TABLE  = new rrwDisplayTable();
        $action = rrwUtil::fetchparameterString( "action" );
        $TABLE->tablename( "$rrw_trails" );
        $TABLE->sortdefault( "trailName" );
        $TABLE->seqname( "trailName" );
        $TABLE->columns( "Trail Name", "trailName", 69 );
        $TABLE->columns( "Trail Short Name", "trail_short_name", 69 );
        $TABLE->DropDownSelf( "Corridor", "corridor", 69 );
         
        if ( empty( $action ) ) {
            $msg .= $TABLE->listdata();
            return $msg;
        }
        $msg .= $TABLE->DoAction();
        return $msg;
    }   // end function
} // end class
?>