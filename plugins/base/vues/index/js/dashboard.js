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
        $.ajax({
            url: 'index.php?dashboard=get_all',
            datatype: 'json',
            success: function(data){
                if(!$.parseJSON(data)){
                    message = 'erreur. Ressayer plus tard';
                    notify(statut, message);
                }
                donneesRecu = $.parseJSON(data);
                if(donneesRecu.status){
                    parent.setDonneesRecu(donneesRecu);
                    parent.setCurrentWidgetList(donneesRecu.dashboard_list);
                    parent.callWidgets();
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

    this.newOrder = function(newOrder){
        //on évite la requete si l'ordre n'as pas changé
        if(newOrder.toSource() == this.order.toSource())
            return false;

        this.order = newOrder;

        parent = this;
        if (typeof(this.newOrderRequest) != "undefined")
            this.newOrderRequest.abort();
        this.newOrderRequest = $.ajax({
            url: 'index.php?page=dashboard',
            datatype: 'JSON',
            type: "POST",
            data: {change_order: new_order},
            success: function(data){
                if(!$.parseJSON(data)){
                    message = 'erreur. Ressayer plus tard';
                    notify(statut, message);
                }
                donneesRecu = $.parseJSON(data);
                if(donneesRecu.status){
                    // parent.setDonneesRecu(donneesRecu);
                    // parent.setCurrentWidgetList(donneesRecu.dashboard_list);
                    // parent.callWidgets();
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

    this.callWidgets = function(){
        i = this.getNextWidgetId();
        this.order[i] = i;
        parent_dashboard = this;
        //sert à attendre le retour de la requete
        $.when(this.widgets[i] = new widget(this.currentWidgetList[i], i))
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

    this.askNewWidget = function(){
        new widget("get_list", i, "addNewWidget");
    }

    this.addWidget = function(current){
        if($(current).val()== "")
            return false;


        i = this.getNextWidgetId();
        this.order[i] = i;
        this.widgets[i] = new widget($(current).val(), i, false, true);
        i++;
        this.setNextWidgetId(i);

        console.log(current.parentNode.parentNode.parentNode);
        console.log(this.nextWidgetId+1);
    }

    this.createWaitingWidget = function(id){
        loader_text = '<svg class="loader" width="20px" height="20px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg>';
        // loader_text = '<div class="outline"><div class="circle"></div></div>';
                        $("#add_dashboard").before('<div class="col-sm-4 tiers_height dashboard_element" id="dashboard_id_'+this.id+'" data-id="'+this.id+'"><div class="panel full_height panel-default loader_content"><div class="panel-heading"><span class="selectable_text">Chargement du widget</span></div><div class="panel-body text-center">'+loader_text+'</div></div></div>');
    }

    //execution
    // console.log("récupération de la liste des wigets de cet utilisateur");
    this.getCurrentWidget();

}