function widget(name, id){
    this.id = id;
    this.donneesRecu;
    this.title;
    this.content;
    this.width;
    this.HTML;
    this.name = name;
    this.ajaxConnexion;
    this.type;

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

    this.createWaitingWidget = function(){
        $("#add_dashboard").before('<div class="col-sm-4 tiers_height dashboard_element" id="dashboard_id_'+this.id+'" data-id="'+this.id+'"><div class="panel full_height panel-default"><div class="panel-heading"><span class="selectable_text">Chargement du widget</span></div><div class="panel-body text-center"><i class="fa fa-circle-o-notch fa-spin fa-4x loading_icon"></i></div></div></div>');
    }

    this.createWidget = function(){
        if(!this.HTML){
            switch(this.type){
                case "widget":
                    $('#dashboard_id_'+this.id).replaceWith('<div class="col-sm-'+this.getWidth()+' tiers_height dashboard_element sortable" id="dashboard_id_'+this.id+'" data-id="'+this.id+'">\n<div class="panel full_height panel-default">\n<div class="panel-heading"><span class="selectable_text">'+this.title+'</span></div>\n<div class="panel-body">'+this.content+'\n</div>\n</div>\n</div>');
                break;

                case "waiting":
                    $("#add_dashboard").before('<div class="col-sm-4 tiers_height dashboard_element" id="dashboard_id_'+this.id+'" data-id="'+this.id+'"><div class="panel full_height panel-default"><div class="panel-heading"><span class="selectable_text">Chargement du widget</span></div><div class="panel-body text-center"><i class="fa fa-circle-o-notch fa-spin fa-4x loading_icon"></i></div></div></div>');
                break;

                case "add":

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
            url: 'index.php?page=dashboard&dashboard='+this.getName(),
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
                    parent.type = "widget";
                    parent.createWidget();
                }
                else
                    notify(error, donneesRecu.message);
                return false;
            },
            error: function(data){
                message = 'erreur. Ressayer plus tard';
                notify(statut, message);
                return false;
            }

        });
    }





    //execution
    // console.log("On crée l'objet d'attente");
    //on crée le widget d'attente
    this.createWaitingWidget();
    // console.log("On récupère le widget");
    // on récupère les informations du widget depuis le serveur
    this.getWidget();
    return this.ajaxConnexion;
}