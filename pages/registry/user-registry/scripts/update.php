<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}

require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $c->beginTransaction();

    $response = ['status' => true, 'message' => ''];
    $userTypes = [
        "student" => "Student",
        "faculty" => "Faculty",
        "staff" => "Staff",
        "outside-client" => "Outside Client"
    ];
    $id = sanitize($_POST['id']);
    $firstName = ucfirst(sanitize($_POST['firstName']));
    $middleName = ucfirst(sanitize($_POST['middleName']));
    $lastName = ucfirst(sanitize($_POST['lastName']));
    $extensionName = sanitize($_POST['extensionName']);
    $userCategory = sanitize($_POST['userCategory']);
    $userType = $userCategory === "office" ? null : sanitize($_POST['userType']);
    $name = $extensionName == "" ? "$firstName $middleName $lastName" : "$firstName $middleName $lastName $extensionName";
    $province = sanitize($_POST['province']);
    $city = sanitize($_POST['city']);
    $barangay = sanitize($_POST['barangay']);
    $address = "$province $city, $barangay";
    $email = sanitize($_POST['email']);
    $contactNumber = sanitize($_POST['contactNumber']);

    $checkDuplicate = $c->prepare("SELECT id FROM users WHERE first_name = ? AND middle_name = ? AND last_name = ? AND extension = ? AND id<>?;");
    $checkDuplicate->execute([$firstName, $middleName, $lastName, $extensionName, $id]);

    if ($checkDuplicate->rowCount() > 0) {
        $response['status'] = false;
        $response['message'] = 'User already exists';
        echo json_encode($response);
        exit;
    }

    $update = $c->prepare("
        UPDATE users SET
            user_type = ?,
            first_name = ?,
            middle_name = ?,
            last_name = ?,
            extension = ?,
            name = ?,
            province = ?,
            city = ?,
            barangay = ?,
            address = ?,
            email = ?,
            contact_no = ?
        WHERE id = ?;
    ");

    $update->execute([
        $userType,
        $firstName,
        $middleName,
        $lastName,
        $extensionName,
        $name,
        $province,
        $city,
        $barangay,
        $address,
        $email,
        $contactNumber,
        $id
    ]);


    if ($response['status']) {
        $c->commit();
        $response['data'] = [
            'id' => $id,
            'userType' => $userTypes[$userType],
            'userCategory' => $userCategory,
            'name' => $name,
            'email' => $email,
            'contactNo' => $contactNumber,
            'address' => $address,
        ];
    }
} catch (PDOException $e) {
    $c->rollBack();

    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
