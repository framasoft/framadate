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

use Framadate\Utils;

include_once __DIR__ . '/app/inc/init.php';

if (!is_file(CONF_FILENAME)) {
    header(('Location: ' . Utils::get_server_name() . 'admin/check.php'));
    exit;
}

$app->get('/', 'poll.controller:indexAction')->bind('home');
$app->get('/{poll_id}', 'poll.controller:showAction')->bind('view_poll'); // TODO : add back ->assert('poll_id', POLL_REGEX)
$app->get('/{admin_poll_id}/admin', 'poll_admin.controller:showAdminPollAction')->bind('view_admin_poll'); // TODO : add back ->assert('admin_poll_id', POLL_ADMIN_REGEX)
$app->post('/{poll_id}', 'poll_comment_controller:postAction')->bind('post_comment');

/**
 * Creating a new poll
 */
$app->match('/new/{type}', 'poll.controller:createPollAction')->bind('new_poll');

/**
 * Creating a date poll
 */
$app->match('/new/date/2', 'date_poll.controller:createPollActionStepTwo')->bind('new_date_poll_step_2');
$app->match('/new/date/3', 'date_poll.controller:createPollActionStepThree')->bind('new_date_poll_step_3');
$app->post('/new/date/4', 'date_poll.controller:createPollFinalAction')->bind('new_date_poll_final');

/**
 * Creating a classic poll
 */
$app->match('/new/classic/2', 'classic_poll.controller:createPollActionStepTwo')->bind('new_classic_poll_step_2');
$app->match('/new/classic/3', 'classic_poll.controller:createPollActionStepThree')->bind('new_classic_poll_step_3');
$app->match('/new/classic/4', 'classic_poll.controller:createPollActionStepThree')->bind('new_classic_poll_final');

/**
 * Posting a vote
 */
$app->post('/{poll_id}/vote', 'vote.controller:voteAction')->bind('vote_poll');
$app->match('/{poll_id}/vote/edit/{vote_uniq_id}', 'vote.controller:editVoteAction')->bind('edit_vote_poll');
$app->post('/{admin_poll_id}/admin/vote', 'vote.controller:voteAdminAction')->bind('vote_poll_admin');

/**
 * Editing a vote
 */
$app->post('{admin_poll_id}/admin/edit', 'poll_admin.controller:editPollAction')->bind('edit_admin_poll');

/**
 * Export a poll
 */
$app->get('{poll_id}/export.{format}', 'poll.controller:exportPollAction')->bind('export_poll')->value('format', 'CSV');

/**
 * Post a comment
 */
$app->post('{poll_id}/comment', 'comment.controller:createCommentAction')->bind('new_comment');
$app->post('{poll_id}/comment/remove', 'comment.controller:removeCommentAction')->bind('remove_comment');

$app->run();

