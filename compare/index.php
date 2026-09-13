<?php
$json = file_get_contents("../PHP/data.json");
$data = json_decode($json, true);
if (isset($_GET['name'])) {
    $displayCompareData = true;
    $name = $_GET['name'];
} else {
    $displayCompareData = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Name</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="main">
        <div class="container">
            <form action="./">
                <input type="search" list="names" placeholder="Search Your Name" name="name">
                <button>Search</button>
            </form>
            <datalist id="names">
                <?php foreach ($data as $item): ?>

                    <option value="<?= htmlspecialchars($item['name']) ?>"></option>

                <?php endforeach; ?>
            </datalist>
            <?php
            if ($displayCompareData) {
                $exists = false;
                foreach ($data as $item) {
                    if ($item['name'] === $name) {
                        $exists = true;
                        $size = $item['size'];
                        break;
                    }
                }

                if ($exists) { ?>

                    <table>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Date</th>
                            <!-- <th>perce...</th> -->
                        </tr>
                        <?php
                        $count = 0;
                        foreach ($data as $item) {
                            if ($item['name'] != $name) { ?>
                                <?php
                                $smaller = ($size > $item['size']) ? $item['size'] : $size;
                                $larger = ($size > $item['size']) ? $size : $item["size"];
                                $per = ($smaller / $larger) * 100;
                                ?>

                                <tr class='<?php echo ($per > 97) ?  "error" : '';?>'>
                                    <td><?php echo ++$count; ?></td>
                                    <td><?php echo $item['name']; ?></td>
                                    <td><?php echo $item['date']; ?></td>

                                    <!-- <td><?php echo floor($per) ?></td> -->
                                </tr>

                        <?php

                            }
                        }
                        ?>
                    </table>

            <?php
                } else {
                    echo "<p class='error'>Fahhhh.... 404, Name not found in list</p>";
                }
            } else {
                echo "<p class='error'>Search Your Name to compare.</p>";
            }
            ?>

        </div>
    </div>
</body>

</html>