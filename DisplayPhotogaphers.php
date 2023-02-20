<?php

require_once "display_tables_class.php";

class DisplayPhotographers {
    public static function Display( $attr ) {
        global $eol, $errorBeg, $errorEnd;
        global $rrw_photographers;
        $msg = "";
        
        $TABLE  = new rrwDisplayTable();
        $action = rrwUtil::fetchparameterString( "action" );
        $TABLE->tablename( "$rrw_photographers" );
        $TABLE->sortdefault( "photographer" );
        $TABLE->keyname( "photographer" );
        $TABLE->columns( "photographer Name", "photographer", 69 );
        $TABLE->columns( "E-Mail", "Address", 69 );
        $TABLE->columns( "Comment", "Comment", 69 );
        $TABLE->columns( "copyright Default", "copyrightDefault", 69 );
        
        if ( empty( $action ) ) {
            $msg .= $TABLE->listdata();
            return $msg;
        }
        $msg .= $TABLE->DoAction();
        return $msg;
    }   // end function
} // end class
