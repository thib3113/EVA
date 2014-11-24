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
            url: 'index.php?page=dashboard&dashboard=get_all',
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
        console.log(this);
        console.log(i<parent_dashboard.currentWidgetList.length);
        console.log(typeof(parent_dashboard.currentWidgetList));
        console.log(i);
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

    //execution
    // console.log("récupération de la liste des wigets de cet utilisateur");
    this.getCurrentWidget();

}