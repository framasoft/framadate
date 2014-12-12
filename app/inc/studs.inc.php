<?php

function countStuds($subjects)
{
    $nb = 0;
    foreach($subjects as $subject) {
        $nb += substr_count($subject->sujet, ',')+1;
    }
    return $nb;
}