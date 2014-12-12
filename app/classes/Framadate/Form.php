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

    /**
     * Tells if users can modify their choices.
     */
    public $editable;

    /**
     * If true, notify poll administrator when new vote is made.
     */
    public $receiveNewVotes;

    /**
     * List of available choices
     */
    private $choices;

    public function __construct(){
        $this->editable = true;
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