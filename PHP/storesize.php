<?php

header("Content-Type: application/json");

$name = trim($_POST['name'] ?? '');

if ($name === '') {
    echo json_encode([
        "success" => false,
        "message" => "Name is required."
    ]);
    exit;
}


/* Check file */
if (!isset($_FILES['file'])) {
    echo json_encode([
        "success" => false,
        "message" => "No file was uploaded."
    ]);
    exit;
}

$file = $_FILES['file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        "success" => false,
        "message" => "File upload failed."
    ]);
    exit;
}


/* Get old/original filename */
$oldFileName = $file['name'];


/* Get actual MIME type */
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);


/* Allowed MIME types */
$allowedTypes = [

    // PDF
    "application/pdf",

    // Documents
    "application/msword",
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    "text/plain",

    // Excel
    "application/vnd.ms-excel",
    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",

    // PowerPoint
    "application/vnd.ms-powerpoint",
    "application/vnd.openxmlformats-officedocument.presentationml.presentation",

    // Images
    "image/jpeg",
    "image/png",
    "image/gif",
    "image/webp",
    "image/bmp"
];


if (!in_array($mimeType, $allowedTypes, true)) {
    echo json_encode([
        "success" => false,
        "message" => "This file type is not allowed."
    ]);
    exit;
}


/* Get extension */
$extension = strtolower(
    pathinfo($oldFileName, PATHINFO_EXTENSION)
);


/* Images folder */
$uploadFolder = __DIR__ . "/images/";


/* Create folder if it doesn't exist */
if (!is_dir($uploadFolder)) {
    if (!mkdir($uploadFolder, 0755, true)) {
        echo json_encode([
            "success" => false,
            "message" => "Could not create images folder."
        ]);
        exit;
    }
}


/* Generate unique filename */
$newGeneratedName = bin2hex(random_bytes(16)) . "." . $extension;


/* Complete destination */
$destination = $uploadFolder . $newGeneratedName;


/* Move uploaded file */
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    echo json_encode([
        "success" => false,
        "message" => "Could not save uploaded file."
    ]);
    exit;
}


/* File size */
$size = $file['size'];

$date = date("d/m/Y h:i A");


/* JSON file */
$jsonFile = __DIR__ . "/data.json";


/* Read existing data */
$data = [];

if (file_exists($jsonFile)) {

    $json = file_get_contents($jsonFile);

    if (!empty($json)) {

        $decoded = json_decode($json, true);

        if (is_array($decoded)) {
            $data = $decoded;
        }
    }
}


/* New item */
$newItem = [
    "name" => $name,
    "size" => $size,
    "date" => $date,
    "fileName" => $oldFileName,
    "newGeneratedName" => $newGeneratedName
];


/* Add item */
$data[] = $newItem;


/* Save JSON */
$result = file_put_contents(
    $jsonFile,
    json_encode(
        $data,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
    )
);


if ($result === false) {

    // Remove uploaded file if JSON couldn't be saved
    if (file_exists($destination)) {
        unlink($destination);
    }

    echo json_encode([
        "success" => false,
        "message" => "Could not save data."
    ]);

    exit;
}


/* Success */
echo json_encode([
    "success" => true,
    "name" => $name,
    "oldfileName" => $oldFileName,
    "fileName" => $newGeneratedName,
    "message" => $name . ", has successfully been saved."
]);

?>