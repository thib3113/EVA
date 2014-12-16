dashboard;
$(function () {
    var dashboard_list;
    var dashboard_id = 0;

    dashboard = new dashboard("default", 0);

    $( "#dashboard" ).sortable({ 
        items: "> .sortable ",
        cancel: "a,button,.panel-body,.selectable_text",
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

    $("#add_dashboard").click(function(){
        dashboard.askNewWidget();
        // onchange="dashboard.addWiget($(this).val());"
        // widget_list = getDashboard("get_list").widget_list;
        // console.log(widget_list);
        // $("#add_dashboard").before('<div class="col-sm-4 tiers_height dashboard_element" style="display:none;" id="dashboard_id'+dashboard_id+'">\n<div class="panel full_height panel-default">\n<div class="panel-heading">Ajouter un nouveau widget</div>\n<div class="panel-body" id="add_widget_list">Chargement&nbsp;<i class="fa fa-refresh fa-spin"></i>\n</div>\n</div>\n</div>');

        // select_list = '<div class="dropdown">\n<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">Selectionner le widget à afficher\n<span class="caret"></span>\n</button>\n<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">\n';
        // for (var i = 0;i < widget_list.length; i++) {
        //     select_list += '<li role="presentation"><a role="menuitem" tabindex="-1" onclick="add_widget(\''+widget_list[i][0]+'\');">'+widget_list[i][1]+'</a></li>\n';
        // };
        // select_list += '</ul></div>';
        // $("#add_widget_list").html(select_list);
        // $('#dashboard_id'+dashboard_id+'').show(0);
    });


});