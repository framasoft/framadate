<?php
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
namespace Framadate;

include_once __DIR__ . '/app/inc/init.php';

if (is_readable('bandeaux_local.php')) {
    include_once('bandeaux_local.php');
} else {
    include_once('bandeaux.php');
}

// affichage de la page
Utils::print_header( __('Generic\\Home') );
bandeau_titre(__('Generic\\Make your polls'));
echo '
        <div class="row">
            <div class="col-md-6 text-center">
                <p class="home-choice"><a href="'.Utils::get_server_name().'infos_sondage.php?choix_sondage=date" class="opacity" role="button">
                    <img class="img-responsive center-block" src="'.Utils::get_server_name().'images/date.png" alt="" />
                    <br /><span class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-calendar"></span>
                    '. __('Homepage\\Schedule an event') . '</span>
                </a></p>
            </div>
            <div class="col-md-6 text-center">
                <p class="home-choice"><a href="'.Utils::get_server_name().'infos_sondage.php?choix_sondage=autre" class="opacity" role="button">
                    <img alt="" class="img-responsive center-block" src="'.Utils::get_server_name().'images/classic.png" />
                    <br /><span class="btn btn-info btn-lg"><span class="glyphicon glyphicon-stats"></span>
                    '. __('Homepage\\Make a classic poll') . '</span>
                </a></p>
            </div>
        </div>
        <hr  role="presentation" />
        <div class="row">';
        $nbcol = $config['show_what_is_that'] + $config['show_the_software'] + $config['show_cultivate_your_garden'];
        if ($nbcol > 0){
            $colmd = 12/$nbcol; // 3 =>col-md-4, 2 =>col-md-6, 1 =>col-md-12.
        }
            if($config['show_what_is_that'] == true){
                echo '<div class="col-md-'.$colmd.'">
                <h3>'. __('1st section\\What is that?') . '</h3>
                <p class="text-center" role="presentation"><span class="glyphicon glyphicon-question-sign" style="font-size:50px"></span></p>
                <p>'. __('1st section\\Framadate is an online service for planning an appointment or make a decision quickly and easily. No registration is required.') .'</p>
                <p>'. __('1st section\\Here is how it works:') . '</p>
                <ol>
                    <li>'. __('1st section\\Make a poll') . '</li>
                    <li>'. __('1st section\\Define dates or subjects to choose') . '</li>
                    <li>'. __('1st section\\Send the poll link to your friends or colleagues') . '</li>
                    <li>'. __('1st section\\Discuss and make a decision') . '</li>
                </ol>
                <p>'. __('1st section\\Do you want to ') . '<a href="' . Utils::getUrlSondage('aqg259dth55iuhwm').'">'. __('1st section\\view an example?') .'</a></p>
                </div>';
            }

            if($config['show_the_software'] == true){
                echo '<div class="col-md-'.$colmd.'">
                <h3>'. __('2nd section\\The software') .'</h3>
                <p class="text-center" role="presentation"><span class="glyphicon glyphicon-cloud" style="font-size:50px"></span></p>
                <p>'. __('2nd section\\Framadate was initially based on '). '<a href="https://sourcesup.cru.fr/projects/studs/">Studs</a>'. __('2nd section\\ a software developed by the University of Strasbourg. Today, it is devevoped by the association Framasoft') .'.</p>
                <p>'. __('2nd section\\This software needs javascript and cookies enabled. It is compatible with the following web browsers:') .'</p>
                <ul>
                    <li>Microsoft Internet Explorer 9+</li>
                    <li>Google Chrome 19+</li>
                    <li>Firefox 12+</li>
                    <li>Safari 5+</li>
                    <li>Opera 11+</li>
                </ul>
                <p>'. __('2nd section\\It is governed by the ').'<a href="http://www.cecill.info">'. __('2nd section\\CeCILL-B license').'</a>.</p>
                </div>';
            }

            if($config['show_cultivate_your_garden'] == true){
                echo '<div class="col-md-'.$colmd.'">
                <h3>'. __('3rd section\\Cultivate your garden') .'</h3>
                <p class="text-center" role="presentation"><span class="glyphicon glyphicon-tree-deciduous" style="font-size:50px"></span></p>
                <p>'. __('3rd section\\To participate in the software development, suggest improvements or simply download it, please visit ') .'<a href="https://git.framasoft.org/framasoft/framadate">'.__('3rd section\\the development site').'</a>.</p>
                <br />
                <p>'. __('3rd section\\If you want to install the software for your own use and thus increase your independence, we help you on:') .'</p>
                <p class="text-center"><a href="http://framacloud.org/cultiver-son-jardin/installation-de-framadate/" class="btn btn-success"><span class="glyphicon glyphicon-tree-deciduous"></span> framacloud.org</a></p>
                </div>';
            }
        echo '</div>'."\n";

bandeau_pied();
