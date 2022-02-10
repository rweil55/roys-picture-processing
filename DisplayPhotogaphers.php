<?php

class DisplayPhotographers {
    public static function Display( $attr ) {
        global $eol, $errorBeg, $errorEnd;
        global $rrw_photographers;
        $msg = "";
        print "$rrw_photographers $eol";
        $action = rrwUtil::fetchparameterString( "action" );
        tablename( "$rrw_photographers" );
        sortdefault( "photographer" );
        seqname( "photographer_id" );
        columns( "photographer Name", "photographer", 69 );
        columns( "E-Mail", "Address", 69 );
        columns( "Comment", "Comment", 69 );
        columns( "copyright Default", "copyrightDefault", 69 );
        
        if ( empty( $action ) ) {
            $msg .= listdata();
            return $msg;
        }
        $msg .= DoAction();
        return $msg;
    }   // end function
} // end class
?>
