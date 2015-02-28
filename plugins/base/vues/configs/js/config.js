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


/////////////////////////////
// Recherche sur le market //
/////////////////////////////
$( "#market_search" ).keyup(function() {
  window.query_search = $(this).val();
  marketSearch($(this).val());
});

list_plugins = [];
page_plugin = 1;
plugin_per_page = 5;
loader_timeout = null;

$(function(){
  marketSearch("");
});

xhr = false;
query = "";
function marketSearch(search_query){
  if(xhr !== false){
    xhr.abort();
  }
  query = search_query;

  $("#market_search_loading").hide('scale',{ percent: 0 },70, function(){
    console.log("loupe cachée");
    $("#load_market").show('scale',{ percent: 0 },70, function(){console.log("loader affiché")});
  });

  // window.location.pathname = "search-"+query;
  xhr = $.ajax({
      type: "GET",
      url: "http://www.evaproject.net/api/v1/",
      crossDomain: false,
      data: "get=market_search&query="+query+"&version="+program_version,
      success: function(json){
        var data = JSON.parse(json);
        text = "";
        list_plugins = [];
        for (var i = 0; i < data.plugins.length; i++) {
          current_plugin = data.plugins[i];
          list_plugins.push(current_plugin);
        };
        affich_plugins();
      },
      error: function(data){
              $("#market_list_plugins").html('<div style="text-align:center;"><i style="color: rgb(183, 10, 10);" class="fa fa-exclamation-triangle fa-5x"></i><br>Le serveur de plugin ne répond pas, veuillez réessayer ultérieurement</div>');
      }
  });
    loader_timeout = setTimeout(function(){
    $("#load_market").hide('scale',{ percent: 0 },70, function(){
      console.log("loader caché");
      $("#market_search_loading").show('scale',{ percent: 0 },70,function(){console.log("loupe affiche");});
    });
  }, 500)
}

function affich_plugins(){
  text = "";
  nb_page = Math.ceil(list_plugins.length/plugin_per_page);
  first = (page_plugin-1)*plugin_per_page;
  if(first < 0)
    first = 0;

  last = page_plugin*plugin_per_page;
  if(last  > list_plugins.length)
    last = list_plugins.length;

  for (var i = first; i < last; i++) {
    plugin = list_plugins[i];
    version_support = JSON.parse(plugin.version_support);

    if($.inArray(program_version, version_support) > 0)
      supported = true;
    else
      supported = false;

    d = new Date(plugin.time*1000);
    title = plugin.name;

    content = "Auteur : "+plugin.author+"<br>Dernière update : "+d.toLocaleString()+"<br>Versions supportées : "+version_support.join(", ")+(supported?'<br><button type="button" data-pluginid="'+plugin.id+'" class="btn btn-success plugin_install">Installer le plugin</button>':' <span class="label label-danger">Votre version n\'est pas supportée</span>');
    text += '<div class="panel panel-'+(supported?"primary":"danger")+'"><div class="panel-heading">'+title+'</div><div class="panel-body">'+content+'</div></div>';
  };

  if(text.length < 1)
    text = "<li>Aucun plugin correspondant</li>";

  $("#market_list_plugins").html(text);

  affich_paginations(nb_page, page_plugin);
}

function change_plugin_page(page){
  page_plugin = page;
  window.page_plugin = page;
  affich_plugins();
}

function affich_paginations(nb_page, current_page){

  if(nb_page > 1){
    text = '<ul class="pagination">\n';
    if(current_page == 1)
      text += '<li class="disabled"><a href="javascript:void(0)">«</a></li>';
    else
      text += '<li><a href="javascript:change_plugin_page('+(current_page-1)+')">«</a></li>';

    for (var i = 1; i <= nb_page; i++) {
      if(i == current_page)
        text += '<li class="active"><a href="javascript:change_plugin_page('+i+')">'+i+'</a></li>';
      else
        text += '<li><a href="javascript:change_plugin_page('+i+')">'+i+'</a></li>';
    };
    
    if(current_page == nb_page)
      text += '<li class="disabled"><a href="javascript:void(0)">»</a></li>';
    else
      text += '<li><a href="javascript:change_plugin_page('+(current_page+1)+')">»</a></li>';
    $("#pagination_plugins").html(text);
  }
  else{
    $("#pagination_plugins").html("");
  }
}

$("body").on("click", ".plugin_install", function(){
  plugin_id = $(this).data("pluginid");
  console.log("id :"+plugin_id);
  current_plugin = null;
  for (var i = 0; i < list_plugins.length; i++) {
    console.log(list_plugins[i]["id"] == plugin_id);
    if(list_plugins[i]["id"] == plugin_id)
      current_plugin = list_plugins[i];
    console.log("current_plugin : "+current_plugin);
  };
  console.log("current_plugin : "+current_plugin);
  if(current_plugin != null)
    alert("Le plugin \""+current_plugin.name+"\" ne peux pas encore être installé");
  else
    alert("Une erreur c'est produite");
});

////////////////
// Tab config //
////////////////
$("#profil_update").submit(function(){
  username = $("#username").val();
  email = $("#email").val();
  pass = $("#pass").val();
  pass_conf = $("#pass_conf").val();

  if(pass != pass_conf){
    if(pass != pass_conf)
      alert("le mot de passe est différent du mot de passe de confirmation");
    return false;
  }
  $.ajax({
        type: "POST",
        url: api_url+"?set=user_info",
        data: {username : username, pass:pass, email:email},
        success: function(json){
          var data = JSON.parse(json);
          if(data.status){
            notify("success", "sauvegardé");
          }
        },
        error: function(data){
          notify("error", "une erreur c'est produite, reessayer ultérieurement");
        }
    });
  return false;
})
