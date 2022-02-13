<?php

/*  function SearchBoxAllKeywords() {
    global $eol;
    global $wpdbExtra, $rrw_photos, $rrw_trails, $rrw_keywords;
    $msg = "";

    $msg .= SetConstants( "searchbox" );

    $debug1 = true;

    $_SESSION[ 'PhotoList' ] = ""; //	Clean out the existing list;
    $_SESSION[ 'searchwords' ] = ""; //	of things that hve been searched;
    $_SESSION[ 'location' ] = "";
    print '
<script type="application/javascript" >
    function changed() {
        trail = document.getElementById("trailname").value;
        selection = "";
        sqlSelectDetail = "";
        seperator = "";
        suroundBeg = "";
        suroundEnd = "";
        field = document.getElementById("selection");
        for (var i = 0; i < field.options.length; i++) {
            if (field.options[i].selected) {
                valu =  field.options[i].value;
                if ("Any Detail" == valu) {
                    sqlSelectDetail = "1";
                    break;
                }
                selection =  selection + seperator + field.options[i].value;
                sqlSelectDetail = sqlSelectDetail + seperator + 
                "photokeyword like xxbx" + valu + "xxex ";
                 seperator = " or ";
         }
        }
        if (seperator == " or ") {
                suroundBeg = "( ";
                suroundEnd = " )";
        }
        sqlSelect = "trail_name = \'" + trail + "\' and ";
        if (trail == "Any Trail") {
            sqlSelect = "";
        }
        sqlSelectDetail ="( " + sqlSelectDetail + " )";
        sqlSelect = "where " + sqlSelect + sqlSelectDetail;
        place = document.getElementById("displayPlace");
        var selectionText =  trail + " and " + suroundBeg + 
        selection +suroundEnd + "<br />";
        place.innerHTML = "Selection is " + selectionText
        winNew = "https:/photos?where=" + sqlSelect ;
        winNew = winNew + "&selectionIs=" + selectionText;
        alert(winNew);
        winNewNoHead = winNew + "&nohead=please";
        // window.open(winNewNoHead, "photo");
        ifram = document.getElementById("photohere");
        ifram.src = winNewNoHead;
        ifram.height = screen.height;
        ifram.scrollIntoView();
        return true;        
    }
    function openOnePhotopage (URL) {
        alert ("here");    
    }    
</script>
<style>
.entry-header  {
 display: none;
 }
</style>

';
    $msg .= "<div id='displayPlace' > xxx make a selection above</div> ";
    $msg .= "<iframe id='photohere' src='about:blank' width='100%'
            height='100%' </iframe>";
    return $msg;
}

// function searchBox () { 
    global $eol;
    global $wpdbExtra, $rrw_photos, $rrw_trails, $rrw_keywords;
    $msg = "";

    $table = new rrwDisplayTable;
    $msg .= "<form action='/search.php' method='post' id='form1' name='form1' >";
    // ------------------------------------------ trail selection
    $currentvalue = "Any Trail";
    $sql = "select '$currentvalue' trailName, '$currentvalue' union 
    select distinct trailName, trailName val from $rrw_trails order by TrailName";
    $out = $table->DisplayDropDownsql( $wpdbExtra, $rrw_photos,
        "trailname", $sql, $currentvalue );  
    $msg .= "<div id='traildiv' onmouseout=changed(); > $out </div> ";
    // ---------------------------------------  detail selection
    $currentvalue = "Any Detail";
    $sql = "select '$currentvalue' selection, '$currentvalue' union 
        select keyword, concat(keyword, ' (', count(*), ')') cnt 
            from $rrw_keywords group by keyword ";
    $out = $table->DisplayDropDownsql( $wpdbExtra, $rrw_keywords,
        "selection", $sql, $currentvalue );
    $out .= str_replace( "selection'", "selection'  multiple ", $out );
    $msg .= "<div id='trailddetail' onmouseout=changed(); > $out </div> ";
    $msg .= "</form>$eol";
    return $msg;
    }
    
    */
?>