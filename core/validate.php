<?php
function validatePassword($password) {
    if (strlen($password) >= 8) {
        $hasLower = false;
        $hasUpper = false;
        $hasNumber = false;
        for ($i = 0; $i < strlen($password); $i++) {
            if (ctype_lower($password[$i])) $hasLower = true;
            elseif (ctype_upper($password[$i])) $hasUpper = true;
            elseif (ctype_digit($password[$i])) $hasNumber = true;
            if ($hasLower && $hasUpper && $hasNumber) return true;
        }
    }
    return false;
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateName($name) {
    return preg_match("/^[a-zA-Z-' ]*$/", $name);
}

function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function validateTime($time) {
    $t = DateTime::createFromFormat('H:i:s', $time);
    return $t && $t->format('H:i:s') === $time;
}

function sanitizeAndValidate($inputArray) {
    foreach ($inputArray as $key => $value) {
        $inputArray[$key] = sanitizeInput($value);
    }
    return $inputArray;
}
?>
