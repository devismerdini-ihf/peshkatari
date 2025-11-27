<?php
    session_start();

    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 7200)) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;
    }
    $_SESSION['last_activity'] = time();

    if (!isset($_SESSION['userid'])) {
        header("Location: login.php");
        exit;
    }

    include "connection/connect.php";
    include "components/navbar.php";
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("Location: " . $baseURL . "login.php");
        exit;
    }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Dashboard</title>
</head>

<body>

    <div class="container">

        <h1>
            Welcome back, <?php echo htmlspecialchars($_SESSION['userusename']); ?>!
        </h1>

    </div>
    <main></main>

    <?php include "components/footer.php"; ?>


    <script src="../js/script.js"></script>
</body>

</html>