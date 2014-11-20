function dashboard(){
    this.lastWidgetId = 0;
    this.DonneesRecu;
    this.currentWidgetList = [];
    this.widgets = [];
    this.nextWidgetId = 0;

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
        parent = this;
        $.ajax({
            url: 'index.php?page=dashboard',
            datatype: 'json',
            type: "POST",
            data: {change_order: new_order},
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

    this.callWidgets = function(){
        i = this.getNextWidgetId();
        parent_dashboard = this;
        //sert à attendre le retour de la requete
        $.when(this.widgets[i] = new widget(this.currentWidgetList[i], i))
        .done(function(){
            if(i<parent_dashboard.currentWidgetList.length)
                parent_dashboard.callWidgets();
            else
                return true;
        });
        i++;
        this.setNextWidgetId(i);
    }

    //execution
    // console.log("récupération de la liste des wigets de cet utilisateur");
    this.getCurrentWidget();

}