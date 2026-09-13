<?php

$name = $_POST['name'] ?? '';
$size = $_POST['size'] ?? '';
$date = date("d/m/Y h:i A");


header("Content-Type: application/json");


$file = "data.json";

/* Read existing data */
$data = [];

if (file_exists($file)) {
    $json = file_get_contents($file);

    if (!empty($json)) {
        $data = json_decode($json, true);

        if (!is_array($data)) {
            $data = [];
        }
    }
}

/* New item */
$newItem = [
    "name" => $name,
    "size" => $size,
    "date" => $date
];

/* Add new item */
$data[] = $newItem;

/* Save JSON */
file_put_contents(
    $file,
    json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

$data = [
    "success" => true,
    "name" => $name,
    "message" => $name.", has successfully been save"
];

echo json_encode($data);

?>