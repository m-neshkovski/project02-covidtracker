<?php
// default timezone
date_default_timezone_set('Europe/Berlin');
// custom execution time
ini_set('max_execution_time', 1800);
// inicijalni setiranja
require(dirname(__DIR__) . "/bootstrap.php");
$result_log = [];
// query za vnesuvawe na podatoci
$iso2_param = $confirmed = $newConfirmed = $deaths = $newDeaths = $recovered = $newRecovered = $active = $date = $date = "";
$sql_insert = 'INSERT INTO `total-dayone` (`country_iso2`, `Confirmed`, `New_confirmed`, `Deaths`, `New_deaths`, `Recovered`, `New_recovered`, `Active`, `Date`) VALUES (:iso2, :confirmed, :newConfirmed, :deaths, :newDeaths, :recovered, :newRecovered, :active, :dateFrom);';
$stmt_insert = $pdo->prepare($sql_insert);
// get all countries
$countries = getAllFromCountries($pdo);
// checkpoint for log for start
$started_at = date('Y-m-d H:i:s');
// check all countries
if(isset($_GET['message']) && !empty($_GET['message']) && $_GET['message'] == 'print') {
    echo "<h1>Update data for countryes</h1>";
}
foreach($countries as $country) {
    $iso2_param = $country['ISO2'];
    $slug_param = $country['Slug'];
    $country_param = $country['Country'];
    $stmt_insert -> bindParam(':iso2', $iso2_param);
    // give back info if sent from css, for crone function dont print
    if(isset($_GET['message']) && !empty($_GET['message']) && $_GET['message'] == 'print') {
        echo "<p>Updating data for: $country_param</p>";
    }
    // initialize counter for country records changes
    $added = 0;
    // initialize for calculating new cases
    $day_before_confirmed = 0;
    $day_before_deaths = 0;
    $day_before_recovered = 0;
    // slowing down for API
    usleep(250000);
    // get the last date from db that has information
    $lastDate = date('Y-m-d\TH:i:s\Z', getLastDateForCountry($pdo, $iso2_param));
    // curent date in right format
    $formatedNow = date('Y-m-d');
    $now = date('Y-m-d\TH:i:s\Z', strtotime($formatedNow));
    // prepare URL
    // https://api.covid19api.com/total/country/south-africa?from=2021-02-28T00:00:00Z&to=2021-03-01T00:00:00Z bez ovie na kraj nuli ne raboti t.e gi dava od day one
    $url = "https://api.covid19api.com/total/country/" . $slug_param . "?from=" . $lastDate . "&to=" . $now;
    // get all cases for country from API
    $totalForCountry = getFromAPI($url);
    if(isset($_GET['message']) && !empty($_GET['message']) && $_GET['message'] == 'print') {
        if(isset($totalForCountry) && !empty($totalForCountry)) {
            echo "<p>Data from API received for $country_param</p>";
        } else {
            echo "<p>Api did not respond for $country_param</p>";
        }
    }
    // if country has cases continue
    if($totalForCountry) {
        // got thru all data for country, and if ok insert it in DB
        foreach($totalForCountry as $totalCountry) {
            // proverka na podatoci za vnesuvawe
            $confirmed = issetNotEmptyZero($totalCountry->Confirmed);
            $newConfirmed = $totalCountry->Confirmed - $day_before_confirmed;
            $deaths = issetNotEmptyZero($totalCountry->Deaths);
            $newDeaths = $deaths - $day_before_deaths;
            $recovered = issetNotEmptyZero($totalCountry->Recovered);
            $newRecovered = $recovered - $day_before_recovered;
            $active = issetNotEmptyZero($totalCountry->Active);
            $date = issetNotEmpty($totalCountry->Date);
            $date = date('Y-m-d', strtotime($date));
            // bind params
            $stmt_insert -> bindParam(':confirmed', $confirmed);
            $stmt_insert -> bindParam(':newConfirmed', $newConfirmed);
            $stmt_insert -> bindParam(':deaths', $deaths);
            $stmt_insert -> bindParam(':newDeaths', $newDeaths);
            $stmt_insert -> bindParam(':recovered', $recovered);
            $stmt_insert -> bindParam(':newRecovered', $newRecovered);
            $stmt_insert -> bindParam(':active', $active);
            $stmt_insert -> bindParam(':dateFrom', $date);
            // ako za datumot vece ima podatoci da ne se vnesuvaat
            if(isset($_GET['message']) && !empty($_GET['message']) && $_GET['message'] == 'print') {
                if(isset($date) && !empty($date)) {
                    echo "<p>----> Try to add data for " . date('Y-m-d', strtotime($date)) . "</p>";
                } 
            }
            if(date('Y-m-d', strtotime($date)) > date('Y-m-d', strtotime($lastDate))) { 
                // ako e se ok vnesi red
                if($stmt_insert->execute()) {
                        if(isset($_GET['message']) && !empty($_GET['message']) && $_GET['message'] == 'print') {
                            echo "<p>----> Try to add data for " . date('Y-m-d', strtotime($date)) . "----> DONE.</p>";
                        } 
                    $added++;
                }
            } else {
                if(isset($_GET['message']) && !empty($_GET['message']) && $_GET['message'] == 'print') {
                    echo "<p>----> Try to add data for " . date('Y-m-d', strtotime($date)) . "----> DATA ALREADY PRESENT FOR DATE.</p>";
                } 
            }
            // novi vrednosti za prethodniot den
            $day_before_confirmed = $confirmed;
            $day_before_deaths = $deaths;
            $day_before_recovered = $recovered;
        }
    }
    if(isset($_GET['message']) && !empty($_GET['message']) && $_GET['message'] == 'print') {
        echo "<p>Done for $country_param, there are $added new records.</p>";
        echo "<hr>";
    }
    // gradime array so podatoci za log
    array_push($result_log, [
        'ISO2' => $iso2_param,
        'Slug' => $slug_param,
        'Country' => $country_param,
        'Changes_added' => $added,
    ]);
}
// kraj na sesijata
$ended_at = date('Y-m-d H:i:s');
// da se kreira log vo baza
totalDayoneLogInsert($pdo, $started_at, $ended_at, json_encode($result_log));
if(isset($_GET['message']) && !empty($_GET['message']) && $_GET['message'] == 'print') {
    echo "<h3>Done <a href='../../welcome.php'>Go back</a></h3>";
}