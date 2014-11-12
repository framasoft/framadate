<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondate: Framasoft (https://git.framasoft.org/framasoft/framadate)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://git.framasoft.org/framasoft/framadate)
 */
 
 /**
 * general configuration
 */
 
	 //is a smtp server is configured to send e-mail ?
	 //$use_smtp = true;
	 
	 //if only one language is allowed in constants.php, $ALLOWED_LANGUAGES, the language selection bar is useless
	 $show_language_bar_selection = true;
	 
 /**
 * index.php
 */
 
	  //display "how to use" section
	 $show_what_is_that = true;

	 //display technical information about the software
	 $show_the_software = true;

	 //display "developpement and administration" information
	 $show_cultivate_your_garden = true;
    
 /**
 * choix_autre.php
 */
	   //default values for the new poll duration (number of days). 
	  $default_poll_duration = 10;
  
  	   //user can add link or URL when creating his poll. 
	  $user_can_add_link_or_url = false;
  
  