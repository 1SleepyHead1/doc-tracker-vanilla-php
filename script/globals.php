<?php

function sanitize($a)
{
    $a = trim($a);
    $a = stripslashes($a);
    $a = htmlspecialchars($a);

    return $a;
}

function getYearsDifference($dateString)
{
    // Create DateTime objects for the given date and the current date
    $givenDate = new DateTime($dateString);
    $currentDate = new DateTime();

    // Calculate the difference between the two dates
    $interval = $givenDate->diff($currentDate);

    // Return the number of years in the difference
    return $interval->y;
}

function convertToUTF8($data)
{
    if (is_array($data)) {
        foreach ($data as &$value) {
            $value = convertToUTF8($value);
        }
    } elseif (is_string($data)) {
        return mb_convert_encoding($data, 'UTF-8', 'auto');
    }

    return $data;
}


function getRemainingTime($targetDate)
{
    // Convert target date to a timestamp
    $targetTimestamp = strtotime($targetDate);

    // Get the current timestamp
    $currentTimestamp = time();

    // Calculate the difference in seconds
    $remainingSeconds = $targetTimestamp - $currentTimestamp;

    // If the target date is in the past, return zero values
    if ($remainingSeconds < 0) {
        return [
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
        ];
    }

    // Calculate days, hours, and minutes
    $remainingDays = floor($remainingSeconds / (60 * 60 * 24));
    $remainingSeconds -= $remainingDays * (60 * 60 * 24);

    $remainingHours = floor($remainingSeconds / (60 * 60));
    $remainingSeconds -= $remainingHours * (60 * 60);

    $remainingMinutes = floor($remainingSeconds / 60);

    // Return the results
    return [
        'days' => $remainingDays,
        'hours' => $remainingHours,
        'minutes' => $remainingMinutes,
    ];
}

function getRemainingDays($targetDate)
{
    // Convert the target date to a Unix timestamp
    $targetTimestamp = strtotime($targetDate);

    // Get the current timestamp
    $currentTimestamp = time();

    // Calculate the difference in days
    $remainingDays = ceil(($targetTimestamp - $currentTimestamp) / (60 * 60 * 24));

    return $remainingDays;
}

function generateDocNo($length = 15)
{
    $prefix = "";
    $uniqueString = substr(md5(uniqid(mt_rand() . microtime(), true)), 0, $length - strlen($prefix));
    return $prefix . $uniqueString;
}

function imgToBlob($imgPath)
{
    $imgData = file_get_contents($imgPath);
    $imgBlob = 'data:image/' . pathinfo($imgPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($imgData);
    return $imgBlob;
}

function saveDocTransactionLogs($c, $docNo, $step = 0, $status = "New", $office = null, $updatedBy = null, $remarks = null)
{
    $query = $c->prepare("
        INSERT INTO document_transaction_logs(
            doc_number,
            step,
            status,
            office,
            updated_by,
            remarks
        ) VALUES(?,?,?,?,?,?);
    ");
    $query->execute([$docNo, $step, $status, $office, $updatedBy, $remarks]);
}

function saveLoginLogs($c, $token, $userAccount)
{
    $check = $c->prepare("SELECT id FROM login_logs WHERE user_account_id=?;");
    $check->execute([$userAccount]);

    if ($check->rowCount() > 0) {
        $reset = $c->prepare("DELETE FROM login_logs WHERE user_account_id=?;");
        $reset->execute([$userAccount]);
    }

    $query = $c->prepare("INSERT INTO login_logs(token,user_account_id) VALUES(?,?);");
    $query->execute([$token, $userAccount]);
}

function getLogsStamp($timestamp)
{
    $currentTime = time();
    $givenTime = strtotime($timestamp);
    $difference = $currentTime - $givenTime;

    $years = floor($difference / (60 * 60 * 24 * 365));
    $weeks = floor($difference / (60 * 60 * 24 * 7));
    $days = floor($difference / (60 * 60 * 24));

    if ($years > 0) {
        return $years . " year" . ($years > 1 ? "s" : "") . " ago";
    } else if ($weeks > 0) {
        return $weeks . " week" . ($weeks > 1 ? "s" : "") . " ago";
    } else if ($days > 0) {
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    } else {
        return "Less than a day ago";
    }
}
