$(function () {
    var dashboard_list;
    var dashboard_id = 0;

    dashboard = new dashboard("default", 0);

    $( "#dashboard" ).sortable({ 
        items: "> .sortable ",
        cancel: "a,button,.panel-body,.selectable_text",
        placeholder: "ui-state-highlight",
        forcePlaceholderSize: true ,
        containment: "parent" ,
        stop: function( event, ui ) {
            new_order = [];
            $("#dashboard > .sortable").each(function( i ){
                new_order[i] = $(this).data("id");
                i++;
            }
            );
            // console.log(new_order);
            dashboard.newOrder(new_order);
            // console.log(ui);
        },
    });
    // $( "#dashboard" ).disableSelection({ items: "> .sortable > .panel-heading" });



    // widget = new widget("default", 0);

    // function affichWidget(id, dashboard_element, custom_theme){
    //     if($.type(custom_theme) === "undefined")
    //         $("#add_dashboard").before('<div class="col-sm-'+($.isNumeric(dashboard_element['dash_width'])? dashboard_element['dash_width'] : 4)+' tiers_height dashboard_element" style="display:none;" id="dashboard_id_'+id+'">\n<div class="panel full_height panel-default">\n<div class="panel-heading">'+dashboard_element['dash_title']+'</div>\n<div class="panel-body">'+dashboard_element['dash_content']+'\n</div>\n</div>\n</div>');
    //     else
    //         $("#add_dashboard").before(eval(custom_theme));

    //     $('#dashboard_id_'+id+'').show(500);
    // }

    // function getDashboard(dashboard, success_function){
    //     var donneesRecu;
    //     $.ajax({
    //         url: 'index.php?page=dashboard&dashboard='+dashboard,
    //         async: false,
    //         datatype: 'json',
    //         success: function(data){
    //             // La fonction à éxécuter avec les données recu
    //             if(!$.parseJSON(data)){ //si le json reçu n'est pas réelement du json
    //                 message = 'erreur. Ressayer plus tard';
    //                 notify(statut, message);
    //             }
    //             donneesRecu = $.parseJSON(data);
    //             if(donneesRecu.status){
    //                 window.[success_function](donneesRecu);
    //             }else{
    //                 return false;
    //             }
    //         },
    //         error: function(data){
    //             message = 'erreur. Ressayer plus tard';
    //             notify(statut, message);
    //         }

    //     });


    //     return (donneesRecu.status)?donneesRecu : false;
    // }

    // $("body").ready(function() {



    //     result = getDashboard('get_all');

    //     if(!result.dashboard_list)
    //         dashboard_list = ['default'];
    //     else
    //         dashboard_list = $.map(result.dashboard_list, function(value, index) {return [value];});

    //     //on crée le chargement
    //     $("#add_dashboard").before('<div class="col-sm-4 tiers_height dashboard_element cursor_pointer" id="wait_dashboard"><div class="panel full_height panel-default"><div class="panel-body text-center"><i class="fa fa-spin fa-refresh fa-5x"></i></div></div></div>');

    //     for (var i = 0; i < dashboard_list.length; i++) {
    //         dashboard_element = getDashboard(dashboard_list[i]);
    //         dashboard_id++;
    //     };

    //     //on efface le loading
    //     $("#wait_dashboard").remove();

    //     $("#add_dashboard").click(function(){
    //         widget_list = getDashboard("get_list").widget_list;
    //         console.log(widget_list);
    //         $("#add_dashboard").before('<div class="col-sm-4 tiers_height dashboard_element" style="display:none;" id="dashboard_id'+dashboard_id+'">\n<div class="panel full_height panel-default">\n<div class="panel-heading">Ajouter un nouveau widget</div>\n<div class="panel-body" id="add_widget_list">Chargement&nbsp;<i class="fa fa-refresh fa-spin"></i>\n</div>\n</div>\n</div>');

    //         select_list = '<div class="dropdown">\n<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">Selectionner le widget à afficher\n<span class="caret"></span>\n</button>\n<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">\n';
    //         for (var i = 0;i < widget_list.length; i++) {
    //             select_list += '<li role="presentation"><a role="menuitem" tabindex="-1" onclick="add_widget(\''+widget_list[i][0]+'\');">'+widget_list[i][1]+'</a></li>\n';
    //         };
    //         select_list += '</ul></div>';
    //         $("#add_widget_list").html(select_list);
    //         $('#dashboard_id'+dashboard_id+'').show(0);
    //     });
    // });

});