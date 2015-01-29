$(function () {
	loopTime = timer*10;
	time_between_loop = 10;

	full_width = 100;
	width_per_loop = full_width / (timer*100);
	last_width = 0;

	console.log(timer);
	function boucle(){
		last_width = last_width + width_per_loop;
		$("#redirect_progress div").width(last_width+"%");
		$("#redirect_progress div .sr-only").html(Math.round(last_width)+"%");
		if(last_width < full_width){
			setTimeout(boucle, time_between_loop);
		}
		else{
			document.location.href = toLink;
			$("#redirect_progress").remove();
			$("#redirect_text").append('<br><a class="btn btn-primary btn-lg" href="'+toLink+'" role="button">Cliquez ici si vous n\'êtes pas redirigé</a>');
		}
	}

	setTimeout(boucle, time_between_loop);
});