<?php

$purpleColorStart = "\e[35m";
$yellowColorStart = "\e[33m";
$greenColorStart = "\e[32m";
$colorEnd = "\033[0m ";

$currentDirectoryPath = scandir(dirname(__FILE__));
$allFileNames = array_diff($currentDirectoryPath, array('.', '..'));
$sqlFileNames = array_filter($allFileNames, function($fileName) {
    return stristr($fileName, '.sql') !== false;
});

if(empty($sqlFileNames)) { 
    throw new \Error('SQL files where not found in directory ' . dirname(__FILE__)); 
}

printf("$yellowColorStart" . count($sqlFileNames) . "$colorEnd files were received" . " \n");

$fileNames = array_values($sqlFileNames);
$resultList = [];

for($i = 0; $i < count($fileNames); $i++) {
    printf("File processing: " . $fileNames[$i] . " | ");
    $notSave = false;
    $file = fopen($fileNames[$i], 'r');
    
    while (($line = fgets($file)) !== false) {
        if (preg_match('/references (\w+)/', $line, $matches)) {
            $referenceName = $matches[1];
            printf("Found references: $purpleColorStart" . $referenceName . "$colorEnd | ");
            if(in_array($referenceName . ".sql", $resultList)) {
                continue;
            } else {
                $fileNames[] = $fileNames[$i];
                $notSave = true;
                printf("$yellowColorStart transfered $colorEnd \n");
                break;
            }
        }
    }

    if($notSave) {
        continue;
    } else {
        printf("$greenColorStart added $colorEnd \n");
        $resultList[] = $fileNames[$i];
    }
}

$resultList = array_map(function(string $fileName) {
    return "\ir " . $fileName . " \n";
}, $resultList);

$resultString = implode(" ", $resultList);

file_put_contents('index.sql', $resultString);
printf("\n $greenColorStart DONE $colorEnd  \n Generated file - $purpleColorStart" . dirname(__FILE__) . "/index.sql $colorEnd  \n");