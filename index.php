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
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Пошук через Google API</title>
</head>
<body>
<h2>Пошукова система</h2>
<form method="GET" action="/index.php">
    <label for="search">Search:</label>
    <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>"><br><br>
    <input type="submit" value="Submit">
</form>

<?php
// !!! TODO 2: відображення результатів
if (!empty($items)) {
    echo "<h3>Результати пошуку:</h3><ul>";
    foreach ($items as $item) {
        echo "<li><a href='" . htmlspecialchars($item['link']) . "' target='_blank'>" .
            htmlspecialchars($item['title']) . "</a><br>" .
            htmlspecialchars($item['snippet']) . "</li><br>";
    }
    echo "</ul>";
} elseif ($search !== '') {
    echo "<p>нічого не знайдено або сталася помилка у запиті</p>";
}
?>
</body>
</html>
