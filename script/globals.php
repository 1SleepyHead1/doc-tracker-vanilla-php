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

function saveItemLogs($c, $externalDonation, $externalDonationNo, $externalDonationD, $donationD, $donor, $item, $quantity, $expiry)
{
    $q = $c->prepare("INSERT INTO external_d_items_logs(external_d_s,external_donation_no,external_d_d,donation_d,donor,item,quantity,expiry_date) VALUES(?,?,?,?,?,?,?,?)");
    $q->execute([$externalDonation, $externalDonationNo, $externalDonationD, $donationD, $donor, $item, $quantity, $expiry]);
}

function getLogsDonated($c, $externalDonation, $externalDonationD, $donationD, $item, $expiry)
{
    $q = $c->prepare("SELECT quantity FROM external_d_items_logs WHERE  external_d_s = ? AND external_d_d = ? AND donation_d = ? AND item = ? AND expiry_date = ?");
    $q->execute([$externalDonation, $externalDonationD, $donationD, $item, $expiry]);

    return $q->fetchColumn() ?? 0;
}
