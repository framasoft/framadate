$(document).ready(function(){
	FramaClassic = {
		//initialise les méthodes
		init : function(){
			FramaClassic.vars();
			FramaClassic.loggin();
			FramaClassic.catchForm();
			FramaClassic.anim();
			FramaClassic.adjustHeight();
		},
		//garde un modele de l'objet json
		vars : function(){
			FramaClassic.HtmlForm = $('#user_infos');
			FramaClassic.JsonObject = 
			{
				infos : {
					titre : "",
					commentaire : "",
					nom : "",
					mail : "",
					option_modif : "",//renvoie false/true
					option_mail : "",//renvoie false/true
					option_date_fin : "",
					//entries : {[I1,I2,I3,...]},
					//entries : {[]},
					entries : []
				},
				/*
				users : {
					Prenom1 : {[o,n,o,...]},
					Prenom2 : {[n,n,n,...]}
				},
				*/
				users : {},
				/*
				commentaires : {
					prenom  : "",
					prenomBis : ""
				}
				*/
				commentaires : {}

			}
		},
		anim : function(){
			setTimeout(function(){
			$('.wait').removeClass('wait');
			},10)
		},
		loggin : function(){
			//console.log(FramaClassic.HtmlForm);
			//console.log(FramaClassic.submitBut);
			//console.log(FramaClassic.JsonObject);
		},
		catchForm : function(){
			//lors de l'envoi du formulaire :
			FramaClassic.HtmlForm.submit(function(e){
				//retrait du comportement par défaut
				e.preventDefault();
				//mise à jour du cache du formulaire
				FramaClassic.HtmlForm = $('#user_infos');
				//alimentation de l'objet Json
				FramaClassic.jsonForm(FramaClassic.HtmlForm);
				//jeu d'ajout/retrait de classes, menant à l'étape suivante
				$('div#entry_form').removeClass('active').addClass('inactive');
				$('div#choices').removeClass('inactive').addClass('active');
				//mise à jour & initialisation des méthodes liées
				FramaClassic.adjustHeight();
				FramaClassic.moreChoices();
				FramaClassic.getEntries();
			})
		},
		jsonForm : function(data){
			FramaClassic.JsonObject = 
			{
				infos : {
					titre : data.find('input[name="titre"]').val(),
					commentaire : data.find('textarea[name="commentaire"]').text(),
					nom : data.find('input[name="nom"]').val(),
					mail : data.find('input[name="mail"]').val(),
					option_modif : data.find('input[name="modif"]').is(':checked'),
					option_mail : data.find('input[name="receive_mail"]').is(':checked'),
					option_date_fin : "",
					//entries : {[I1,I2,I3,...]},
					//entries : {[]},
					entries : []
				},
				/*
				users : {
					Prenom1 : [o,n,o,...],
					Prenom2 : [n,n,n,...]
				},
				*/
				users : {},
				/*
				commentaires : {
					prenom  : "",
					prenomBis : ""
				}
				*/
				commentaires : {}

			};
		},
		//ajustement de la taille de la div contenante à son contenu
		adjustHeight : function(){
			$('div[role="Main"]').height($('.active').outerHeight()+30);
		},
		//ajout de choix (par 1 ou par 5)
		moreChoices : function(){
			var button = $('.add_entries').find('button');
			var entries_length = $('#choices').find('label').length;
			
			button.on('click',function(){
				//Ajout de 5 choix supplémentaires
				//for (var i = 0; i <= 4; i++) {
				//	entries_length += 1;	
				//	var list_model = '<label for="choix_'+entries_length+'"><p>Choix '+entries_length+' :</p></label><input id="choix_'+entries_length+'" class="choice" type="text" name="Choix_'+entries_length+'" value="">';
				//	$('#choices').find('label:last').append(list_model);
				//};

				//Ajout d'un choix supplementaire
				entries_length += 1;
				var list_model = '<label for="choix_'+entries_length+'"><p>Choix '+entries_length+' :</p></label><input id="choix_'+entries_length+'" class="choice" type="text" name="Choix_'+entries_length+'" value="">';
				$('#choices').find('input:last').after(list_model);
				FramaClassic.adjustHeight();
			})
		},
		//alimente l'objet JSON avec les entrées
		getEntries : function(){
			$('#choices .continue').on('click',function(){
				//FramaClassic.JsonObject.infos.entries = [];
				$('#choices').find('label ~ input.choice').each(function(){
					if ($(this).val() !== ''){
						FramaClassic.JsonObject.infos.entries.push($(this).val());
						console.log($(this).val());
					}
					else {}
				})
				if (FramaClassic.JsonObject.infos.entries[0] == undefined){
					alert('Vous devez renseigner au moins un choix');
				}
				else {
					$('#choices').removeClass('active').addClass('inactive');
					$('#action').removeClass('inactive').addClass('active');

					FramaClassic.actionTable();
					FramaClassic.adjustHeight();
				}
			})
		},
		//génére le tableau de choix des entrées
		actionTable : function(){
			var headContent, bodyContent;
			var tableTemplate = "<table><thead><tr>"+headContent+"</tr></thead><tbody>"+bodyContent+"</tbody></table>";

			var headEntries = FramaClassic.JsonObject.infos.entries;

			var ok = '<button class="ok">ok</button>';

			var headTh = '<th></th>';

			var bodyTd = '<td><input type="text" name="active_name" value="'+FramaClassic.JsonObject.infos.nom+'" placeholder=""></td>';
			
			for (var i = 0; i <= headEntries.length-1; i++) {
				headTh += '<th class="head_entry">'+headEntries[i]+'</th>';
				bodyTd += '<td><input name="modif" type="checkbox"></input></td>';
			};

			headTh += '<th class="ok_field"></th>';

			bodyTd += '<td class="ok_field">'+ok+'</td>';

			var tableTemplate = "<table><thead><tr>"+headTh+"</tr></thead><tbody><tr class='choice_line active'>"+bodyTd+"</tr></tbody></table>";

			$('table').replaceWith(tableTemplate);

			//identifie la plus grande largeur et l'applique aux td de thead 
			//!necessité d'inserer le template dans le dom pour faire ce controle

			var THeadz = $('table').find('thead').find('.head_entry');
			var width_counter = 0;

			THeadz.each(function(){
				var tempwidth = parseInt($(this).css('width'));
				if (tempwidth >= width_counter){width_counter = tempwidth};
			});

			width_counter = width_counter+(width_counter*0.1);

			THeadz.each(function(){
				$(this).css('width',width_counter);
			});

			FramaClassic.addLine();
			FramaClassic.printJSON();
		},
		//ajoute un nouvel utilisateur
		addLine : function(){
			//attachement dynamique
			$(document).on('click','button.ok',function(){
				var name = $(this).parent().parent().find('td input[name="active_name"]').val();

				//si le nom est vide, alerte
				if (name == ''){
					return alert('Indiquez un nom svp');
				}

				$(this).parent().parent().find('td input[name="active_name"]').replaceWith(name);
				
				var colNum = $(this).parent().parent().find('td').length;
				
				var name_entry_template = '<td><input type="text" name="active_name" value="" placeholder=""></td>';
				var ok = '<button class="ok">ok</button>';

				for (var i = 0; i <= colNum-3; i++) {
					name_entry_template += '<td><input name="modif" type="checkbox"></input></td>';
				};

				//ajouter une condition pour ne pas rajouter +d'1 ligne à la fois
				var activeLines = $('tr.active');

				var activeOk = activeLines.find('button.ok');
				activeOk.remove();

				//var selected = activeLines.find('[type="checkbox"]:checked');
				//selected.parent().addClass('ok');

				var flag = '',
					choice_sequence = [];
				activeLines.find('input[name="modif"]').each(function(){

					if ($(this).is(':checked')){
						$(this).parent().addClass('ok');
						flag = "o";
					}
					else {
						flag = "n";
					}

					choice_sequence.push(flag);

				})
				FramaClassic.JsonObject.users[name] = choice_sequence;

				activeLines.removeClass('active').addClass('chosen');

				var newLine  = '<tr class="choice_line active">'+name_entry_template+'<td>'+ok+'</td></tr>';
				$('table tbody').append(newLine);
				FramaClassic.adjustHeight();
			})
		},
		printJSON : function(){
			$('button[type="print"]').click(function(){
				if(confirm('Voulez-vous visualiser le fichier JSON ?'))
				{
					$('#printJSON').find('code').html(JSON.stringify(FramaClassic.JsonObject, null, 4));
					var newHeight = parseInt($('#printJSON').find('code').css('height'));
					newHeight = newHeight + 40;
					$('#action').removeClass('active').addClass('inactive');
					$('#printJSON').removeClass('inactive').addClass('active');
					$('div[role="Main"]').css('height',newHeight);
					var JSONheader = $('<div class="json_title">Contenu du fichier JSON :</div>');
					$('div[role="Main"]').prepend(JSONheader);
					$('h1').css('left','500px');
				}
			})
		}
	};
	FramaClassic.init();
})