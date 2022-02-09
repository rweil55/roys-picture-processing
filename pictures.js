function changed() {
    var trail = document.getElementById("trailname").value;
    var field = document.getElementById("selection");
    var session = document.getElementById("session").value;
    var selectDisplay = "";
    var sqlSelectDetail = "";
    var seperator = "";
    var suroundBeg = "";
    var suroundEnd = "";
    var eol = "<br />";
    for (var i = 0; i < field.options.length; i++) {
        if (field.options[i].selected) {
            var valu = field.options[i].value;
            if (" Any Detail" == valu || "Any Detail" == valu) {
                sqlSelectDetail = "";
                break;
            }
            selectDisplay = selectDisplay + seperator + field.options[i].value;
            sqlSelectDetail = sqlSelectDetail + seperator
                + "photokeyword like xxbx" + valu + "xxex ";
            seperator = " or ";
        }
    }
    if (seperator == " or ") {
        suroundBeg = "( ";
        suroundEnd = " )";
    }
    // console.log("trail '" + trail + "'");
    var sqlSelectTrail = "trail_name = xxqx" + trail + "xxqx ";
    if (" Any Trail" == trail) {
        sqlSelectTrail = "";
    }
    var finalAnswer = sqlSelectTrail + sqlSelectDetail;
    if ("" == finalAnswer.trim())
        return true; // nothing selected

    var sqlSeperator = ""; // if only one selected
    if ("" != sqlSelectDetail && "" != sqlSelectTrail) {
        sqlSeperator = " and "; // both selected
    }
    var sqlSelect = "where " + sqlSelectTrail + sqlSeperator + sqlSelectDetail;
    console.log( " sqlSelectTrail " + sqlSelectTrail);
    console.log( " sqlSeperator " + sqlSeperator);
    console.log( " sqlSelectDetail " + sqlSelectDetail);
    var place = document.getElementById("main");
    var selectionText = trail + " and " + suroundBeg
        + selectDisplay + suroundEnd;
    var selectionMsg = "Selection is " + selectionText;
    place.innerHTML = selectionMsg + " -- One Moment " + eol;
    var loc = window.location;
    var host = loc.protocol + "//" + loc.host;
    var winNew = host + "/displayphotos?nohead=1&session=" + session + 
        "&where=" + sqlSelect;
    winNew = winNew + "&selectionIs=" + selectionText;
    //    place.innerHTML = place.innerHTML+ winNew;
    console.log(winNew);
    console.log (place);
    console.log ($);
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
