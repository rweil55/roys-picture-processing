<?php
// Freewheeling Easy Mapping Application
//		A collection of routines for display of trail maps and amenities
//  	copyright Roy R Weil 2019 - https://royweil.com

/*      Short code
        jigsaw_verify::Verify - run a test to check input - show answer
        jigsaw_verify::buildOne( $pictureUrl, $creditLine = "", $creditUrl = "",
                $puzzleMaker = "puzzleExployer" ) {
                rwturns an http string
        jigsaw_verify::buildpiece $pictureUrl, $creditLine = "", $creditUrl = "",
                $puzzleMaker = "puzzleExployer" ) {
                rwturns an <img of a puzzle piece with link to puzzle


        */
class jigsaw_verify {
    public static function verify( $attr ) {
        // this routine checks that all basic functions works
        global $eol, $errorBeg, $errorEnd;
        $msg = "";
        $pictureURL = "https://pictures.shaw-weil.com/wp-content/photos";
        $pictureURL .= "/coke-ovens-0102-s_cr.jpg";
        $jigsawURL = self::buildOne( $pictureURL );
        if ( "http" != substr( $jigsawURL, 0, 4 ) )
            $msg .= "$errorBeg E#220 build one did not retrun a 'http' answer $errorEnd" . htmlspecialchars( $jigsawURL ) . $eol;
        $msg .= "$jigsawURL $eol";
        $msg .= "$eol <a href='$jigsawURL' target='puzzle'> try it now $eol";
        $piece = self::buildpiece( $pictureURL, " You did it", "codedbestmerli
        by Roy Weil" );
        $msg .= "$eol &nbsp;  $piece $eol";
        return $msg;
    }
    public static function buildpiece( $pictureUrl, $creditLine = "", $creditUrl = "",
        $puzzleMaker = "puzzleExployer" ) {
        $debugPiece = false;

        if ($debugPiece) print "buildOne( $pictureUrl, $creditLine ,
                    $creditUrl, $puzzleMaker )";
        $pictureHttp = self::buildOne( $pictureUrl, $creditLine = "", $creditUrl = "",
            $puzzleMaker = "puzzleExployer" );
        $output = "<a href='$pictureHttp' target=='puzzle' >
            <img src='https://www.jigsawexplorer.com/images/mast-piece-1.png'
            alt='play a jigsaw puzzle'> </a> ";
        return $output;
    }
    public static function buildOne( $pictureUrl, $creditLine = "", $creditUrl = "",
        $puzzleMaker = "puzzleExployer" ) {
        //  $pictureUrl  a url that points to a jpg, or some other picture type
        //  $puzzleMaker - list of makers that can create puzzles
        //               - currently only one maker available.
        //  OutputURL    - a URL that displays a jigsaw puzzle to play
        global $eol, $errorBeg, $errorEnd;
        $msg = "";
        switch ( $puzzleMaker ) {
            case "puzzleExployer":
                case "";
                $httpSource = "https:://www.jigsawexplorer.com/create-a-custom-jigsaw-puzzle/";
                // https://www.jigsawexplorer.com/images/mast-piece-1.png
                break;
            default:
                throw new Exception( "$errorBeg E#221 Do not handle puzzle make '$puzzleMaker' $errorEnd" );
        } // end switch (puzzleMaker)
        $postfields = http_build_query( array(
            "image-url" => $pictureUrl,
            "credit-line" => $creditLine,
            "creditUrl" => $creditUrl,
            "puzzle-nop" => 100
        ) );
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL,
            "https://www.jigsawexplorer.com//jigsaw-puzzle-result/" );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postfields );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        // Receive server response ...
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $explorerPage = $server_output = curl_exec( $ch );
        $iifullLink = strpos( $explorerPage, "full-link" );
        $explorerPage = substr( $explorerPage, $iifullLink );
        $iiHttp = strpos( $explorerPage, "https" );
        $explorerPage = substr( $explorerPage, $iiHttp );
        $iiGT = strpos( $explorerPage, "</textarea" );
        $pictureUrl = SUBSTR( $explorerPage, 0, $iiGT );
        curl_close( $ch );
        $pictureUrl = "https://www.jigsawexplorer.com/online-jigsaw-puzzle-player.html?url=aHR0cHM6Ly9waWN0dXJlcy5zaGF3LXdlaWwuY29tL3dwLWNvbnRlbnQvcGhvdG9zL2Nva2Utb3ZlbnMtMDEwMi1zX2NyLmpwZw~~";
        return $pictureUrl;    }
} // end class
