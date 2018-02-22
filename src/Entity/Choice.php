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

use App\Entity\ChoiceInterface;

class Choice
{
    const CHOICE_REGEX = '/^[ 012]$/';
    const MD5_REGEX = '/^[A-Fa-f0-9]{32}$/';

    /**
     * @var string
     */
    protected $id;

    /**
     * Name of the Choice
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    protected $poll_id;

    /**
     * Choice constructor.
     * @param string $name
     */
    public function __construct(string $name = '')
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Choice
     */
    public function setName(string $name): Choice
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Choice
     */
    public function setId(string $id): Choice
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getPollId(): ?string
    {
        return $this->poll_id;
    }

    /**
     * @param string $poll_id
     * @return Choice
     */
    public function setPollId(string $poll_id): Choice
    {
        $this->poll_id = $poll_id;
        return $this;
    }

    /**
     * @param Choice $a
     * @param Choice $b
     * @return bool
     */
    public static function compare(Choice $a, Choice $b)
    {
        return $a->getName() < $b->getName();
    }
}
