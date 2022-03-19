<?php

function CleanupFiles($files, $files_to_keep = 30) {
    $filesList = SortFilesByDate($files);

    $files = [];
    for ($i = 0; count($filesList) > $files_to_keep; $i++) {
        array_push($files, $filesList[$i]);
        unlink($filesList[$i]);
        unset($filesList[$i]);
    }

    return $files;
}


function SortFilesByDate($files)
{
// Älteste an erster Stelle
    usort($files, function($x, $y) {
        return filemtime($y) < filemtime($x);
    });

    return $files;
}
