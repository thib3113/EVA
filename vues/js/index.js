$(function () {
    $("form#form_sign_in").submit(function(e) {
            if(last_notif != 0)
                last_notif.close();

            $.ajax({
            url: 'index.php?page=index&dashboard=get_all',        /* Il s'agit de l'url ou seront traitÃ¯Â¿Â½s les donnÃ¯Â¿Â½es */
            type: 'POST',            /* Il s'agit de la mÃ¯Â¿Â½thode employÃ¯Â¿Â½e */
            data : {},
            datatype: 'json',
            success: function(data){
                // La fonction à éxécuter avec les données recu 
                if(!$.parseJSON(data)){ //si le json reçu n'est pas réelement du json
                    message = 'erreur. Ressayer plus tard';
                    last_notif = notify(statut, message);
                }
                donneesRecu = $.parseJSON(data);
                if(donneesRecu.status == 'success'){
                    
                }else{
                    //anything else
                }
                message = donneesRecu.message;
                statut = donneesRecu.status;
                last_notif = notify(statut, message);
            },
            error: function(data){
                message = 'erreur. Ressayer plus tard';
                last_notif = notify(statut, message);
            }

        });
            return false;
    });
});