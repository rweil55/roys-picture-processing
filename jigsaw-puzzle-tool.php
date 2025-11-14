<?php
// Freewheeling Easy Mapping Application
//		A collection of routines for display of trail maps and amenities
//  	copyright Roy R Weil 2019 - https://royweil.com

class jigsaw_puzzle_tool {

/**
 * Builds a jigsaw puzzle piece link with an image that opens the puzzle in a new window.
 *
 * This method creates an HTML anchor tag containing an image that, when clicked,
 * opens a jigsaw puzzle game in a new browser window/tab named 'puzzle'. The puzzle
 * is generated from the provided picture URL with optional credit information.
 *
 * @param string $pictureUrl The URL of the picture to be used for the jigsaw puzzle
 * @param string $creditLine Optional credit line text for the picture (default: "")
 * @param string $creditUrl Optional URL for the credit link (default: "")
 *
 * @return string HTML string containing an anchor tag with a jigsaw puzzle piece image
 *                that links to the generated puzzle page
 */
   public static function buildpiece( $pictureUrl, $creditLine = "", $creditUrl = "") {

		$pictureHttp = self::buildOne( $pictureUrl, $creditLine, $creditUrl );
                $output = "<a href='$pictureHttp' target='puzzle' >
            <img src='https://www.jigsawexplorer.com/images/mast-piece-1.png'
            alt='play a jigsaw puzzle'> </a> ";
        return $output;
    }
    /**
     * Builds a jigsaw puzzle URL using the Jigsaw Explorer platform
     *
     * Creates a URL that displays an interactive jigsaw puzzle using the provided image.
     * The method encodes the image URL and optional credit information using base64 encoding
     * with special character replacements as required by Jigsaw Explorer's URL format.
     * see self::makeBase64() for encoding details.
     *
     * @param string $pictureUrl The URL pointing to the image file (jpg or other supported format)
     * @param string $creditLine Optional credit line text to display with the puzzle (default: "")
     * @param string $creditUrl Optional URL to link the credit line to (default: "")
     * $pictureUrl posts as ?url=
     * $creditLine posts as &cred=
     * $creditUrl posts as &credu=
     *
     * @return string The complete URL to the jigsaw puzzle player with the specified image
     *
     * @throws Exception If any error occurs during URL generation
     *
     * @note Jigsaw Explorer does not currently provide an official API for custom puzzles.
     *       This method constructs URLs based on their documented URL format specification.
     */
    public static function buildOne( $pictureUrl, $creditLine = "", $creditUrl = "") {
        try {
            if (empty( $pictureUrl ) ) {
                throw new Exception( "E#220 pictureUrl are required" );
            }
			$jigsawPage = "https://www.jigsawexplorer.com/online-jigsaw-puzzle-player.html?url=" .
                            self::makeBase64( $pictureUrl );
			if ( ! empty( $creditLine) ) {
				$jigsawPage .= "&" . self::makeBase64( $creditLine );
			}
			if ( ! empty($creditUrl) ) {
				$jigsawPage .= "&" . self::makeBase64( $creditUrl );
			}

			return $jigsawPage;
        } catch ( Exception $e ) {
            throw new Exception( "Bottom of buildOne: " . $e->getMessage() );
        }
    } // end function

/**
 * Converts input data to a URL-safe base64 encoded string.
 *
 * This method takes any input data, encodes it using standard base64 encoding,
 * then replaces URL-unsafe characters with safe alternatives:
 * - '+' becomes '-'
 * - '/' becomes '_'
 * - '=' becomes '~'
 *
 * @param mixed $input The data to be encoded. Can be string or any data type that can be base64 encoded.
 * @return string Returns URL-safe base64 encoded string, or empty string if input is empty.
 */
private static function makeBase64( $input ) {
    // make a base64 string with URL safe characters
    if ( empty($input) )
        return "";
    $b64 = base64_encode( $input );
    $b64 = strtr($b64, '+/=', '-_~');
    return $b64;
}
} // end class jigsaw_puzzle_tool
