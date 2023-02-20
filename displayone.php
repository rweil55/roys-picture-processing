<?php
/*		Freewheeling Easy Mapping Application
 *		A collection of routines for display of pictures
 *		copyright Roy R Weil 2019 - https://royweil.com
 */
class freeWheeling_DisplayOne
{
    public static function DisplayOne($attr)
    {
        // display full size and thumbnail and the meta data infomation
        ini_set("display_errors", true);
        global $photoUrl, $photoPath, $highresPath,
            $thumbPath, $thumbUrl, $site_url;
        global $wpdbExtra, $rrw_photos, $rrw_trails, $rrw_photographers,
            $rrw_keywords, $rrw_access;
        global $httpSource;
        global $eol, $errorBeg, $errorEnd;
        $msg = "";
        $debugPath = false;
        try {
            $photoname = rrwUtil::fetchparameterString("photoname", $attr);
            $photoname = str_replace("_tmb", "", $photoname);
            $photoname = str_replace(".jpg", "", $photoname);
            if ($debugPath) $msg .= "displayOne:photoPath $photoPath $eol";
            //	if thumbs is true them display thumbnail, else display fill size
            if ($debugPath) $msg .= " DisplayOne ($photoname )   ... $eol ";
            $current_user = wp_get_current_user();
            if (!($current_user instanceof WP_User))
                $ser = "Guest";
            else
                $user = $current_user->name;

            $ip = $_SERVER['REMOTE_ADDR'];

            $sqlAccess = "Insert into $rrw_access (accessphotoname, accessIP,accessuser)
                values ('$photoname', '$ip', '$user')";
            $answer = $wpdbExtra->query($sqlAccess);
            if ("nokey" == $photoname) {
                $sqlNoKey = "select photoname from $rrw_photos  where not photoname in 
                 (select distinct keywordFilename from $rrw_keywords )";
                $recNokey = $wpdbExtra->get_resultsA($sqlNoKey);
                if (0 == $wpdbExtra->num_rows)
                    return "There are no photos with out keywords
                    [ <a href='/fix/?task=add'>find more </a> ]
                    [ <a href='/fix/?task=direonp'>missing source </a> ]
                            ";
                $photoname = $recNokey[0]["photoname"];
            }
            $sql = "Select * from $rrw_photos where photoname = '$photoname'";
            $msg .= ("\n<!-- sql is $sql -->\n");
            $recset_query = $wpdbExtra->get_resultsA($sql);
            if (1 != $wpdbExtra->num_rows) {
                $msg .= "E#234 no meta data for image $photoname was found             $errorEnd $sql $eol";
                $msg .= freewheeling_fixit::filelike(
                    array(
                        "photname" => $photoname,
                        "partial" => $photoname
                    )
                );
                return $msg;
            }
            $recset = $recset_query[0];
            $photoname = $recset["photoname"];
            $photographer = $recset["photographer"];
            $copyright = $recset["copyright"];
            $PhotoDate = $recset["PhotoDate"];
            $location = $recset["location"];
            $people = $recset["people"];
            $comment = $recset["comment"];
            $direonp = $recset["DireOnP"];
            $trail_name = $recset["trail_name"];
            $uploaddate = $recset["uploaddate"];
            $owner = $recset["owner"];
            $highresShortname = $recset["highresShortname"];
            if ($debugPath) $msg .= "displayOne:photoUrl $photoPath $eol";
            $htmlfileref1 = "$photoUrl/{$photoname}_cr.jpg";
            $fullfilename1 = "$photoPath/{$photoname}_cr.jpg";
            $htmlfileref2 = "$thumbUrl/{$photoname}_tmb.jpg";
            $fullfilename2 = "$thumbPath/{$photoname}_tmb.jpg";
            if (!file_exists($fullfilename1)) {
                $msg .= "$errorBeg E#210 Not found was the display image $htmlfileref1 $errorEnd";
            }
            /*  displaywidth is based on screen size 
            $imageinfo = getimagesize( $fullfilename1 );
            $displayHeight = $imageinfo[ 0 ];
            $displayWidth = $imageinfo[ 1 ];
            $maxHeight = 400;
            if ( $displayHeight > $maxHeight ) {
                $displayHeight = $maxHeight;
                $displaywidth = $displaywidth * ( $displayHeight / $maxHeight );
            }
            */
            if (!file_exists($fullfilename2)) {
                $msg .= "$errorBeg E#209 Not found was the thumbnail image $htmlfileref2 $errorEnd";
                $thumbsize = 200;
            } else {
                $thumbInfo = getimagesize($fullfilename2);
                $thumbsize = $thumbInfo[3];
            }
            if (false) {
                // let wordpress determine image size
                $msg .= "<figure class='wp-block-image size-large'><img 
                src='$htmlfileref1' class='wp-image-49'/></figure>";
            } else {
                $h_screen = rrwUtil::fetchparameterInteger("h_screen") - 30;
                $w_screen = rrwUtil::fetchparameterInteger("w_screen");
                //      $aspect = $w_screen / $h_screen;
                //      $adjust = $h_screen / $h_cr;
                //      $w_desired = round( $w_cr * $adjust, 0 ) . "px";
                $msg .= "<table><tr>
                    <td align='left' width='400'>
                        <img src='$htmlfileref1' alt='Trail Photo'
                        height='auto' width='$$w_screen' id='bigImage' />
                        &nbsp; </td>
                    <td width=3px> </td>
                    <td align='left' valign='center' width='200' >
                        <img src='$htmlfileref2' alt='small Trail Photo'
                    $thumbsize id='smallImage' /><br /><br />
                    <a href = '$htmlfileref1' download=$photoname > 
                    <img src ='/wp-content/download.png' width='200'></a>";
                $msg .= freeWheeling_DisplayOne::DisplayTableData($recset, "narrow");
                $msg .= "
                    </td></tr></table> $eol";
                //print "adjust = $adjust, width=$w_desired $eol";
            }

            //       $msg .= freeWheeling_DisplayOne::DisplayTableData( $recset, "wide" );
            $msg .= "
        <script type='javascript' >
        var photo =document.getElementById('bigImage');
        photo.scrollIntoView();
        </script>
        ";
            // End of display data section
            if (!freewheeling_fixit::allowedEdit($owner))
                return "$msg $eol .";

            $msg .= "<hr>";
            // --------------------------------------------------- update section
            // --------------------------------------------------- update section
            // --------------------------------------------------- update section
            $server = $_SERVER['HTTP_HOST'];
            $msg .= "
            <form action='https://$server/update' method='post' >
            <input type='hidden' name='allow' value='1' />
            <table><tr><td>
            ";
            if ($debugPath) $msg .= "***** 4 *****displayOne:photoPath $photoPath <br/>";
            $msg .= "<input type='hidden' name='copyright' id='copyright'
                value='$copyright' /> \n";
            $msg .= "<strong>Trail:</strong>" .
                rrwFormat::selectBox(
                    $wpdbExtra,
                    "$rrw_trails",
                    "trailName",
                    $trail_name,
                    "trailName"
                ) . " $eol";
            $msg .= "<strong>Photogrpher:</strong>" .
                rrwFormat::selectBox(
                    $wpdbExtra,
                    "$rrw_photographers",
                    "photographer",
                    $photographer,
                    "photographer"
                ) . $eol;
            $msg .= "\n" . "<strong>Photo Date:</strong>
                <input type='text' name='photodate' size='20' 
                        value='$PhotoDate'>" . "\n" . "$eol 
                <strong>upload date:</strong> 
                <input type='text' name='uploaddate' size='20' 
                        value='$uploaddate'>
                &nbsp; &nbsp; &nbsp; &nbsp; ";
            if (false === strpos($photoname, "_cr")) {
                $msg .= "[ <a href='/fix/?task=reload&" .
                    "fullfilename=$highresPath/$highresShortname' >
                    reload image </a> ] ";
            }
            $msg .= "
             [ <a href='/fix/?task=filelike&partial=$photoname&photoname=$photoname' >
                    search $photoname</a> ] 
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Delete this photo from the daabase
            [ <a href='/fix?task=deletephoto&del2=$photoname&del3=$photoname" .
                "&why=duplicate' > Mark as Duplicate </a> ] 
            [ <a href='/fix?task=deletephoto&del2=$photoname&del3=$photoname" .
                "&why=reject' > Mark as Rejected </a> ] $eol
            Location: <input type='text' name='location' size='50' 
                        value='$location'>" . "\n" .
                "<br><strong>People:</strong>
            <input type='text' name='people' size='50' 
                        value='$people' >" . "\n" .
                "<br><strong>Comments:</strong>
            <textarea name='comment' rows='5' cols='50' >$comment</textarea>
            </td><td align='left'>
            <img src='$htmlfileref2' alt='small Trail Photo'
                    $thumbsize id='smallImage' />
            </td></tr>
            </table>   
            <strong>Source Directory:</strong> &nbsp; 
            ";
            if (empty($direonp)) {
                $msg .= "<input type='text' name='direonp' id='direonp' size='80px' />";
            } else {
                $onDriveD = substr($direonp, 2);
                $msg .= "$direonp   
                    <input type='hidden' name='direonp' id='direonp' 
                        value='$direonp' /> &nbsp; 
                  [ <a href='$httpSource/$onDriveD'
                target='submit' > 127.0.0.1 picture </a> ] ";
            }
            $msg .= " [ Search: 
                <a href='/fix/?task=filelike&partial=$photoname&photoname=$photoname' 
                            >$photoname</a> ] 
                   [ <a href='/display-one-photo/?photoname=nokey'> 
                        Next no keywords</a>]
        <input type='hidden' name='photoname' id='photoname' value='$photoname' />
        <br /><input type='submit' value=\"Commit all changes\" id='submit1' name='submit1' > ";
            // --------------------------------------------------  keyword list

            $msg .= "<p>Enter additional keywords in a comma separated list or use the check boxes below. " .
                " List and checked boxes will be merged to produce final list. $eol" .
                "<input size=150 type=text name=commalist><br>";

            $cntChk = 0;
            $cntTot = 0;
            $msg .= freeWheeling_DisplayOne::keywodCheckboxes($photoname) . "
            
            
                <input type='hidden' name='photoname' value='$photoname' />
                <input type='submit' value=\"Commit all changes\"> &nbsp;
                <input type='reset'>
                <a href='/display-one-photo/?photoname=nokey'> 
                        Next no keywords</a>
                <img src='$htmlfileref2' alt='small Trail Photo'
                    $thumbsize id='smallImage' valign='top' />
       
         <!--   <img src='$fullfilename2' alt='small size image' /> -->
               </form>";
            if (file_exists($fullfilename1)) {
                try {
                    $meta = rrwExif::rrw_exif_read_data($fullfilename1);
                } // end try
                catch (Exception $ex) {
                    $msg .= $ex->getMessage() . "$errorBeg  E#668 error loading the exif for '$fullfilename1' $errorEnd";
                }
                if (false !== $meta) {
                    $msg .= rrwUtil::print_r($meta, true, "EXIF data for $fullfilename1");
                } else {
                    $msg .= "exif_read_data returned false on $eol $fullfilename1 $eol";
                }
            } else {
                $msg .= "exif_read_data could not find $eol $fullfilename1 $eol";
            }
        } catch (Exception $ex) {
            print "catch";
            $msg .= " E#199 DisplayOne " . $ex->getMessage();
        }
        return $msg;
    } // end function

