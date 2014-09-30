$(function () {
    var dashboard_list;

    function getDashboard(dashboard){
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
                    return donneesRecu;
                }else{
                    return false;
                }
            },
            error: function(data){
                message = 'erreur. Ressayer plus tard';
                notify(statut, message);
            }

        });
    }

    dashboard_list = getDashboard('get_all');
    
    if(!dashboard_list)
        dashboard_list = ['default'];
    
    for (var i = 0; i < dashboard_list.length; i++) {

        console.log(dashboard_list[i]);
        getDashboard(dashboard_list[i]);
    };
});