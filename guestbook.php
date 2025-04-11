<?php
// TODO 1: PREPARING ENVIRONMENT: 1) session 2) functions
session_start();

// TODO: render guestBook comments — function

$aConfig = require_once 'config.php';

$db = mysqli_connect(
    $aConfig['host'],
    $aConfig['user'],
    $aConfig['pass'],
    $aConfig['name']
);

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}
// оновлена функція для рендеру комментів
function renderComments()
{
    global $db;
    $query = 'SELECT * FROM comments ORDER BY date DESC';
    $result = mysqli_query($db, $query);

    if ($result) {
        while ($comment = mysqli_fetch_assoc($result)) {
            echo "<div class='card mb-2 p-2'>";
            echo "<strong>" . htmlspecialchars($comment['name']) . "</strong> (" . htmlspecialchars($comment['email']) . ") <br>";
            echo "<small>{$comment['date']}</small><br>";
            echo "<p>" . nl2br(htmlspecialchars($comment['text'])) . "</p>";
            echo "</div>";
        }
    } else {
        echo "Error: " . mysqli_error($db);
    }
}

// TODO 2: ROUTING (not used explicitly here)

// TODO 3: CODE by REQUEST METHODS (handle data from form)

// оновлений код форми для комментів
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $name = trim(isset($_POST['name']) ? $_POST['name'] : '');
    $text = trim(isset($_POST['text']) ? $_POST['text'] : '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Некоректний email.";
    }

    if (empty($name)) {
        $errors[] = "Ім’я не може бути порожнім.";
    }

    if (empty($text)) {
        $errors[] = "Коментар не може бути порожнім.";
    }

    if (empty($errors)) {
        $comment = [
            'email' => $email,
            'name' => $name,
            'text' => $text,
            'date' => date('Y-m-d H:i:s')
        ];

        $query = "INSERT INTO comments (email, name, text, date) VALUES (
            '".mysqli_real_escape_string($db, $comment['email'])."',
            '".mysqli_real_escape_string($db, $comment['name'])."',
            '".mysqli_real_escape_string($db, $comment['text'])."',
            '".$comment['date']."'
        )";

        if (mysqli_query($db, $query)) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $errors[] = "Error inserting comment: " . mysqli_error($db);
        }
    }
}
?>



<!DOCTYPE html>
<html>

<?php require_once 'sectionHead.php' ?>

<body>
<div class="container">

    <?php require_once 'sectionNavbar.php' ?>
    <br>

    <div class="card card-primary">
        <div class="card-header bg-primary text-light">
            GuestBook form
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- TODO: create guestBook html form -->
                    <form method="POST">
                        <input type="email" name="email" placeholder="Your email" value="<?= htmlspecialchars(isset($_POST['email']) ? $_POST['email'] : '') ?>" required><br>
                        <input type="text" name="name" placeholder="Your name" value="<?= htmlspecialchars(isset($_POST['name']) ? $_POST['name'] : '') ?>" required><br>
                        <textarea name="text" placeholder="Your comment" required><?= htmlspecialchars(isset($_POST['text']) ? $_POST['text'] : '') ?></textarea><br>
                        <button type="submit">Send</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <br>

    <div class="card card-primary">
        <div class="card-header bg-body-secondary text-dark">
            Comments
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php renderComments(); ?>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>

<?php
mysqli_close($db);
?>