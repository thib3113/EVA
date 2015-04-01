function dashboard(){
    this.lastWidgetId = 0;
    this.DonneesRecu;
    this.widgets = [];
    this.nextWidgetId = 0;
    this.newOrderRequest;
    this.newWidthRequest = null;
    this.order = [];

    this.setDonneesRecu = function(DonneesRecu){
        this.DonneesRecu = DonneesRecu;
    }

    this.setCurrentWidgetList = function(currentWidgetList){
        this.widgets = currentWidgetList;
    }

    this.addToWidgetList = function(new_widget){
        this.widgets.push(new_widget);
    }

    this.setNextWidgetId = function(nextWidgetId){
        // console.log(NextWidgetId);
        this.nextWidgetId = nextWidgetId;
    }


    this.getDonneesRecu = function(DonneesRecu){
        return this.DonneesRecu;
    }

    this.getCurrentWidgetList = function(currentWidgetList){
        return this.widgets;
    }

    this.getWidgetById = function(id){
        return this.widgets[id];
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

    this.setWidgetWidth = function(widget_id, width, callback){
        w = this.getWidgetById(widget_id);
        w.changeWidth(width);
        if(this.newWidthRequest != null )
            this.newWidthRequest.abort();

        this.newWidthRequest = $.ajax({
            url: api_url+'?type=SET&API=WIDGET_WIDTH',
            datatype: 'JSON',
            type: "POST",
            data: {widget_name: w.name, new_width: width, expiration : new Date().getTime()+3000},
            success: function(data){
                try{
                    donneesRecu = $.parseJSON(data);
                    if(donneesRecu.status){
                        parent.setDonneesRecu(donneesRecu);
                        callback();
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

    this.refreshWidget = function(id){
        this.widgets[id] = new widget(this, this.widgets[id], id);
    }

    this.newOrder = function(newOrder){

        console.log(newOrder);

        //si le tableau n'as qu'une valeur, il n'y à pas d'ordre
        if(newOrder.length < 2)
            return false;

        console.log(JSON.stringify(newOrder));
        console.log(JSON.stringify(this.order));
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
        for (var i = 0; i < this.widgets.length; i++) {
            this.widgets[i] = new widget(parent_dashboard, this.widgets[i], i, false, false);
        };
        this.setNextWidgetId(i);
    }

    this.exportAllWidgets = function(){
        temp = [];
        for (var i = this.widgets.length - 1; i >= 0; i--) {
            temp [i] = this.widgets[i].export();

        console.log(this.widgets[i]);
        };
        console.log(temp);
        return temp;
    }

    this.updateWidgetList = function(){
        donneesRecu = "";
        console.log(this);
        this.ajaxConnexion = $.ajax({
            url: api_url+'?type=SET&API=WIDGET_LIST',
            datatype: 'json',
            type: "POST",
            data: {widget_list : this.exportAllWidgets(), expiration : new Date().getTime()+3000},
            success: function(data){
                // La fonction à éxécuter avec les données recu
                try{
                    donneesRecu = $.parseJSON(data);
                    if(donneesRecu.status){
                    }
                    else{
                        notify("error", "erreur lors de la mise à jour des widgets");
                    }
                }
                catch(e){
                    notify("error", "erreur lors de la mise à jour des widgets");
                    console.log(e);
                }
                return false;
            },
            error: function(data){
                    notify("error", "erreur lors de la mise à jour des widgets");
                return false;
            }

        });
    }

    this.askNewWidget = function(){
        new widget(this, {"name":"list", "width":"4"}, this.getNextWidgetId(), "addNewWidget", false);
    }

    this.addWidget = function(current){
        if($(current).val()== "")
            return false;


        i = this.getNextWidgetId();
        this.order[i] = i;
        this.widgets[i] = new widget(this, {"name":$(current).val(), "width":4}, i, false, true);
         console.log(this.widgets[i]);
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

