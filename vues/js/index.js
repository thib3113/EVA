$(function () {
    var dashboard_list;

    function getDashboard(dashboard){
        var donneesRecu;
        $.ajax({
            url: 'index.php?page=index&dashboard='+dashboard,        /* Il s'agit de l'url ou seront traitÃ¯Â¿Â½s les donnÃ¯Â¿Â½es */
            type: 'POST',            /* Il s'agit de la mÃ¯Â¿Â½thode employÃ¯Â¿Â½e */
            data : {},
            async: false,
            datatype: 'json',
            success: function(data){
                // La fonction à éxécuter avec les données recu 
                if(!$.parseJSON(data)){ //si le json reçu n'est pas réelement du json
                    message = 'erreur. Ressayer plus tard';
                    notify(statut, message);
                }
                donneesRecu = $.parseJSON(data);
                if(donneesRecu.status){

                }else{
                    return false;
                }
            },
            error: function(data){
                message = 'erreur. Ressayer plus tard';
                notify(statut, message);
            }

        });


        return (donneesRecu.status)?donneesRecu : false;
    }

    $("body").ready(function() {
    
    

        result = getDashboard('get_all');

        if(!result.dashboard_list)
            dashboard_list = ['default'];
        else
            dashboard_list = $.map(result.dashboard_list, function(value, index) {return [value];});
        
        //on crée le chargement
        $("#add_dashboard").before('<div class="col-sm-4 tiers_height dashboard_element cursor_pointer" id="wait_dashboard"><div class="panel full_height panel-default"><div class="panel-body text-center"><i class="fa fa-spin fa-refresh fa-5x"></i></div></div></div>');

        for (var i = 0; i < dashboard_list.length; i++) {
            dashboard_element = getDashboard(dashboard_list[i]);
            $("#add_dashboard").before('<div class="col-sm-'+($.isNumeric(dashboard_element['dash_width'])? dashboard_element['dash_width'] : 4)+' tiers_height dashboard_element" style="display:none;" id="dashboard_id'+i+'">\n<div class="panel full_height panel-default">\n<div class="panel-heading">'+dashboard_element['dash_title']+'</div>\n<div class="panel-body">'+dashboard_element['dash_content']+'\n</div>\n</div>\n</div>');
            $('#dashboard_id'+i+'').show(500);
        };

        //on efface le loading
        $("#wait_dashboard").remove();

        $("#add_dashboard").click(function(){
            getDashboard('get_list');
            console.log("click");
        });


    });

});