dashboard;
$(function () {
    var dashboard_list;
    var dashboard_id = 0;

    dashboard = new dashboard();

    old_order = [];
    $( "#dashboard" ).sortable({ 
        items: "> .sortable ",
        cancel: "a,button,.no-drag",
        start: function(){
            $("#dashboard > .sortable:not(.ui-sortable-placeholder)").each(function( i ){
                old_order[i] = $(this).data("widget-id");
                i++;
            });
            dashboard.order = old_order;
        },
        stop: function( event, ui ) {
            new_order = [];
            $("#dashboard > .sortable").each(function( i ){
                new_order[i] = $(this).data("widget-id");
                i++;
            }
            );
            // console.log(ui);
        },
    });

    widget_id = null;
    $("body").on("click", "[data-role='change_width']", function(){
        widget_id = $(this).parents(".dashboard_element").data("widget-id");
        current_widget = dashboard.getWidgetById(widget_id);

        $( "#change_width_slider" ).slider( "value", current_widget.width );
        $( "#change_width" )[0].selectedIndex = current_widget.width-1;
        $('#width_modal').modal('show');
    });

    $("body").on("click", "[data-role='valid_width']", function(){
        if(isNaN(widget_id))
            return false;

        dashboard.setWidgetWidth(widget_id,$("#change_width").val());
        $('#width_modal').modal('hide');
    });

    var select = $( "#change_width" );
    var slider = $( '<div id="change_width_slider" class="col-lg-9 col-md-8 col-sm-12 col-xs-12" style="margin: 10px 0;"></div>' ).insertAfter( $("#change_width_select") ).slider({
      min: 1,
      max: 12,
      range: "min",
      value: select[ 0 ].selectedIndex + 1,
      slide: function( event, ui ) {
        select[ 0 ].selectedIndex = ui.value - 1;
      },
    });

    $( "#change_width" ).change(function() {
      slider.slider( "value", this.selectedIndex + 1 );
    });

    $("body").on("click", "[data-role='toggle_widget']", function(){
        $(this).parents(".panel").children(".panel-body").slideToggle(500);
        this.style.transform=(this.style.transform=='rotate(180deg)')?'rotate(0deg)':'rotate(180deg)';
    });

    $("body").on("click", "[data-role='refresh_widget']", function(){
        widget_id = $(this).parents(".dashboard_element").data("widget-id");
        dashboard.refreshWidget(widget_id);
    });

    $("body").on("click", "[data-role='delete_widget']", function(){
        
    });

    $("body").on("click", ".widget_menu_icon", function(event){
        widget_id = $(this).parents(".dashboard_element").data("widget-id");
        $(".widget_menu").hide(500);
        $(this).parents(".panel-heading").children(".widget_menu").show(500);
        event.stopPropagation();
    })

    $("body").on("click", '.widget_menu', function(event){
        event.stopPropagation();
    });

    $("html").click(function(){
        $(".widget_menu").each(function(index, element){
            $(element).hide(500);
        })
    })

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