function changed() {
    console.log("changed called");
    var trail = document.getElementById("trailname").value;
    var field = document.getElementById("selection");
    var session = document.getElementById("session").value;
    var selectDisplay = "";
    var sqlSelectDetail = "";
    var separator = "";
    var eol = "<br />";
    var cntItems = 0;
    console.log("trail '" + trail + "'");
    var sqlSelectTrail = "trail=" + trail;
    if (" Any Trail" == trail) {
        sqlSelectTrail = "";
    }
    for (var i = 0; i < field.options.length; i++) {
        if (field.options[i].selected) {
            var valueOption = field.options[i].value;
            console.log("in loop value '" + valueOption + "'");
            if (" Any Detail" == valueOption || "Any Detail" == valueOption) {
                sqlSelectDetail = "";
                break;
            }
            cntItems++;
            selectDisplay = selectDisplay + separator + field.options[i].value;
            sqlSelectDetail = valueOption;
            separator = " or ";
        }
    }
    console.log("count " + cntItems + ", " + selectDisplay);
    if (0 == cntItems && "" == sqlSelectTrail)
        return true;
    if (1 < cntItems)
        sqlSelectDetail = "( " + sqlSelectDetail + ")"; // multiple selected
    console.log("sqlSelectDetail '" + sqlSelectDetail + "'");
    var display = trail;
    if (display.length == 0) {
        display = sqlSelectDetail;
    } else {
        display = display + " and " + sqlSelectDetail;
    }
    if (display.length == 0) {
        return true; // nothing selected
    }
    console.log(sqlSelectDetail);
    var place = document.getElementById("main");
    var selectionMsg = "Selection is " + display;
    place.innerHTML = selectionMsg + " -- One Moment " + eol;
    var loc = window.location;
    var host = loc.protocol + "//" + loc.host;
    var winNew = host + "/displayphotos?session=" + session +
        "&trail=" + trail + "&searchdropdown=" + sqlSelectDetail;
    console.log(winNew);
    window.open(winNew, "_self");
    return true;
}
function onClickKeyword(keyword) {
    var selectKey = document.getElementById("selection");
    selectKey.value = keyword;
    changed();
    return true;
}
function onClickCopy2Clip(xxzzy) {
    console.log(xxzzy);
    navigator.clipboard.writeText(xxzzy);
}
function openOnePhotopage(photoname) {
    var loc = window.location;
    var host = loc.protocol + "//" + loc.host;
    var h_screen = screen.height;
    var w_screen = screen.width;
    var winOne = host + "/display-one-photo?photoname=" + photoname +
        "&w_screen=" + w_screen + "&h_screen=" + h_screen;
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