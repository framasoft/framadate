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
namespace Framadate\Repositories;

use Framadate\FramaDB;

class RepositoryFactory {

    private static $connect;

    private static $pollRepository;
    private static $slotRepository;
    private static $voteRepository;
    private static $commentRepository;

    /**
     * @param FramaDB $connect
     */
    static function init(FramaDB $connect) {
        self::$connect = $connect;
    }

    /**
     * @return PollRepository The singleton of PollRepository
     */
    static function pollRepository() {
        if (self::$pollRepository == null) {
            self::$pollRepository = new PollRepository(self::$connect);
        }

        return self::$pollRepository;
    }

    /**
     * @return SlotRepository The singleton of SlotRepository
     */
    static function slotRepository() {
        if (self::$slotRepository == null) {
            self::$slotRepository = new SlotRepository(self::$connect);
        }

        return self::$slotRepository;
    }

    /**
     * @return VoteRepository The singleton of VoteRepository
     */
    static function voteRepository() {
        if (self::$voteRepository == null) {
            self::$voteRepository = new VoteRepository(self::$connect);
        }

        return self::$voteRepository;
    }

    /**
     * @return CommentRepository The singleton of CommentRepository
     */
    static function commentRepository() {
        if (self::$commentRepository == null) {
            self::$commentRepository = new CommentRepository(self::$connect);
        }

        return self::$commentRepository;
    }

}
