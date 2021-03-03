<?php
    // controller za Welcome
    require(dirname(__FILE__) . "/php/controllers/WelcomeController.php");
?>

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

    <link rel="stylesheet" href="./css/style.css">
    <title>Welcome</title>

</head>

<body class="bg-light">

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 p-0 m-0">
                <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                    <a class="navbar-brand" href="#">Hi, <span class="text-capitalize"><?php echo htmlspecialchars($_SESSION["username"]); ?></span></a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <a class="nav-link px-3" href="./php/db_update/update-countries.php" id="update-total-countries">
                                    Update Countries
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3" href="./php/db_update/update-total-countries.php?message=print" id="update-total-countries">
                                    Update Total Dayone
                                </a>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle px-3" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Tools
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="./register.php">Register new Your Account</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="./reset-password.php">Reset Your Password</a>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a href="./php/tools/logout.php" class="btn btn-danger">Sign Out</a>
                            </li>

                        </ul>
                    </div>
                </nav>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12 p-0 mb-3">
                <div class="page-header text-center">
                    <h1>Преземање на податоци од API</h1>
                    <hr>
                </div>
            </div>
            <div class="col-12 p-0 mb-3">
                <?php $log_data = lastLog($pdo); ?>
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-6 text-center">
                                    <h3>
                                        Started at: <?php echo $log_data['started_at'] ?>
                                    </h3>
                                </div>
                                <div class="col-6 text-center">
                                    <h3>
                                        Ended at: <?php echo $log_data['ended_at'] ?>
                                    </h3>
                                </div>
                            </div>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <table class="table table-striped text-center">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">ISO2</th>
                                        <th scope="col">Country</th>
                                        <th scope="col">Changes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                    $no = 1;
                                    foreach(json_decode($log_data['change_log']) as $log) {
                                ?>
                                    <tr>
                                        <th scope="row"><?php echo $no ?></th>
                                        <td><?php echo $log -> ISO2 ?></td>
                                        <td><?php echo $log -> Country ?></td>
                                        <td><?php echo $log -> Changes_added ?></td>
                                    </tr>
                                <?php
                                    $no++;
                                    }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            

        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>

</body>
<script src="https://kit.fontawesome.com/280db70b77.js"></script>

</html>