    private static function keywodCheckboxes($photoname)
    {
        // buld the display of keyword checkbox
        global $wpdbExtra, $rrw_keywords;
        global $eol, $errorBeg, $errorEnd;
        $msg = "";
        $debugKeywords = false;
        $grid = false;

        $keywordList = array(); // get list all keyword
        $sql = "select distinct keyword from $rrw_keywords order by keyword";
        $recset_query = $wpdbExtra->get_resultsA($sql);
        $cntTot = 0;
        foreach ($recset_query as $recset) {
            $ctemp = $recset["keyword"];
            $keywordList[$ctemp] = 0; // set not checked
            $cntTot++;
        }
        $sql = "select distinct keyword from $rrw_keywords
                where keywordfilename = '$photoname' "; // get checked keyword
        if ($debugKeywords) $msg .= "Keyword search: $sql $eol";
        $cntChk = 0;
        $recset_query = $wpdbExtra->get_resultsA($sql);
        foreach ($recset_query as $recset) {
            $ctemp = $recset["keyword"];
            $keywordList[$ctemp] = 1; // set checked
            $cntChk++;
        }
        if ($debugKeywords) $msg .= "there are $cntChk keywords checked";
        if ($grid)
            $msg .= "\n<div class='rrwKeywordGrid'>\n";
        else
            $msg .= "\n<div class='rrwKeywordFlex'>\n";
        $jj = -1;
        foreach ($keywordList as $key => $valu) {
            $jj++;
            if ($grid)
                $msg .= " <div class='rrwKeywordGridItem'>";
            else
                $msg .= " <div class='rrwKeywordFlexItem'>";
            $msg .= "<input type='checkbox' name='keyword$jj' value='$key' ";
            if (1 == $valu) {
                $msg .= " checked";
            }
            $msg .= " >" . $key . "</div>\n";
        }
        $msg .= "</div> ";
        $msg .= "<input type='hidden' name='keywordcnt' value='$cntTot' />";
        return $msg;
    }

