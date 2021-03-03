<?php
// setup
require(dirname(__DIR__) . "/bootstrap.php");
if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $response = [
        'Period' => $_GET['period'],
        'Country' => $_GET['country'],
    ];
    // gi zemame samo aktivnite drzavi
    $response['Active_countries'] = getActiveCountries($pdo);
    // funkcijata vraca world ili country name
    $response['Country'] = getCountryName($response['Active_countries'], $_GET['country']);
    // datumi
    $last_date = date('Y-m-d', getLastDateInTable($pdo, 'total-dayone'));
    $from_date = date('Y-m-d', strtotime("$last_date - " . $_GET['period'] ." days"));
    $response['Date'] = $last_date;
    $response['From_date'] = $from_date;
    // global i countries prilagodeni za front
    if($_GET['country'] == "world") {
        // karticke i prva redica
        $response['Global'] = worldTotalForPeriod($pdo, $last_date, $_GET['period']);
        // for all countries
        $response['Countries'] = allCountriesTotalForPeriod($pdo, $last_date, $_GET['period']);
    } else {
        // karticke i prva redica
        $response['Global'] = countryTotalForPeriod($pdo, $last_date, $_GET['country'], $_GET['period']);
        // for all countries
        $response['Countries'] = countryDataForPeriod($pdo, $last_date, $_GET['country'], $_GET['period']);
    }
    echo json_encode($response);
}
?>