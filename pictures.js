function changed() {
    var trail = document.getElementById("trailname").value;
    var field = document.getElementById("selection");
    var session = document.getElementById("session").value;
    var selectDisplay = "";
    var sqlSelectDetail = "";
    var seperator = "";
     var eol = "<br />";
    var cntItems = 0;
    console.log("trail '" + trail + "'");
    var sqlSelectTrail = "trail=" + trail;
    if (" Any Trail" == trail) {
        sqlSelectTrail = "";
    }
    for (var i = 0; i < field.options.length; i++) {
        if (field.options[i].selected) {
            var valu = field.options[i].value;
        console.log("in loop value '" + valu + "'");
            if (" Any Detail" == valu || "Any Detail" == valu) {
                sqlSelectDetail = "";
                break;
            }
            cntItems++;
            selectDisplay = selectDisplay + seperator + field.options[i].value;
            sqlSelectDetail = valu;
            seperator = " or ";
        }
    }
    console.log ("count " + cntItems + ", " + selectDisplay);
    if (0 == cntItems && "" == sqlSelectTrail)
        return true;
    if (1 < cntItems)
        sqlSelectDetail = "( " + sqlSelectDetail + ")"; // multiple selected
     
    console.log("sqlSelectDetail '" + sqlSelectDetail + "'");
  
    var display = trail;
    if (display.length == 0){
        display = sqlSelectDetail;
    }else {
        display = display + " and " + sqlSelectDetail;
    }
   
    if (display.length == 0) {
        return true; // nothing selected
        }
   
    console.log (sqlSelectDetail);
    var place = document.getElementById("main");
    var selectionMsg = "Selection is " + display;
    place.innerHTML = selectionMsg + " -- One Moment " + eol;
    var loc = window.location;
    var host = loc.protocol + "//" + loc.host;
    var winNew = host + "/displayphotos?nohead=1&session=" + session +
        "&trail=" + trail + "&searchdropdown=" + sqlSelectDetail;
   console.log (winNew);
    jQuery.get(winNew,
        function (data, status ) {
            place.innerHTML =  data;
        place.scrollIntoView(true);
        }
    );
    return true;
}
function onClickKeyword (keyword) {
    var selectKey = document.getElementById("selection");
    selectKey.value = keyword;
    changed();
    return true;
}
function onClickCopy2Clip(xxzzy)  {
    console.log (xxzzy);
    navigator.clipboard.writeText(xxzzy);
}
function openOnePhotopage(photoname) {
    var loc = window.location;
    var host = loc.protocol + "//" + loc.host;
    var h_screen = screen.height;
    var w_screen = screen.width;
    var winOne = host + "/display-one-photo?nohead=1&photoname=" + photoname +
        "&nohead=1&w_screen=" + w_screen + "&h_screen=" + h_screen ;
    console.log(winOne)
    var place = document.getElementById("main");
    jQuery.get(winOne,
        function (data) {
            //  alert('page content: ' + data);
            place.innerHTML = data;
            place.scrollIntoView();
        }
    );
    return true;
}
