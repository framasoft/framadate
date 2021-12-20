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
namespace Framadate\Services;

use DateTime;
use Framadate\Utils;
use Sabre\VObject;

class ICalService {
    /**
     * Creates an ical-File and initiates the download. If possible, the provided time is used, else an all day event is created.
     */
    public function getEvent($poll, string $start_day, string $start_time): void
    {
        if(!$this->dayIsReadable($start_day)) {
            return;
        }

        $ical_text = "";
        $elements = explode("-", $start_time);
        $end_time = null;
        if(count($elements) === 2) {
            $start_time = trim($elements[0]);
            $end_time = trim($elements[1]);
        }
        $start_time = $this->reviseTimeString($start_time);
        if($end_time !== null) {
            $end_time = $this->reviseTimeString($end_time);
        }
        if($start_time !== null) {
            if($end_time !== null) {
                $ical_text = $this->getTimedEvent($poll, $start_day . " " . $start_time, $start_day . " " . $end_time);
            } else {
                $ical_text = $this->getTimedEvent1Hour($poll, $start_day . " " . $start_time);
            }
        }
        else {
            $date = DateTime::createFromFormat('d-m-Y', $start_day);
            $day = $date->format('Ymd');
            $ical_text = $this->getAllDayEvent($poll, $day);
        }
        $this->provideFile($poll->title, $ical_text);
    }

    /**
     * Calls getTimedEvent with one hour as a time slot, starting at $start_daytime
     */
    public function getTimedEvent1Hour($poll, string $start_daytime): string
    {
        $end_daytime = date(DATE_ATOM, strtotime('+1 hours', strtotime($start_daytime)));
        return $this->getTimedEvent($poll, $start_daytime, $end_daytime);
    }

    /**
     * Generates the text for an ical event including the time
     */
    public function getTimedEvent($poll, string $start_daytime, string $end_daytime): string
    {
        $vcalendar = new VObject\Component\VCalendar([
            'VEVENT' => [
                'SUMMARY' => $poll->title,
                'DESCRIPTION' => $this->stripMD($poll->description),
                'DTSTART' => new DateTime($start_daytime),
                'DTEND'   => new DateTime($end_daytime)
            ],
            'PRODID' => ICAL_PRODID
        ]);
        return $vcalendar->serialize();
    }

    /**
     * Generates the text for an ical event if the time is not known
     */
    public function getAllDayEvent($poll, string $day): string
    {
        $vcalendar = new VObject\Component\VCalendar();
        $vevent = $vcalendar->add('VEVENT');
        $vevent->add('SUMMARY', $poll->title);
        $vevent->add('DESCRIPTION', $this->stripMD($poll->description));
        $dtstart = $vevent->add('DTSTART', $day);
        $dtstart['VALUE'] = 'DATE';
        unset($vcalendar->PRODID);
        $vcalendar->add('PRODID', ICAL_PRODID);
        return $vcalendar->serialize();
    }

    /**
     * Creates a file and initiates the download
     * @param string $title
     * @param string $ical_text
     */
    public function provideFile(string $title, string $ical_text): void
    {
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . $this->stripTitle($title) . ICAL_ENDING);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header("Content-Type: text/calendar");
        echo $ical_text;
        exit;
    }

    /**
     * Reformats a string value into a time readable by DateTime
     * @param string $time
     * @return string the corrected value, null if the format is unknown
     */
    public function reviseTimeString(string $time): ?string
    {
        // 24-hour clock / international format
        if (preg_match('/^\d\d(:)\d\d$/', $time)) {
            return $time;
        }
        // 12-hour clock / using am and pm

        if (preg_match('/^\d[0-2]?:?\d{0,2}\s?[aApP][mM]$/', $time)) {
            return $this->formatTime($time);
        }
        // french format HHhMM or HHh

        if (preg_match('/^\d\d?[hH]\d?\d?$/', $time)) {
            return $this->formatTime(str_pad(str_ireplace("H", ":", $time),  5, "0"));
        }
        // Number only

        if (preg_match('/^\d{1,4}$/', $time)) {
            return $this->formatTime(str_pad(str_pad($time,  2, "0", STR_PAD_LEFT),  4, "0"));
        }
        return null;
    }

    /**
     * @param string $day
     * @return false|int 1 if the day string can be parsed, 0 if not and false if an error occured
     */
    public function dayIsReadable(string $day) {
        return preg_match('/^\d{2}-\d{2}-\d{4}$/', $day);
    }

    /**
     * @param string $time
     * @return string date string in format H:i (e.g. 19:00)
     */
    public function formatTime(string $time): string
    {
        return date("H:i", strtotime($time));
    }

    /**
     * Converts MD Code to HTML, then strips HTML away
     */
    public function stripMD(string $string): string
    {
        return strip_tags(Utils::markdown($string));
    }

    /**
     * Strips a string so it's usable as a file name (only digits, letters and underline allowed)
     *
     * @return null|string
     */
    public function stripTitle(string $string): ?string {
        return preg_replace('/[^a-z0-9_]+/', '-', strtolower($string));
    }
}
