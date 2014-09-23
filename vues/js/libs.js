function notify(statut, message){
        statut = typeof statut !== 'undefined' ? statut : "error";
        message = typeof message !== 'undefined' ? message : 'erreur inconnue';
        //choix du layout    
        if($(document).width() > 750)
            choiceLayout = 'bottomLeft';
        else
            choiceLayout = 'bottom';

        noty({
            layout: choiceLayout,
            theme: 'defaultTheme',
            type: statut,
            text: message, // can be html or string
            dismissQueue: false, // If you want to use queue feature set this true
            timeout: 5000, // delay for closing event. Set false for sticky notifications
            killer: true // for close all notifications before show

        });
    }