<?php
// !!! TODO 1: обробка GET-запиту

$apiKey = "AIzaSyDM13cTEeimvd5aAtvb80E7fcM7eepVrh0";
$cx = "03f5b7dc217dc40b4";

$items = [];
$search = '';

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];

    $url = "https://www.googleapis.com/customsearch/v1?key={$apiKey}&cx={$cx}&q=" . urlencode($search);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['items'])) {
        $items = $data['items'];
    }

//     echo "<pre>";
//     echo $url;
//     var_dump($items);
//     var_dump($response);
//     curl_exec($ch);
//     echo "</pre>";
}

if (isset($_GET['goto']) && !empty($_GET['goto'])) {
    $gotoUrl = $_GET['goto'];
    header("Location: " . $gotoUrl);
    exit;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Пошук та перехід за URL</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h2, h3 {
            color: #333;
        }
        form {
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 1em;
        }
        input[type="submit"]:hover {
            background-color: #51a351;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            margin-bottom: 15px;
            background-color: #fff;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        li a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        li a:hover {
            text-decoration: underline;
        }
        li p {
            color: #666;
            margin-top: 5px;
        }
        .goto-form {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
<h2>Пошукова система</h2>
<form method="GET" action="index.php">
    <label for="search">Search:</label>
    <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>"><br><br>
    <input type="submit" value="Submit">
</form>
<div class="goto-form">
    <h3>Перейти за URL</h3>
    <form method="GET" action="index.php">
        <label for="goto">Введіть URL:</label>
        <input type="text" id="goto" name="goto"><br><br>
        <input type="submit" value="Перейти">
    </form>
</div>

<?php
// !!! TODO 2: відображення результатів
if (!empty($items)) {
    echo "<h3>Результати пошуку:</h3><ul>";
    foreach ($items as $item) {
        echo "<li><a href='" . htmlspecialchars($item['link']) . "' target='_blank'>" .
            htmlspecialchars($item['title']) . "</a><br>" .
            "<p>" . htmlspecialchars($item['snippet']) . "</p></li><br>";
    }
    echo "</ul>";
} elseif ($search !== '') {
    echo "<p>нічого не знайдено або сталася помилка у запиті</p>";
}
?>
</body>
</html>
