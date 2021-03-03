<?php


// ! POTREBNA vraca true ako e POST, false ako ne e post
function if_POST() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        return true;
    }
    return false;
}
// ! POTREBNA proveruva dali e vnesena vrednost za pole vo zavisnost pd POST i GET metod i vraca true ako ima vrednost i false ako nema
function field_isset_not_empty($fieldName, $method) {
    if ($method == $_SERVER['REQUEST_METHOD']) {
        if (isset($_POST[$fieldName]) && !empty($_POST[$fieldName])) {
            return true;
        }
    } 
    return false;
}

// ! POTREBNA Validacija na email so minimum 5 karakteri pred @

function validateEmail($email) {
    $temp = explode('@', $email);
    if (strlen($temp[0]) >= 5) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
    } 
    return false;
}

// ! POTREBNA validacija na full_name za bukvi i barem edno prazno mesto

function validateFullname($fullname) {
    if (!preg_match( '/\d/', $fullname) && preg_match( '/[\\s]/', $fullname )) {
        return preg_match( '/^[a-zA-Z\p{L}\s]+$/u', $fullname);
    }
}

// ! POTREBNA validacija na company_name za bukvi, brojki, "-" i prazno mesto

function validateCompanyname($companyname) {
    return preg_match( '/^[-a-zA-Z\p{L}\d\s]+$/u', $companyname );
}

// ! POTREBNA validacija na company_name za bukvi, brojki, "-" i prazno mesto

function validateTel($tel_number) {
    if (! preg_match( '/[^+\d]/', $tel_number) && preg_match( '/[+]/', $tel_number )) {
        return preg_match( '/[+\d]/', $tel_number );

    }
}

// ! Potrebna za koja opcija vo selektot da ostane selektirana
function option_is_selected($option, $test, $method) {
    if ($_SERVER['REQUEST_METHOD'] == $method) {
        if ($option == $test) {
            return true;
        }
    }
    return false;
}
// funkcija za citanje na text dokument linija po linija (test funkcija)
function readLineByLine($fileToOpen) {
    $file = fopen($fileToOpen,"r");

while(! feof($file))
  {
  echo fgets($file). "<br />";
  }

fclose($file);
}
// converzija na text fajl vo niza za polesna upotreba
function fileToArray($fileToMakeArray) {

    $file = fopen($fileToMakeArray,"r");

    $tempArrey = [];

    while(! feof($file))
        {
            $tempArrey[] = explode(', ', fgets($file));
        }
    fclose($file);
    
    return $tempArrey;
}
// Proveruva dali email postoi vo nizata.
function emailExistsIn($email, $arrey) {
    $temp = false;
    foreach ($arrey as $key => $value) {
        if ((isset($value[0])) && ($value[0] === $email)) {
            $temp = true;
            break;
        }
    }
    return $temp;
}
// Proveruva dali username postoi vo nizata
function usernameExistsIn($username, $arrey) {
    $temp = false;
    foreach ($arrey as $key => $value) {
        if (isset($value[1])) {
            $tempArr = explode('=', $value[1]);
            if ($tempArr[0] === $username) {
                $temp = true;
                break;
            }
        }
    }
    return $temp;
}
// Proveruva dali login informaciite vneseni se korektni
function loginCheck($email, $username, $password, $arrey) {
    $str = $username . "=" . $password . "\n";
    $temp = false;
    foreach ($arrey as $key => $value) {
        if ((isset($value[0])) && ($value[0] === $email)) {
            if (isset($value[1]) && ($value[1] === $str)) {
                
                $temp = true;
                break; 
            }
        }
    }
    return $temp;
}
// Proveruva dali email i username se korektni i vraca password za istite
function returnPass($email, $username, $arrey) {
    $temp = '';
    foreach ($arrey as $key => $value) {
        if ((isset($value[0])) && ($value[0] === $email)) {
            if (isset($value[1])) {
                $tempArr = explode('=', $value[1]);
                if ($tempArr[0] === $username) {
                    $temp = $tempArr[1];
                    break;
                }
            }
            $temp = true;
            break;
        }
    }
    return $temp;
}
// bi trebalo da proveri dali nekoj string go ima vo text fajl, ama ne se koristi
function has_string($file, $arrey, $element) {
    $string = $arrey[$element];
    $valid = FALSE;
    $handle = fopen($file, "r");
    if ($handle) {
        // Read file line-by-line
        while (($buffer = fgets($handle))) {
            if (strpos($buffer, $string))
                $valid = TRUE;
        }
    }
    fclose($handle);
    return $valid;
}
// bi trebalo da proveri dali nekoj string go ima vo fajl. Ne se koristi
function check_Email_In_File($file, $email) {
    // $tempfile = fopen("$file", "r" or die("Unable to open file!"));
    $arrey=[];
    $str='';
    while (!feof($file)) {
        $str = fgets($file);
        $arrey = explode(', ', $str);
        if ($arrey[0] == $email) {
            return true;
            break;
        }
    }
    return false;
}

// $myfile = fopen("webdictionary.txt", "r") or die("Unable to open file!");
// // Output one line until end-of-file
// while(!feof($myfile)) {
//   echo fgets($myfile) . "<br>";
// }
// fclose($myfile);



// Does string contain letters?
function _s_has_letters( $string ) {
    return preg_match( '/[a-zA-Z]/', $string );
}

// Does string contain numbers?
function _s_has_numbers( $string ) {
    return preg_match( '/\d/', $string );
}

// Does string contain special characters?
function _s_has_special_chars( $string ) {
    return preg_match('/[^a-zA-Z\d]/', $string);
}

// Username moze da ima samo bukvi i brojki
function validateUsername($string) {
    if (!_s_has_special_chars( $string )) {
        return true;
    }
    return false;
}
// Password mora da ima povece od 8 karakteri, barem edna golema bukva i barem eden specijalen znak.
function validatePassword($string) {
    if (strlen($string) >= 8) {
        if (_s_has_special_chars( $string )) {
            if (_s_has_numbers( $string )) {
                if (_s_has_letters( $string )) {
                    return true;
                }
            }
        }
    }
    return false;
}
// proveruva dali dva stringa se isti
function stringMatch($str1, $str2) {
    if ($str1 == $str2) {
        return true;
    }
    return false;
}
// proverka dali vnesenite passwordi se isti pri registracija
function passwordCheck($array) {
    $temp=$array;
    if ($temp['password'] == $temp['repeatPass']) {
        return true;
    }
    return false;
}
// dodava nov user vo text dokumentot.
function newUser($file, $arrey) {
    $Myfile = fopen($file, 'a') or die("Unable to open file!");
    $str = $arrey['email'] . ", " . $arrey['username'] . "=" . $arrey['password'] . "\n";
    fwrite($Myfile, $str);
    fclose($Myfile);
}



?>
