<?php
$file = "d:/Dev/applications/pindang-oi/resources/views/pages/main/pegawai/rencana-kerja/rencana-kerja-dl.blade.php";
$content = file_get_contents($file);

$content = str_replace(
    "<th`n                                                    class=`"px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700`">`n                                                    Status DL`n                                                </th>",
    "<th`n                                                    class=`"px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700`">`n                                                    Status DL / Translok`n                                                </th>",
    $content
);

file_put_contents($file, $content);