    private static function DisplayTableData($recset, $format)
    {
        global $photoUrl, $photoPath, $highresPath, $thumbPath;
        global $eol, $errorBeg, $errorEnd;
        $msg = "";

        $photoname = $recset["photoname"];

        if (is_null($recset["trail_name"])) {
            $trailDisplay = " & lt; & lt; Missing & gt; & gt;
        ";
        } else {
            $trailDisplay = htmlspecialchars($recset["trail_name"]);
        }
        if (is_null($recset["photographer"])) {
            $photographerDisplay = " & lt; & lt; Missing & gt; & gt; ";
        } else {
            $photographerDisplay = $recset["photographer"] . "</td>\n ";
        }

        $copyRight = $recset["copyright"];
        if (empty($copyRight))
            $copyRight = "Copyright missing from file - Assume all rights reserved
                <a href='/author2copyright/?filename=$photoname' >.</a>";
        else {
            $copyRight = str_replace(",", $eol, $copyRight);
            $copyRight = str_replace(" - ", $eol, $copyRight);
        }

        $nameDisplay = " <a href='$photoUrl/{$photoname}_cr.jpg'>$photoname</a>
                &nbsp; &nbsp; &nbsp; &nbsp; " . $recset['comment'] . "$eol";

        $keywordDisplay = self::GetkkeywordLinkedList($photoname);

        $fullname = "$photoPath/$photoname" . "_cr.jpg";
        if (file_exists($fullname)) {
            $photoSize = getimagesize($fullname);
            $sizeDisplay = $photoSize[3];
        } else
            $sizeDisplay = "";

        $fullHighRes = "$highresPath/$photoname.jpg";
        if (file_exists($fullHighRes)) {
            $photoSize = getimagesize($fullHighRes);
            $sizeHighres = $photoSize[3];
        } else
            $sizeHighres = "";

        $fullThumb = "$thumbPath/$photoname" . "_tmb.jpg";
        if (file_exists($fullThumb)) {
            $photoSize = getimagesize($fullThumb);
            $sizeThumb = $photoSize[3];
        } else
            $sizeThumb = "";
        switch ($format) {
            case "wide":

                //  -------------------------------------------------- Now the display
                $msg .= " File is $nameDisplay<table> \n ";
                $msg .= rrwFormat::CellRow(
                    "Trail: ",
                    $trailDisplay,
                    "Photographer: ",
                    $photographerDisplay
                );
                $msg .= rrwFormat::CellRow(
                    "Location: ",
                    $recset["location"],
                    "Photo Date: ",
                    $recset["PhotoDate"]
                );
                $msg .= rrwFormat::CellRow(
                    "Photo Size ",
                    "<a href='$photoUrl/{$photoname}_cr.jpg'>this - $sizeDisplay</a>,
               ThumbNail - $sizeThumb "
                );
                $msg .= "</table>";
                $msg .= "$copyRight $eol";

                # -------------------- keywords
                $msg .= "<strong>Existing keywords:</strong>\n $keywordDisplay";

                $msg .= "$eol<strong>Identifiable People:</strong>" . $recset["people"] . "
<div id='missedClassifi' onclick='openMissedClassifi(this,$photoname);'>
    if any of this infomation is incorrect or missing. Please
    <a href='/webmaster-feedback'>let us know</a></div>$eol";
                break;
            case "narrow":
                $msg .= "\n<div class='rrwOnePhoto'><table> \n " .
                    rrwFormat::CellRow("Photo Name: ", $nameDisplay) .
                    rrwFormat::CellRow("Trail: ", $trailDisplay) .
                    rrwFormat::CellRow("Location: ", $recset["location"]) .
                    rrwFormat::CellRow("Photographer: ", $photographerDisplay) .
                    rrwFormat::CellRow("Photo Date: ", $recset["PhotoDate"]) .
                    rrwFormat::CellRow("Identifiable People ", $recset["people"]) .
                    rrwFormat::CellRow("Keywords: ", $keywordDisplay) .
                    rrwFormat::CellRow("Copyright: ", $copyRight) .
                    rrwFormat::CellRow("Photo Size ", $sizeDisplay) .
                    rrwFormat::CellRow("TumbNail size", $sizeThumb) .
                    "</table>";
                # -------------------- keywords

                $msg .= "
<div id='missedClassifi' onclick='openMissedClassifi(this,$photoname);'>
    if any of this information is incorrect or missing. Please
    <a href='/webmaster-feedback'>let us know</a></div>$eol";
                $jigsawPiece = jigsaw_verify::buildpiece(
                    "$photoUrl/{$photoname}_cr.jpg",
                    $keywordDisplay,
                    $copyRight,
                    ""
                );
                $msg .= "$eol $jigsawPiece $eol";
                break;
            default:
                $msg .= "$errorBeg E#175 unknown fromat style of '$format' $errorEnd";
                break;
        } // end switch $format
        return $msg;
    } // end display table

