$(function(){
    refresh_time = 5;//s
    refresh_id = setTimeout(update_gpio, refresh_time*1000);

    gpio = [];
    ajaxConnexion = "";
    function update_gpio(){
        ajaxConnexion = $.ajax({
            url: 'api/v1/?get=GPIO_STATE',
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
                            text = '<button type="button" data-state="1" data-wiringpin="'+gpio[i].id+'" class="btn btn-success change_state">on</button>';
                        else
                            text = '<button type="button" data-state="0" data-wiringpin="'+gpio[i].id+'" class="btn btn-warning change_state">off</button>';
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
        refresh_id = setTimeout(update_gpio, refresh_time*1000);
        return false;
    }

    $("body").on("click", ".change_state", function(){
        clearTimeout(refresh_id);
        WPIN = $(this).data("wiringpin");
        state = Math.abs($(this).data("state")-1);
        $.ajax({
            url: 'api/v1/?set=GPIO_STATE&GPIO_STATE='+state+'&GPIO='+WPIN,
            datatype: 'json',
            success: function(data){
                // La fonction à éxécuter avec les données recu
                if(!$.parseJSON(data)){ //si le json reçu n'est pas réelement du json
                    alert("Une erreur s'est produite lors du chargement des statut GPIO");
                }
                donneesRecu = $.parseJSON(data);
                if(donneesRecu.status){
                        if(donneesRecu.state == 1)
                            text = '<button type="button" data-state="1" data-wiringpin="'+donneesRecu.wiringpin+'" class="btn btn-success change_state">on</button>';
                        else
                            text = '<button type="button" data-state="0" data-wiringpin="'+donneesRecu.wiringpin+'" class="btn btn-warning change_state">off</button>';
                        $("button[data-wiringpin=" +donneesRecu.wiringpin + "]").replaceWith(text);
                }
                else{
                    alert("Une erreur s'est produite lors du chargement des statut GPIO");
                }
            },
            error: function(data){
                    alert("Une erreur s'est produite lors du chargement des statut GPIO");
            }

        });
        refresh_id = setTimeout(update_gpio, refresh_time*1000);
    })
});