function getAnchor(){
    return location.hash.substring(1);
}
function setAnchor(anchor){
    window.location.hash = anchor;
}

function affich(anchor){
    for (var i = 0; i < list_anchor.length; i++) {
        $("#tab_"+list_anchor[i]).hide();
//                 $("#tab_"+list_anchor[i]).css("width", $(this).width).css("position", "absolute").animate({
//     left: '-=200%'
// }, 500).hide(500);
    };
    setAnchor(anchor);
    $("#tab_"+anchor).show();
}

$(function () {
    list_anchor = [];

    $("[role='tabs']").each(function(){
        list_anchor[list_anchor.length] = $(this).data("tab");
    });

    if(getAnchor().length == 0 || $.inArray(getAnchor(), list_anchor) < 0 )
        setAnchor(list_anchor[0]);
    else
        affich(getAnchor());

    $("[role='tabs']").click(function(){
        affich($(this).data("tab"));
        $(".active[role='tabs']").removeClass("active");
        $(this).addClass("active");
    });
});