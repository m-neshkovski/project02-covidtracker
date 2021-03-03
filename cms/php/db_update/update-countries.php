<?php

require(dirname(__DIR__) . "/bootstrap.php");
// url for countries
$url = "https://api.covid19api.com/countries";
$response = getFromAPI($url);
// required query statements
$sql_insert = "INSERT INTO `countries` (`ISO2`, `Slug`, `Country`) VALUES (:iso2, :slug, :country);";
$sql_select = "SELECT * FROM `countries` WHERE `ISO2` = :iso2";
$sql_update = "UPDATE `countries` SET `Slug` = :slug, `Country` = :country WHERE `countries`.`ISO2` = :iso2;";

$stmt_insert = $pdo->prepare($sql_insert);
$stmt_select = $pdo->prepare($sql_select);
$stmt_update = $pdo->prepare($sql_update);

$changes = 0;

if($stmt_insert && $stmt_select && $stmt_update) {
    
    foreach($response as $object) {

        $iso2 = $object -> ISO2;
        $slug = $object -> Slug;
        $country = $object -> Country;

        $stmt_select -> bindParam(':iso2', $iso2);

        if($stmt_select->execute()) {

            $resault = $stmt_select->fetchAll();

            if(count($resault) == 1) {
                if($resault[0]['Slug'] != $slug || $resault[0]['Country'] != $country) {
                    $stmt_update -> bindParam(':slug', $slug);
                    $stmt_update -> bindParam(':country', $country);
                    $stmt_update -> bindParam(':iso2', $iso2);

                    if ($stmt_update->execute()) {
                        $changes++;
                    }
                }

            } else if (count($resault) == 0) {
                $stmt_insert -> bindParam(':iso2', $iso2);
                $stmt_insert -> bindParam(':slug', $slug);
                $stmt_insert -> bindParam(':country', $country);

                if($stmt_insert->execute()) {
                    $changes++;
                }
            }
        }
    }
}