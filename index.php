<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous" />

    <!-- Custom Fonts needed -->
    <!-- Ubuntu, san-serif-->
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet" />
    <!-- Vira Code, monospace -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <!-- Stick font -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Stick&display=swap" rel="stylesheet">
    <!-- Rubik font -->
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Od Apexcharts -->
    <script>
        window.Promise ||
            document.write(
                '<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.min.js"><\/script>'
            )
        window.Promise ||
            document.write(
                '<script src="https://cdn.jsdelivr.net/npm/eligrey-classlist-js-polyfill@1.2.20171210/classList.min.js"><\/script>'
            )
        window.Promise ||
            document.write(
                '<script src="https://cdn.jsdelivr.net/npm/findindex_polyfill_mdn"><\/script>'
            )
    </script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <link rel="stylesheet" href="./css/index.css">


    <script>
        var _seed = 42;
        Math.random = function() {
            _seed = _seed * 16807 % 2147483647;
            return (_seed - 1) / 2147483646;
        };
    </script>

    <title>Covid 19 Tracker</title>
</head>

<body class="bg-light font-family-source-code body-bg-image">

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="container">
                    <nav class="navbar navbar-expand-lg navbar-light bg-transparent">
                        <a class="navbar-brand font-family-stick h1" href="./">
                            <img src="./img/logo300px.png" width="30" height="30" class="d-inline-block align-top" alt="" loading="lazy">
                            Covid 19 Tracker</a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavMain" aria-controls="navbarNavMain" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNavMain">
                            <ul class="navbar-nav ml-auto text-center">
                                <li class="nav-item">
                                    <a class="nav-link" href="./cms/">Log in</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                    <div id="card-wrapper" class="row py-3">
                        <h1 class="col-sm-12 display-4 text-center">
                            <span id="data-for-span-country">World</span>
                        </h1>
                        <p class="col-sm-12 text-center">
                            Period: <span id="data-for-span-period">All time</span>
                        </p>
                        <div class="col-sm-12 col-md-4 text-center pb-3">
                            <div class="card box-shadow-custom">
                                <div class="card-body">
                                    <h4>Confirmed</h4>
                                    <h3 id="total-confirmed" class="card-title text-warning h2"></h3>
                                    <h6 class="card-subtitle mb-2">Last day: <span id="new-confirmed"></span></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 text-center pb-3">
                            <div class="card box-shadow-custom">
                                <div class="card-body">
                                    <h4>Deaths</h4>
                                    <h3 id="total-deaths" class="card-title text-danger h2 "></h3>
                                    <h6 class="card-subtitle mb-2">Last day: <span id="new-deaths"></span></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 text-center pb-3">
                            <div class="card box-shadow-custom">
                                <div class="card-body">
                                    <h4>Recovered</h4>
                                    <h3 id="total-recovered" class="card-title text-success h2"></h3>
                                    <h6 class="card-subtitle mb-2">Last day: <span id="new-recovered"></span></h6>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="row pb-2">
            <div class="col-sm-12 text-center">
                <div class="container">
                    <form id="filter-form" class="form-row form-change">
                        <div class="col-xs-12 col-sm-3 col-md-2">
                            <input class="tgl tgl-flip" type="checkbox" id="charts-check" name="charts-check" value="true">
                            <label class="tgl-btn" data-tg-off="Show Charts" data-tg-on="Show Table" for="charts-check">Charts</label>
                        </div>
                        <div class="col-xs-12 col-sm-9 col-md-10 col-lg-4 pb-3">
                            <select name="period" id="period-select" class="custom-select">
                                <option value="false" default selected>Period: All time</option>
                                <option value=7>Period: 7 days</option>
                                <option value=30>Period: 30 days</option>
                                <option value=90>Period: 90 days</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-12 col-md-12 col-lg-6">
                            <select name="countries" id="countries-select" class="custom-select">
                                <option value="world" default selected>World</option>
                                <option value="" disabled>----------</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-12 text-muted text-center">
                <small id="last-updated" class="d-block">Last updated: <span></span></small>
            </div>
        </div>

        <div id="charts-row" class="row">
            <div class="col-12">
                <nav class="navbar navbar-expand-lg navbar-light bg-transparent">
                    <div class="text-wrap">
                        <a class="navbar-brand text-wrap" href="#"><span id="chart-name"></span><span id="chart-data-selected">/Confirmed/Cumulative</span></a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavFilter" aria-controls="navbarNavFilter" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavFilter">
                        <ul class="navbar-nav ml-auto text-center">
                            <li class="nav-item dropdown active">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Confirmed
                                </a>
                                <div class="dropdown-menu text-center" aria-labelledby="navbarDropdown">
                                    <a id="total-confirmed-btn" class="dropdown-item btn btn-transparent filter-btns active" data-filter="TotalConfirmed" data-title="/Confirmed/Cumulative">Cumulative</a>
                                    <a id="new-confirmed-btn" class="dropdown-item btn btn-transparent filter-btns" data-filter="NewConfirmed" data-title="/Confirmed/By Date">By Date</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Deaths
                                </a>
                                <div class="dropdown-menu text-center" aria-labelledby="navbarDropdown">
                                    <a id="total-deaths-btn" class="dropdown-item btn btn-transparent filter-btns" data-filter="TotalDeaths" data-title="/Deaths/Cumulative">Cumulative</a>
                                    <a id="new-deaths-btn" class="dropdown-item btn btn-transparent filter-btns" data-filter="NewDeaths" data-title="/Deaths/By Date">By Date</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Recovered
                                </a>
                                <div class="dropdown-menu text-center" aria-labelledby="navbarDropdown">
                                    <a id="total-recovered-btn" class="dropdown-item btn btn-transparent filter-btns" data-filter="TotalRecovered" data-title="/Recovered/Cumulative">Cumulative</a>
                                    <a id="new-recovered-btn" class="dropdown-item btn btn-transparent filter-btns" data-filter="NewRecovered" data-title="/Recovered/By Date">By Date</a>
                                </div>
                            </li>
                            <li class="nav-item">
                                <div>
                                    <a id="active-btn" class="nav-link btn btn-transparent filter-btns disabled" data-filter="Active" data-title="/Active">Active</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
            <div id="chart-world-wrapper" class="col-12 graph-color-custom">
                <div id="chart-world"></div>
            </div>
            <div id="chart-country-wrapper" class="col-12 graph-color-custom">
                <div id="chart-country"></div>
            </div>
        </div>

        <div id="table-row" class="row">
            <div class="col-12 table-overflow">
                <table id="all-countries" class="table table-striped table-text-color-custom">
                    <!-- all tables go here -->
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12 py-3 text-center text-muted">
                <p>Project 02 by Milosh Neshkovski &copy; for Brainster Fullstack Academy</p>
            </div>
        </div>
    </div>
    </div>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="./js/jq/jquery-3.5.1.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

    <script src="./js/index.js"></script>
    <script>




    </script>
</body>
<script src="https://kit.fontawesome.com/280db70b77.js"></script>

</html>