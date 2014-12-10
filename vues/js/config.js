function getAnchor(){
    return location.hash.substring(1);
}
function setAnchor(anchor){
    window.location.hash = anchor;
}

function affich(anchor){
    for (var i = 0; i < list_anchor.length; i++) {
        $("#tab_"+list_anchor[i]).hide();
    };
    setAnchor(anchor);
    $(".active[role='tabs']").removeClass("active");
    $("#nav_"+anchor).addClass("active");
    $("#tab_"+anchor).show();
}

function check_update(update){

}

$(function () {
    list_anchor = [];

    $("[role='tabs']").each(function(){
        list_anchor[list_anchor.length] = $(this).data("tab");
        $(this).attr("id", "nav_"+$(this).data("tab"));
    });

    if(getAnchor().length == 0 || $.inArray(getAnchor(), list_anchor) < 0 ){
        setAnchor(list_anchor[0]);
        affich(getAnchor());
    }
    else
        affich(getAnchor());

    $("[role='tabs']").click(function(){
        affich($(this).data("tab"));
    });
});