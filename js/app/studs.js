/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondate: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */

function changeStatus( value, select, unselect) {
	for( var i = 0; i < unselect.length; i++) {
		$("#"+unselect[i]+"-display-"+value+" label").removeClass("check");
		$("#"+unselect[i]+"-choice-"+value).prop( "checked", false );
	}
	$("#"+select+"-choice-"+value).prop( "checked", true );
	$("#"+select+"-display-"+value+" label").addClass("check");
}
function checkClick(){
	var value = $(this).data("value");
	var type = $(this).data("type");
	switch (type) {
		case "y":
			changeStatus(value,"y",["i","n"]);
			break;
		case "i":
			changeStatus(value,"i",["y","n"]);
			break;
		case "n":
			changeStatus(value,"n",["i","y"]);
			break;
	}
}

$(document).ready(function () {

	$("#poll_form").submit(function (event) {
		var name = $("#name").val();
		name = name.trim();

		if (name.length == 0) {
			event.preventDefault();
			var newMessage = $("#nameErrorMessage").clone();
			$("#message-container").empty();
			$("#message-container").append(newMessage);
			newMessage.removeClass("hidden");
			$('html, body').animate({
				scrollTop: $("#message-container").offset().top
			}, 750);
		}
	});

	$('.choice input:radio').on('change', function(){
	  $(this).parent().parent().find('.startunchecked').removeClass('startunchecked');
	});
	$('.startunchecked').on('click', function(){
	  $(this).removeClass('startunchecked');
	});
	$('.no input').on('focus', function(){
	  $(this).next().removeClass('startunchecked');
	});
	$('.yes div').on('click',checkClick);
	$('.no div').on('click',checkClick);
	$(".ifneedbe div").on('click',checkClick); 
});
