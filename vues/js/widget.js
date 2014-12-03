function widget(name, id, special, new_widget){
    this.id = id;
    this.donneesRecu;
    this.title;
    this.content;
    this.width;
    this.HTML;
    this.name = name;
    this.ajaxConnexion;
    this.resizable;
    this.widgetType = (typeof(special) != "undefined")? special : false;
    this.new_widget = (typeof(new_widget) != "undefined")? "&new_widget=1" : "";

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
        this.width = width;
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
                    $('#dashboard_id_'+this.id).replaceWith('<div class="col-sm-'+this.getWidth()+' tiers_height dashboard_element sortable" id="dashboard_id_'+this.id+'" data-id="'+this.id+'">\n<div class="panel full_height panel-default">\n<div class="panel-heading"><span class="selectable_text">'+this.title+'</span><span class="float_right selectable_text toggle_widget" style="cursor:pointer" onclick="$(this.parentNode.parentNode.getElementsByTagName(\'div\')[1]).toggle(500);this.style.transform=(this.style.transform==\'rotate(180deg)\')?\'rotate(0deg)\':\'rotate(180deg)\';"><i class="fa fa-angle-double-down"></i></span></div>\n<div class="panel-body">'+this.content+'\n</div>\n</div>\n</div>');
                break;

                case "waiting":
                    code_loader = '<div class="outline"><div class="circle"></div></div>';
                    if(!$('#dashboard_id_'+this.id).length)
                        $("#add_dashboard").before('<div class="col-sm-4 tiers_height dashboard_element" id="dashboard_id_'+this.id+'" data-id="'+this.id+'"><div class="panel full_height panel-default loader_content"><div class="panel-heading"><span class="selectable_text">Chargement du widget</span></div><div class="panel-body text-center">'+code_loader+'</div></div></div>');
                    else
                        $('#dashboard_id_'+this.id).replaceWith('<div class="col-sm-4 tiers_height dashboard_element" id="dashboard_id_'+this.id+'" data-id="'+this.id+'"><div class="panel full_height panel-default loader_content"><div class="panel-heading"><span class="selectable_text">Chargement du widget</span></div><div class="panel-body text-center">'+code_loader+'</div></div></div>');
                break;

                case "addNewWidget":
                    list = '<select onchange="dashboard.addWidget(this);" class="form-control input-lg">\n\t\t<option value="">Choisissez votre nouveau widget</option>\n';
                    for (var i = 0; i < this.donneesRecu.widget_list.length; i++) {
                        list += '\t\t<option value="'+this.donneesRecu.widget_list[i][0]+'">'+this.donneesRecu.widget_list[i][1]+'</option>\n';
                    };
                    list += '\t\t</select>';
                    $('#dashboard_id_'+this.id).replaceWith('<div class="col-sm-4 tiers_height dashboard_element" id="dashboard_id_'+(this.id)+'" data-id="'+(this.id)+'"><div class="panel full_height panel-default"><div class="panel-heading">Ajouter un nouveau widget<span class="float_right selectable_text"  style="cursor:pointer" onclick="$(this.parentNode.parentNode.parentNode).remove();">X</span></div><div class="panel-body">\n'+list+'\n</div></div></div>');
                break;

                case "add_button":

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
            url: 'index.php?page=dashboard&dashboard='+this.getName()+this.new_widget,
            datatype: 'json',
            success: function(data){
                // La fonction à éxécuter avec les données recu
                if(!$.parseJSON(data)){ //si le json reçu n'est pas réelement du json
                    message = 'erreur. Ressayer plus tard';
                    notify(statut, message);
                }
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
                else
                    notify("error", donneesRecu.message);
                return false;
            },
            error: function(data){
                message = 'erreur. Ressayer plus tard';
                notify(statut, message);
                return false;
            }

        });
    }

    this.newWidget = function(){
        parent = this;
        $.ajax({
            url: 'index.php?page=dashboard&dashboard=get_list',
            datatype: 'json',
            success: function(data){
                if(!$.parseJSON(data)){
                    message = 'erreur. Ressayer plus tard';
                    notify(statut, message);
                }
                donneesRecu = $.parseJSON(data);
                if(donneesRecu.status){

                }
                else{
                    return false;
                }
            },
            error: function(data){
                message = 'erreur. Ressayer plus tard';
                notify(statut, message);
            }

        });
    }





    //execution
    // console.log("On crée l'objet d'attente");
    //on crée le widget d'attente
    this.createWidget("waiting");
    // console.log("On récupère le widget");
    // on récupère les informations du widget depuis le serveur
    this.getWidget();
    return this.ajaxConnexion;
}