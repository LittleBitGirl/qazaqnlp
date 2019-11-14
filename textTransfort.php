<?php
$words = file('all_words.txt', FILE_IGNORE_NEW_LINES);
foreach ($words as $key => $value) {
    $words[$key] = substr($words[$key], 0, strpos($words[$key], ' '));
    echo $words[$key];
    // if(strpos($words[$key], '(') !== false){
    // 	$words[$key] = substr($words[$key], 0, strpos($words[$key], ' ('));
    // }
}
file_put_contents('words.txt', $words);
?>
<!-- <?php

?> -->