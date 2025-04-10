<?php
// TODO 1: PREPARING ENVIRONMENT: 1) session 2) functions
session_start();

// TODO: render guestBook comments — function
function renderComments()
{
    if (file_exists('comments.csv')) {
        $file = fopen('comments.csv', 'r');
        while (!feof($file)) {
            $line = fgets($file);
            $data = json_decode($line, true);
            if ($data) {
                echo "<div class='card mb-2 p-2'>";
                echo "<strong>" . htmlspecialchars($data['name']) . "</strong> (" . htmlspecialchars($data['email']) . ") <br>";
                echo "<small>{$data['date']}</small><br>";
                echo "<p>" . nl2br(htmlspecialchars($data['text'])) . "</p>";
                echo "</div>";
            }
        }
        fclose($file);
    }
}

// TODO 2: ROUTING (not used explicitly here)

// TODO 3: CODE by REQUEST METHODS (handle data from form)
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

        $jsonString = json_encode($comment);
        $file = fopen('comments.csv', 'a');
        fwrite($file, $jsonString . "\n");
        fclose($file);

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
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
