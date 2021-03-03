<?php
// query za zemanje na podatoci za drzavi
function getAllFromCountries($pdo_input) {
    $sql = "SELECT * FROM countries";
    $stmt = $pdo_input->prepare($sql);
    if($stmt->execute()) {
        return $stmt->fetchAll();
    }
}
// query za zemanje na podatoci za drzavi za koi ima podatoci za selectot
function getActiveCountries($pdo_input) {
    $sql = "SELECT c.* FROM `total-dayone` as t LEFT JOIN `countries` as c ON t.country_iso2 = c.ISO2 WHERE 1 GROUP BY t.country_iso2 ORDER BY c.Slug";
    $stmt = $pdo_input->prepare($sql);
    if($stmt -> execute()) {
        return $stmt->fetchAll();
    }
    return false;
}
// function get from API
function getFromAPI($url_sent) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url_sent,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    if (NULL !== json_decode($response) && !empty(json_decode($response))) {
        return json_decode($response);
    }
    return false;
}
// date to compare
function getLastDateForCountry($pdo_selected, String $iso2) {
    $sql_date = 'SELECT `Date` FROM `total-dayone` WHERE country_iso2 = :iso2 ORDER BY `Date` DESC LIMIT 1';
    $stmt_date = $pdo_selected->prepare($sql_date);
    $stmt_date -> bindParam(':iso2', $iso2);
    if($stmt_date->execute()) {
        $date = $stmt_date->fetch();
        if(isset($date) && !empty($date)) {
            return strtotime($date['Date']);
        } else {
            return strtotime("1970-01-01T00:00:00Z");
        }
    }
}
// ova gi dava total vrednostite za period ili ako e false za total od dayone
function worldTotalForPeriod($pdo_input, $last_date_input, $period = false) {
    $from_date = date('Y-m-d', strtotime("$last_date_input - $period days"));
    $day_before_last_date = date('Y-m-d', strtotime("$last_date_input - 1 days"));
    $sql = "SELECT * FROM (SELECT SUM(a.NewConfirmed) as `Confirmed`, SUM(a.NewDeaths) as `Deaths`, SUM(a.NewRecovered) as `Recovered` FROM (SELECT country_iso2, SUM(New_confirmed) as NewConfirmed, SUM(New_deaths) as NewDeaths, SUM(New_recovered) as NewRecovered FROM `total-dayone` WHERE Date > :from_date GROUP BY `country_iso2`) as a WHERE 1) AS b 
    UNION
    (SELECT SUM(a.NewConfirmed) as `NewConfirmed`, SUM(a.NewDeaths) as `NewDeaths`, SUM(a.NewRecovered) as `NewRecovered` FROM (SELECT country_iso2, SUM(New_confirmed) as NewConfirmed, SUM(New_deaths) as NewDeaths, SUM(New_recovered) as NewRecovered FROM `total-dayone` WHERE Date > :day_before_last_date GROUP BY `country_iso2`) as a WHERE 1)";
    $stmt = $pdo_input->prepare($sql);
    $stmt -> bindParam(':from_date', $from_date);
    $stmt -> bindParam(':day_before_last_date', $day_before_last_date);
    if($stmt -> execute()) {
        $response = $stmt->fetchAll();
        if (count($response) == 2) {
            return [
                'TotalConfirmed' => $response[0]['Confirmed'],
                'TotalDeaths' => $response[0]['Deaths'],
                'TotalRecovered' => $response[0]['Recovered'],
                'NewConfirmed' => $response[1]['Confirmed'],
                'NewDeaths' => $response[1]['Deaths'],
                'NewRecovered' => $response[1]['Recovered'],
            ];
        } else {
            return [
                'TotalConfirmed' => $response[0]['Confirmed'],
                'TotalDeaths' => $response[0]['Deaths'],
                'TotalRecovered' => $response[0]['Recovered'],
                'NewConfirmed' => $response[0]['Confirmed'],
                'NewDeaths' => $response[0]['Deaths'],
                'NewRecovered' => $response[0]['Recovered'],
            ];
        }
    }
    return false;
}
// ova gi dava site drzavi sumirani vo period
function allCountriesTotalForPeriod($pdo_input, $last_date_input, $period = false) {
    $from_date = date('Y-m-d', strtotime("$last_date_input - $period days"));
    $day_before_last_date = date('Y-m-d', strtotime("$last_date_input - 1 days"));
    $sql = "SELECT b.*, d.NewConfirmed, d.NewDeaths, d.NewRecovered FROM (SELECT a.Country, a.CountryCode, a.Slug, SUM(a.Confirmed) as TotalConfirmed, SUM(a.Deaths) as TotalDeaths, SUM(a.Recovered) as TotalRecovered FROM (SELECT c.Country as Country, t.country_iso2 as CountryCode, c.Slug as Slug, t.New_confirmed as Confirmed, t.New_deaths as Deaths, t.New_recovered as Recovered FROM `total-dayone` as t INNER JOIN countries as c ON t.country_iso2 = c.ISO2 WHERE Date > :from_date) as a GROUP BY a.CountryCode) as b
    INNER JOIN (SELECT a.Country, a.CountryCode, a.Slug, SUM(a.Confirmed) as NewConfirmed, SUM(a.Deaths) as NewDeaths, SUM(a.Recovered) as NewRecovered FROM (SELECT c.Country as Country, t.country_iso2 as CountryCode, c.Slug as Slug, t.New_confirmed as Confirmed, t.New_deaths as Deaths, t.New_recovered as Recovered FROM `total-dayone` as t INNER JOIN countries as c ON t.country_iso2 = c.ISO2 WHERE Date > :day_before_last_date) as a GROUP BY a.CountryCode) as d
    ON b.CountryCode = d.CountryCode ORDER by b.TotalConfirmed DESC";
    $stmt = $pdo_input->prepare($sql);
    $stmt -> bindParam(':from_date', $from_date);
    $stmt -> bindParam(':day_before_last_date', $day_before_last_date);
    if($stmt -> execute()) {
        return $stmt->fetchAll();
    }
    return false;
}
// ova gi dava podatocite za vkupno vo period za odredena drzava
function countryTotalForPeriod($pdo_input, $last_date_input, $country_iso2, $period = false) {
    $from_date = date('Y-m-d', strtotime("$last_date_input - $period days"));
    $day_before_last_date = date('Y-m-d', strtotime("$last_date_input - 1 days"));
    $sql = "SELECT a.TotalConfirmed, a.TotalDeaths, a.TotalRecovered, b.NewConfirmed, b.NewDeaths, b.NewRecovered FROM
    (SELECT country_iso2, SUM(New_confirmed) AS TotalConfirmed, SUM(New_deaths) AS TotalDeaths, SUM(New_recovered) AS TotalRecovered FROM `total-dayone` WHERE `Date` > :from_date GROUP BY country_iso2) AS a
    INNER JOIN
    (SELECT country_iso2, New_confirmed AS NewConfirmed, New_deaths AS NewDeaths, New_recovered AS NewRecovered FROM `total-dayone` WHERE `Date` > :day_before_last_date GROUP BY country_iso2) AS b
    ON a.country_iso2 = b.country_iso2
    WHERE a.country_iso2 = :country_iso2";
    $stmt = $pdo_input->prepare($sql);
    $stmt -> bindParam(':from_date', $from_date);
    $stmt -> bindParam(':day_before_last_date', $day_before_last_date);
    $stmt -> bindParam(':country_iso2', $country_iso2);
    if($stmt -> execute()) {
        return $stmt->fetch();
    }
    return false;
}
// oga dava podatoci za period za drzava po datumi
function countryDataForPeriod($pdo_input, $last_date_input, $country_iso2, $period = false) {
    $from_date = date('Y-m-d', strtotime("$last_date_input - $period days"));
    $sql="SELECT t.Date as Country, t.country_iso2 as CountryCode, c.Slug as Slug, t.Confirmed as TotalConfirmed, t.New_confirmed as NewConfirmed, t.Deaths as TotalDeaths, t.New_deaths as NewDeaths, t.Recovered as TotalRecovered, t.New_recovered as NewRecovered FROM `total-dayone` AS t INNER JOIN countries as c ON t.country_iso2 = c.ISO2 WHERE t.Date > :from_date AND t.country_iso2 = :country_iso2 ORDER BY t.Date DESC";
    $stmt = $pdo_input->prepare($sql);
    $stmt -> bindParam(':from_date', $from_date);
    $stmt -> bindParam(':country_iso2', $country_iso2);
    if($stmt -> execute()) {
        return $stmt->fetchAll();
    }
    return false;
}
// drzava od praten world ili iso2
function getCountryName($active_countries_array, $country_iso2 = 'world') {
    if ($country_iso2 == 'world') {
        return 'World';
    } else {
        foreach(array_filter($active_countries_array, function($v, $k) {
            return $v['ISO2'] == $_GET['country'];
        }, ARRAY_FILTER_USE_BOTH) as $Val) {
            return $Val['Country'];
        }
    }
}
// get last date in table
function getLastDateInTable($pdo_selected, String $table) {
    $sql = "SELECT `Date` FROM `$table` WHERE 1 ORDER BY `Date` DESC LIMIT 1";
    $stmt = $pdo_selected->prepare($sql);
    if($stmt->execute()) {
        $date = $stmt->fetch();
        if(isset($date) && !empty($date)) {
            return strtotime($date['Date']);
        } else {
            return strtotime("1970-01-01T00:00:00Z");
        }
    }
}
// total dayone log insert
function totalDayoneLogInsert($db_pdo, $started, $ended, $changes) {
    $sql = 'INSERT INTO `total-dayone-log` (`started_at`, `ended_at`, `change_log`) VALUES (:started_at, :ended_at, :change_log);';
    $stmt = $db_pdo->prepare($sql);
    $stmt -> bindParam(':started_at', $started);
    $stmt -> bindParam(':ended_at', $ended);
    $stmt -> bindParam(':change_log', $changes);
    if($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
// total dayone last log get
function lastLog($db_pdo) {
    $sql = "SELECT * FROM `total-dayone-log` WHERE 1 ORDER BY `created_at` DESC limit 1";
    $stmt = $db_pdo->prepare($sql);
    if($stmt->execute()) {
        return $stmt->fetch();
        exit;
    }
    return false;
} 
// to record null
function issetNotEmpty($variable) {
    if (isset($variable) && !empty($variable)) {
        return $variable;
    }
    return NULL;
}
// to record zero
function issetNotEmptyZero($variable) {
    if (isset($variable) && !empty($variable)) {
        return $variable;
    }
    return 0;
}