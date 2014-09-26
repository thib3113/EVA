function notify(statut, message){
        statut = typeof statut !== 'undefined' ? statut : "error";
        message = typeof message !== 'undefined' ? message : 'erreur inconnue';
        //choix du layout    
        if($(document).width() > 768)
            choiceLayout = 'bottomLeft';
        else
            choiceLayout = 'bottom';
        console.log(statut);
        return noty({
            layout: choiceLayout,
            theme: 'defaultTheme',
            type: statut,
            text: message, // can be html or string
            dismissQueue: false, // If you want to use queue feature set this true
            timeout: 5000, // delay for closing event. Set false for sticky notifications
            killer: true // for close all notifications before show

        });
    }

    var last_notif = 0;     
    $("form#form_sign_in").submit(function(e) {
            message = 'Une erreur inconnue c\'est produite';
            statut = "error";

            user   = $("#user").val();
            pass = $("#pass").val();
            remember_me = $("#remember_me").is(":checked");

            if(last_notif != 0)
                last_notif.close();

            $.ajax({
            url: 'index.php',        /* Il s'agit de l'url ou seront traitÃ¯Â¿Â½s les donnÃ¯Â¿Â½es */
            type: 'POST',            /* Il s'agit de la mÃ¯Â¿Â½thode employÃ¯Â¿Â½e */
            data : {user : user, pass : pass, remember_me : remember_me},
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