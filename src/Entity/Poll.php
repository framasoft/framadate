<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
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
namespace Framadate\Entity;

use Framadate\Editable;

class Poll
{
    const POLL_REGEX = '/^[a-z0-9-]*$/i';
    const ADMIN_POLL_REGEX = '/^[a-z0-9]{24}$/i';

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    private $admin_id;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $admin_name;

    /**
     * @var string
     */
    protected $admin_mail;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var \DateTime
     */
    protected $end_date;

    /**
     * @var string
     */
    protected $creation_date;

    /**
     * @var string
     */
    protected $choix_sondage;

    /**
     * @var string
     */
    protected $ValueMax;

    /**
     * Tells if users can modify their choices.
     * @var \Framadate\Editable
     */
    protected $editable;

    /**
     * If true, notify poll administrator when new vote is made.
     */
    protected $receiveNewVotes;

    /**
     * If true, notify poll administrator when new comment is posted.
     */
    protected $receiveNewComments;

    /**
     * If true, only the poll maker can see the poll's results
     * @var boolean
     */
    protected $use_ValueMax;

    /**
     * if true, there will be a limit of voters per option
     * @var boolean
     */
    protected $hidden;

    /**
     * If true, the author want to customize the URL
     * @var boolean
     */
    protected $use_customized_url;

    /**
     * If true, a password will be needed to access the poll
     * @var boolean
     */
    protected $use_password;

    /**
     * @var string
     */
    protected $password;

    /**
     * The password needed to access the poll, hashed. Only used if $use_password is set to true
     * @var string
     */
    protected $password_hash;

    /**
     * If true, the polls results will be also visible for those without password
     * @var boolean
     */
    protected $results_publicly_visible;

    /**
     * List of available choices
     *
     * @var array<Choice>
     */
    private $choices;

    /**
     * @var boolean
     */
    private $active;

    public function __construct()
    {
        $this->editable = Editable::EDITABLE_BY_ALL;
        $this->clearChoices();
    }

    public function clearChoices() {
        $this->choices = [];
    }

    public function addChoice(Choice $choice)
    {
        $this->choices[] = $choice;
    }

    /**
     * @return array<Choice>
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param $choices
     * @return Poll
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;
        return $this;
    }

    public function sortChoices()
    {
        usort($this->choices, [Choice::class, 'compare']);
    }

    /**
     * @return Editable
     */
    public function getEditable()
    {
        return $this->editable;
    }

    /**
     * @param int $value
     * @return Poll
     */
    public function setEditable($value) {
        $this->editable = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Poll
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Poll
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdminName()
    {
        return $this->admin_name;
    }

    /**
     * @param string $admin_name
     */
    public function setAdminName($admin_name)
    {
        $this->admin_name = $admin_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdminMail()
    {
        return $this->admin_mail;
    }

    /**
     * @param string $admin_mail
     */
    public function setAdminMail($admin_mail)
    {
        $this->admin_mail = $admin_mail;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return bool
     */
    public function isDate(): bool
    {
        return $this->format === 'D';
    }

    /**
     * @param string $format
     * @return Poll
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * @param \DateTime $end_date
     * @return Poll
     */
    public function setEndDate(\DateTime $end_date)
    {
        $this->end_date = $end_date;
        return $this;
    }

    /**
     * @return string
     */
    public function getChoixSondage()
    {
        return $this->choix_sondage;
    }

    /**
     * @param string $choix_sondage
     */
    public function setChoixSondage($choix_sondage)
    {
        $this->choix_sondage = $choix_sondage;
        return $this;
    }

    /**
     * @return int
     */
    public function getValueMax()
    {
        return $this->ValueMax ?: 0;
    }

    /**
     * @param int $ValueMax
     * @return Poll
     */
    public function setValueMax(?int $ValueMax)
    {
        $this->ValueMax = $ValueMax;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReceiveNewVotes()
    {
        return $this->receiveNewVotes;
    }

    /**
     * @param mixed $receiveNewVotes
     */
    public function setReceiveNewVotes($receiveNewVotes)
    {
        $this->receiveNewVotes = $receiveNewVotes;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReceiveNewComments()
    {
        return $this->receiveNewComments;
    }

    /**
     * @param mixed $receiveNewComments
     */
    public function setReceiveNewComments($receiveNewComments)
    {
        $this->receiveNewComments = $receiveNewComments;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUseValueMax()
    {
        return $this->use_ValueMax;
    }

    /**
     * @param bool $use_ValueMax
     */
    public function setUseValueMax($use_ValueMax)
    {
        $this->use_ValueMax = $use_ValueMax;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUseCustomizedUrl()
    {
        return $this->use_customized_url;
    }

    /**
     * @param bool $use_customized_url
     */
    public function setUseCustomizedUrl($use_customized_url)
    {
        $this->use_customized_url = $use_customized_url;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUsePassword()
    {
        return $this->use_password;
    }

    /**
     * @param bool $use_password
     */
    public function setUsePassword($use_password)
    {
        $this->use_password = $use_password;
        return $this;
    }

    /**
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->password_hash;
    }

    /**
     * @param string $password_hash
     */
    public function setPasswordHash($password_hash)
    {
        $this->password_hash = $password_hash;
        return $this;
    }

    /**
     * @return bool
     */
    public function isResultsPubliclyVisible()
    {
        return $this->results_publicly_visible;
    }

    /**
     * @param bool $results_publicly_visible
     */
    public function setResultsPubliclyVisible($results_publicly_visible)
    {
        $this->results_publicly_visible = $results_publicly_visible;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getAdminId()
    {
        return $this->admin_id;
    }

    /**
     * @param string $admin_id
     * @return Poll
     */
    public function setAdminId($admin_id)
    {
        $this->admin_id = $admin_id;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return Poll
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreationDate()
    {
        return $this->creation_date;
    }

    /**
     * @param string $creation_date
     * @return Poll
     */
    public function setCreationDate($creation_date)
    {
        $this->creation_date = $creation_date;
        return $this;
    }
}