    public static function keywordsDisplay($photoname)
    {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_keywords;
        $output = "";

        $sqlkey = "select keyword from $rrw_keywords 
                        where keywordfilename = '$photoname'";
        $recKeys = $wpdbExtra->get_resultsA($sqlkey);
        foreach ($recKeys as $reckey) {
            $keyWordItem = $reckey["keyword"];
            $keyWordItem = trim($keyWordItem);
            if (empty($keyWordItem))
                continue;
            //        $http = "/search.php?keyword=$keyWordItem";
            //       $msg .= "<a href='" . htmlspecialchars( $http ) .
            //       "'>" . $keyWordItem . ",</a>";
            $output .= "$keyWordItem, ";
        }
        $output = substr($output, 0, -2);
        return $output;
    }
    public static function GetkkeywordLinkedList($photoname)
    {
        global $eol, $errorBeg, $errorEnd;
        global $wpdbExtra, $rrw_keywords;
        $output = "";

        $sqlkey = "select keyword from $rrw_keywords 
                        where keywordfilename = '$photoname'";
        $recKeys = $wpdbExtra->get_resultsA($sqlkey);
        foreach ($recKeys as $reckey) {
            $keyWordItem = $reckey["keyword"];
            $keyWordItem = trim($keyWordItem);
            if (empty($keyWordItem))
                continue;
            //        $http = "/search.php?keyword=$keyWordItem";
            //       $msg .= "<a href='" . htmlspecialchars( $http ) .
            //       "'>" . $keyWordItem . ",</a>";
            $output .= " <a onclick='onClickKeyword(\"$keyWordItem\");' >
                        $keyWordItem, </a> ";
        }
        return $output;
    }
} // ed class
