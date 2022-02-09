<?php
class freewheeling_quality { 
    function quality() {
        $msg .= "
        <h1 class-'entry-title'> Quality Standards</h1>
        This site accesses and displays 
	high quality photographs that can be used for pubication and presentation.  As such only
	pictures that meet certain quality standards are included. 
	<p>The picures should be of such a quality that that they would be considered 
	to be published in a magazine article or brochure about the trail.  
<ol>
	<li>Pictures should be of, or taken from a bicycle trail. 
	<li>Pictures should have good composition   
	<li>Pictures should have proper color balance and tone 
  	<li>Pictures should be high quality jpegs or gifs with a 
  	minimum of 800 pixels wide or high 
  	<li>Pictures should have model releases for all 
  	identifiable individuals, or have been taken in a sufficiently public place 
  so that model releases are not an issue. 
	<li>Bikers actually riding bikes must be wearing helmets, 
	unless there is an extraordinary reason for an exception
	<li>Roller Blades actually skating must be wearing helmet and protective gear,
	unless there is an extraordinary reason for an exception
	<li>The picture's copyright holder must agree to the 
  submission and either accept one of the standard releases, or provide an 
  acceptable one.</li>           
</ol>
To be considered for entry into the database the 
following information must be entered on the 
<a href='/submission.php'>submission form</a>         
   (not yet implemented) along with the uploaded graphics file.
<ol>
	<li>The name of the trail on which the picture is taken </li>
	<li>The name of the nearest trail head </li>
	<li>A brief location descriptor</li>
	<li>Photographer's name </li>
	<li>The copyright holder of not the photographer </li>
	<li>The date the photo was taken </li>
	<li>The names of any identifiable people in the photograph 
  of not taken in a public place and a signed photo relaease  </li>
	<li>Five or more keywords that describe items visible in the photograph.  Typically selected from the existing lis of keywords, although more may be created</li>
</ol>

";
        return $msg;
    } // end function
} // end class