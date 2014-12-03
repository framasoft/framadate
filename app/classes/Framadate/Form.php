<?php
namespace Framadate;

class Form
{

    public $titre;
    public $commentaires;
    public $nom;
    public $adresse;
    public $formatsondage;
    public $champdatefin;
    public $choix_sondage;
    public $studsplus;
    public $mailsonde;
    public $toutchoix;
    public $totalchoixjour;
    public $horaires;

    /**
     * Step of form
     */
    public $step = 0;

    /**
     * List of available choices
     */
    private $choices;

    public function __construct(){
        $this->clearChoices();
    }

    public function clearChoices() {
        $this->choices = array();
    }

    public function addChoice(Choice $choice)
    {
        $this->choices[] = $choice;
    }

    public function getChoices()
    {
        return $this->choices;
    }

    public function sortChoices()
    {
        usort($this->choices, array('Framadate\Choice', 'compare'));
    }

    public function lastChoice()
    {
        return end($this->choices);
    }

}