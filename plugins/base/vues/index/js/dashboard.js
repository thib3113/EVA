function dashboard(){
    this.lastWidgetId = 0;
    this.DonneesRecu;
    this.currentWidgetList = [];
    this.widgets = [];
    this.nextWidgetId = 0;
    this.newOrderRequest;
    this.order = [];

    this.setDonneesRecu = function(DonneesRecu){
        this.DonneesRecu = DonneesRecu;
    }

    this.setCurrentWidgetList = function(currentWidgetList){
        this.currentWidgetList = currentWidgetList;
    }

    this.addToWidgetList = function(new_widget){
        this.currentWidgetList.push(new_widget);
    }

    this.setNextWidgetId = function(nextWidgetId){
        // console.log(NextWidgetId);
        this.nextWidgetId = nextWidgetId;
    }


    this.getDonneesRecu = function(DonneesRecu){
        return this.DonneesRecu;
    }

    this.getCurrentWidgetList = function(currentWidgetList){
        return this.currentWidgetList;
    }

    this.getNextWidgetId = function(nextWidgetId){
        return this.nextWidgetId;
    }

    this.getCurrentWidget = function(){
        parent = this;

        //on récupère les nouveau widgets
        $.ajax({
            url: api_url+'?type=GET&API=WIDGET_ALL',
            datatype: 'json',
            type: "POST",
            data: {expiration : new Date().getTime()+3000},
            success: function(data){
                try{
                    donneesRecu = $.parseJSON(data);
                    if(donneesRecu.status){
                        parent.setDonneesRecu(donneesRecu);
                        parent.setCurrentWidgetList(donneesRecu.dashboard_list);
                        parent.callWidgets();
                    }
                    else{
                        return false;
                    }
                    }
                catch(e){
                    notify("error", "une erreur s'est produite");
                    console.log(e);
                }
            },
            error: function(data){
                message = 'erreur. Ressayer plus tard';
                notify(statut, message);
            }

        });
    }

    this.newOrder = function(newOrder){
        //on évite la requete si l'ordre n'as pas changé
        if(JSON.stringify(newOrder) == JSON.stringify(this.order))
            return false;

        this.order = newOrder;

        parent = this;
        if (typeof(this.newOrderRequest) != "undefined")
            this.newOrderRequest.abort();
        this.newOrderRequest = $.ajax({
            url: api_url+'?type=SET&API=WIDGET_ORDER',
            datatype: 'JSON',
            type: "POST",
            data: {change_order: new_order, expiration : new Date().getTime()+3000},
            success: function(data){
                try{
                    donneesRecu = $.parseJSON(data);
                    if(donneesRecu.status){
                        parent.setDonneesRecu(donneesRecu);
                        // parent.setCurrentWidgetList(donneesRecu.dashboard_list);
                        // parent.callWidgets();
                    }
                    else{
                        return false;
                    }
                }
                catch(e){
                    notify("error", "une erreur s'est produite lors de la mise à jour de l'ordre des widgets");
                    console.log(e);
                }
            },
            error: function(data){
                notify("error", "une erreur s'est produite lors de la mise à jour de l'ordre des widgets");
            }

        });
    }

    this.callWidgets = function(){
        i = this.getNextWidgetId();
        this.order[i] = i;
        parent_dashboard = this;
        //sert à attendre le retour de la requete
        this.widgets[i] = new widget(parent_dashboard, this.currentWidgetList[i], i, false, false)
        $.when(this.widgets[i].ajaxConnexion)
        .done(function(){
            length = (typeof(parent_dashboard.currentWidgetList) != "array")? objectSize(parent_dashboard.currentWidgetList) : parent_dashboard.currentWidgetList.length;
            if(i<length)
                parent_dashboard.callWidgets();
            else
                return true;
        });
        i++;
        this.setNextWidgetId(i);
    }

    this.updateWidgetList = function(){
        donneesRecu = "";
        console.log(this);
        this.ajaxConnexion = $.ajax({
            url: api_url+'?type=SET&API=WIDGET_LIST',
            datatype: 'json',
            type: "POST",
            data: {widget_list : this.currentWidgetList, expiration : new Date().getTime()+3000},
            success: function(data){
                // La fonction à éxécuter avec les données recu
                try{
                    donneesRecu = $.parseJSON(data);
                    if(donneesRecu.status){
                    }
                    else{
                        notify("error", "erreur lors de la sauvegarde de l'ajout du widget");
                    }
                }
                catch(e){
                    notify("error", "erreur lors de la sauvegarde de l'ajout du widget");
                    console.log(e);
                }
                return false;
            },
            error: function(data){
                    notify("error", "erreur lors de la sauvegarde de l'ajout du widget");
                return false;
            }

        });
    }

    this.askNewWidget = function(){
        new widget(this, "list", i, "addNewWidget", false);
    }

    this.addWidget = function(current){
        if($(current).val()== "")
            return false;


        i = this.getNextWidgetId();
        this.order[i] = i;
        this.widgets[i] = new widget(this, $(current).val(), i, false, true);
        this.currentWidgetList[i] = this.widgets[i].name;
        this.updateWidgetList();
        i++;
        this.setNextWidgetId(i);
    }

    this.createWaitingWidget = function(id){
        loader_text = '<svg class="loader" width="20px" height="20px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg>';
        // loader_text = '<div class="outline"><div class="circle"></div></div>';
        $("#add_dashboard").before('<div class="col-sm-4 tiers_height dashboard_element" id="dashboard_id_'+this.id+'" data-id="'+this.id+'"><div class="panel full_height panel-default loader_content"><div class="panel-heading"><span class="no-drag">Chargement du widget</span></div><div class="panel-body text-center">'+loader_text+'</div></div></div>');
    }

    //execution
    // console.log("récupération de la liste des wigets de cet utilisateur");
    this.getCurrentWidget();

}

