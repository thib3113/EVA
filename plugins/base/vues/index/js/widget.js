function widget(parent, widget_info, id, special, new_widget){
    this.id = id;
    this.donneesRecu;
    this.title;
    this.content;
    this.HTML;
    this.name = widget_info.name;
    this.width = widget_info.width;
    this.ajaxConnexion;
    this.resizable;
    this.widgetType = (typeof(special) != "undefined")? special : false;
    this.new_widget = (typeof(new_widget) != "undefined")? new_widget : false;
    this.parent = parent;

    this.getId = function(){
        return this.id;
    }

    this.getDonneesRecu = function(){
        return this.donneesRecu;
    }

    this.getTitle = function(){
        return this.title;
    }

    this.getContent = function(){
        return this.content;
    }

    this.getName = function(){
        return this.name;
    }

    this.getWidth = function(){
        return ($.isNumeric(this.width)? this.width : 4);
    }

    this.changeWidth = function(width){
        $('#dashboard_id_'+this.id).removeClass("col-sm-"+this.width).removeClass("col-md-"+this.width).removeClass("col-lg-"+this.width);
        this.setWidth(width);
        $('#dashboard_id_'+this.id).addClass("col-sm-"+this.getWidth()).addClass("col-md-"+this.getWidth()).addClass("col-lg-"+this.getWidth());
    }   

    this.setId = function(id){
        this.id = id;
    }

    this.setDonneesRecu = function(donneesRecu){
        this.donneesRecu = donneesRecu;
    }

    this.setTitle = function(title){
        this.title = title;
    }

    this.setContent = function(content){
        this.content = content;
    }

    this.setName = function(name){
        this.name = name;
    }

    this.setWidth = function(width){
        this.width = parseInt(width);
    }

    this.setHTML = function(HTML){
        if (typeof(HTML) != "undefined")
            this.HTML = HTML;
        else
            this.HTML = false;
    }

    this.createWidget = function(type){
        if(!this.HTML){
            switch(type){
                case "widget":
                    $('#dashboard_id_'+this.id).replaceWith('\t\t\t<div class="col-sm-'+this.getWidth()+' col-md-'+this.getWidth()+' col-lg-'+this.getWidth()+' tiers_height dashboard_element sortable" id="dashboard_id_'+this.id+'" data-widget-id="'+this.id+'">\n\
\t\t\t\t<div class="panel full_height panel-default">\n\
\t\t\t\t\t<div class="panel-heading">\n\
\t\t\t\t\t\t<span class="widget_menu_icon float_right no-drag config-icon">\n\
\t\t\t\t\t\t\t<i class="md-apps"></i>\n\
\t\t\t\t\t\t</span>\n\
\t\t\t\t\t\t<span data-role="toggle_widget" class="float_right hidden-xs no-drag config-icon">\n\
\t\t\t\t\t\t\t<i class="md-expand-more"></i>\n\
\t\t\t\t\t\t</span>\n\
\t\t\t\t\t\t<span class="no-drag">'+this.title+'</span>\n\
\t\t\t\t\t\t<div style="display:none;" class="widget_menu no-drag">\n\
\t\t\t\t\t\t\t<div class="widget_config">\n\
\t\t\t\t\t\t\t\t<div class="config">\n\
\t\t\t\t\t\t\t\t\t<div data-role="toggle_widget" class="config-icon hidden-md hidden-sm hidden-lg">\n\
\t\t\t\t\t\t\t\t\t\t<i class="md-expand-more"></i>\n\
\t\t\t\t\t\t\t\t\t</div>\n\
\t\t\t\t\t\t\t\t</div>\n\
\t\t\t\t\t\t\t\t<div class="config">\n\
\t\t\t\t\t\t\t\t\t<div class="config-icon" data-role="change_width" style="position: relative;top: -4px;height: 64px;">\n\
\t\t\t\t\t\t\t\t\t\t↔\n\
\t\t\t\t\t\t\t\t\t</div>\n\
\t\t\t\t\t\t\t\t</div>\n\
\t\t\t\t\t\t\t\t<div class="config">\n\
\t\t\t\t\t\t\t\t\t<div data-role="refresh_widget" class="config-icon">\n\
\t\t\t\t\t\t\t\t\t\t<i class="md-refresh"></i>\n\
\t\t\t\t\t\t\t\t\t</div>\n\
\t\t\t\t\t\t\t\t</div>\n\
\t\t\t\t\t\t\t\t<div class="config">\n\
\t\t\t\t\t\t\t\t\t<div class="config-icon">\n\
\t\t\t\t\t\t\t\t\t\t<i class="md-close"></i>\n\
\t\t\t\t\t\t\t\t\t</div>\n\
\t\t\t\t\t\t\t\t</div>\n\
\t\t\t\t\t\t\t</div>\n\
\t\t\t\t\t\t</div>\n\
\t\t\t\t\t</div>\n\
\t\t\t\t\t<div class="panel-body no-drag">\n\
\t\t\t\t\t\t'+this.content+'\n\
\t\t\t\t\t</div>\n\
\t\t\t\t</div>\n\
\t\t\t</div>');
                break;

                case "waiting":
                    // code_loader = '<div class="outline"><div class="circle"></div></div>';
                    // code_loader = '<svg class="loader" width="20px" height="20px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg>';
                    code_loader = '<div class="loader"><svg class="circular"><circle class="path" cx="30" cy="30" r="20" fill="none" stroke-width="5" stroke-miterlimit="10"/></svg></div>';

                    if(!$('#dashboard_id_'+this.id).length)
                        $("#add_dashboard").before('<div class="col-sm-'+(!isNaN(this.width)? this.width : 4)+' tiers_height dashboard_element" id="dashboard_id_'+this.id+'" data-id="'+this.id+'"><div class="panel full_height panel-default loader_content"><div class="panel-heading"><span class="no-drag">Chargement du widget</span></div><div class="panel-body text-center">'+code_loader+'</div></div></div>');
                    else
                        $('#dashboard_id_'+this.id).replaceWith('<div class="col-sm-'+(!isNaN(this.width)? this.width : 4)+' tiers_height dashboard_element" id="dashboard_id_'+this.id+'" data-id="'+this.id+'"><div class="panel full_height panel-default loader_content"><div class="panel-heading"><span class="no-drag">Chargement du widget</span></div><div class="panel-body text-center">'+code_loader+'</div></div></div>');
                break;

                case "addNewWidget":
                    list = '<select onchange="dashboard.addWidget(this);" class="form-control input-lg">\n\t\t<option value="">Choisissez votre nouveau widget</option>\n';
                    for (var i = 0; i < this.donneesRecu.widget_list.length; i++) {
                        list += '\t\t<option value="'+this.donneesRecu.widget_list[i][0]+'">'+this.donneesRecu.widget_list[i][1]+'</option>\n';
                    };
                    list += '\t\t</select>';
                    $('#dashboard_id_'+this.id).replaceWith('<div class="col-sm-4 tiers_height dashboard_element" id="dashboard_id_'+(this.id)+'" data-id="'+(this.id)+'"><div class="panel full_height panel-default"><div class="panel-heading">Ajouter un nouveau widget<span class="float_right no-drag"  style="cursor:pointer" onclick="$(this.parentNode.parentNode.parentNode).remove();">X</span></div><div class="panel-body">\n'+list+'\n</div></div></div>');
                break;

                case "error":
                    $('#dashboard_id_'+this.id).replaceWith('<div class="col-sm-4 tiers_height dashboard_element" id="dashboard_id_'+this.id+'" data-id="'+this.id+'"><div class="panel full_height panel-default loader_content"><div class="panel-heading"><span class="no-drag">Erreur lors du chargement du widget</span></div><div class="panel-body text-center"><i style="color: rgb(183, 10, 10);" class="fa fa-exclamation-triangle fa-5x"></i><br>'+this.message+'</div></div></div>');
                break;
            }
        }
        else
            $('#dashboard_id_'+this.id).replaceWith(this.HTML);

        // console.log("Le widget "+this.id+" est affiché");
    }

    this.getWidget = function(){
        donneesRecu = "";
        parent = this;

        this.ajaxConnexion = $.ajax({
            url: api_url+'?type=GET&API=WIDGET_'+this.getName().toUpperCase(),
            datatype: 'json',
            type: "POST",
            data: {expiration : new Date().getTime()+3000},
            success: function(data){
                // La fonction à éxécuter avec les données recu
                try{
                    donneesRecu = $.parseJSON(data);
                    if(donneesRecu.status){
                        parent.setDonneesRecu(donneesRecu);
                        parent.setTitle(donneesRecu.dash_title);
                        parent.setContent(donneesRecu.dash_content);
                        parent.setWidth(donneesRecu.dash_width);
                        parent.setHTML(donneesRecu.HTML);
                        // console.log()
                        if(typeof(donneesRecu.executeFunction) == "undefined" || donneesRecu.executeFunction.length == 0){
                            if(!parent.widgetType)
                                    parent.createWidget("widget");
                            else{
                                parent.createWidget(parent.widgetType);
                            }
                        }
                        else{
                            window[donneesRecu.executeFunction](donneesRecu.arguments);
                        }
                    }
                    else{
                        parent.message = "Une erreur s'est produite lors du chargement du widget";
                        parent.createWidget("error");
                    }
                }
                catch(e){
                    parent.message = "Une erreur s'est produite lors du chargement du widget";
                    parent.createWidget("error");
                    console.log(e);
                }
                return false;
            },
            error: function(data){
                    parent.message = "Une erreur s'est produite lors du chargement du widget";
                    parent.createWidget("error");
                return false;
            }

        });
    }

    // this.newWidget = function(){
    //     parent = this;
    //     $.ajax({
    //         url: 'index.php?dashboard=get_list',
    //         datatype: 'json',
    //         success: function(data){
    //             if(!$.parseJSON(data)){
    //                 message = 'erreur. Ressayer plus tard';
    //                 notify(statut, message);
    //             }
    //             donneesRecu = $.parseJSON(data);
    //             if(donneesRecu.status){

    //             }
    //             else{
    //                 return false;
    //             }
    //         },
    //         error: function(data){
    //             message = 'erreur. Ressayer plus tard';
    //             notify(statut, message);
    //         }

    //     });
    // }





    //execution
    // console.log("On crée l'objet d'attente");
    //on crée le widget d'attente
    this.createWidget("waiting");
    // console.log("On récupère le widget");
    // on récupère les informations du widget depuis le serveur
    this.getWidget();
    return this;
}