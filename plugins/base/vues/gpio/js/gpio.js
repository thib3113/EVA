$(function(){
    refresh_time = 5;//s
    setTimeout(update_gpio, refresh_time*1000);

    gpio = [];
    function update_gpio(){
        this.ajaxConnexion = $.ajax({
            url: 'api.php?get=GPIO_STATE',
            datatype: 'json',
            success: function(data){
                // La fonction à éxécuter avec les données recu
                if(!$.parseJSON(data)){ //si le json reçu n'est pas réelement du json
                    console.log("Une erreur s'est produite lors du chargement des statut GPIO");
                }
                donneesRecu = $.parseJSON(data);
                if(donneesRecu.status){
                    gpio = donneesRecu.GPIO;
                    for (var i = 0; i < gpio.length; i++) {
                        if(gpio[i].state == 1)
                            text = '<button type="button" data-wiringpin="'+gpio[i].id+'" class="btn btn-success">on</button>';
                        else
                            text = '<button type="button" data-wiringpin="'+gpio[i].id+'" class="btn btn-warning">off</button>';
                        $("button[data-wiringpin=" +gpio[i].id + "]").replaceWith(text);
                    };
                }
                else{
                    console.log("Une erreur s'est produite lors du chargement des statut GPIO");
                }
            },
            error: function(data){
                    console.log("Une erreur s'est produite lors du chargement des statut GPIO");
            }

        });
        setTimeout(update_gpio, refresh_time*1000);
        return false;
    }
});