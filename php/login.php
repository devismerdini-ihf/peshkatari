<?php
if (isset($_POST['loginbtn'])) {
    $Enteredusername = $_POST['enteredusername'];
    $Enteredpassword = $_POST['enteredpassword'];

    // 1. Prepare SQL statement to select user by username (DO NOT select password yet)
    // Using LIMIT 1 is a good practice as usernames should be unique
    $loginquery = "SELECT user_id, user_password, user_username, user_pic, user_status FROM users WHERE user_username = ? LIMIT 1";

    // 2. Initialize and prepare the statement
    $stmt = mysqli_prepare($connect, $loginquery);
    mysqli_stmt_bind_param($stmt, "s", $Enteredusername); // "s" for string

    // 3. Execute the statement
    mysqli_stmt_execute($stmt);

    // 4. Get the result set and data
    $loginresult = mysqli_stmt_get_result($stmt);

    if ($loginresult && $userdata = mysqli_fetch_assoc($loginresult)) {
        // 5. Verify the password using the stored hash
        if (password_verify($Enteredpassword, $userdata['user_password'])) {
            // Success! Set session variables
            $_SESSION['userid'] = $userdata['user_id'];
            $_SESSION['userusename'] = $userdata['user_username'];
            $_SESSION['userpic'] = $userdata['user_pic'];
            $_SESSION['userstatus'] = $userdata['user_status'];

            mysqli_stmt_close($stmt);
            header('location: dashboard.php');
            exit;
        }
    }
    // Failure (no user found or password didn't match)
    mysqli_stmt_close($stmt);
    header("location: login.php?nouserfound=No user was found");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Log in</title>
</head>

<body>
    <?php include "components/navbar.php";
    ?>
    <main>
        <section class="forms-section">
            <h1 class="section-title">Animated Forms</h1>
            <div class="forms">
                <div class="form-wrapper is-active">
                    <?php
                    if (isset($get['nouserfound'])) {
                        echo "<p>no user was found</p>";
                    }
                    ?>
                    <button type="button" class="switcher switcher-login">
                        Login
                        <span class="underline"></span>
                    </button>
                    <form class="form form-login" method="post">

                        <fieldset>
                            <legend>Please, enter your username and password for login.</legend>
                            <div class="input-block">
                                <label for="login-username">Username</label>
                                <input id="login-username" name="enteredusername" type="text" required>
                            </div>
                            <div class="input-block">
                                <label for="login-password">Password</label>
                                <input id="login-password" name="enteredpassword" type="password">
                            </div>
                        </fieldset>
                        <button type="submit" name="loginbtn" class="btn-login">Login</button>
                        <?php
                        include "connection/connect.php";
                        if (isset($_POST['loginbtn'])) {
                            $Enteredusername = $_POST['enteredusername'];
                            $Enteredpassword = $_POST['enteredpassword'];

                            // 1. Prepare SQL statement to select user by username (DO NOT select password yet)
                            // Using LIMIT 1 is a good practice as usernames should be unique
                            $loginquery = "SELECT user_id, user_password, user_username, user_pic, user_status FROM users WHERE user_username = ? LIMIT 1";

                            // 2. Initialize and prepare the statement
                            $stmt = mysqli_prepare($connect, $loginquery);
                            mysqli_stmt_bind_param($stmt, "s", $Enteredusername); // "s" for string

                            // 3. Execute the statement
                            mysqli_stmt_execute($stmt);

                            // 4. Get the result set and data
                            $loginresult = mysqli_stmt_get_result($stmt);

                            if ($loginresult && $userdata = mysqli_fetch_assoc($loginresult)) {
                                // 5. Verify the password using the stored hash
                                if (password_verify($Enteredpassword, $userdata['user_password'])) {
                                    // Success! Set session variables
                                    $_SESSION['userid'] = $userdata['user_id'];
                                    $_SESSION['userusename'] = $userdata['user_username'];
                                    $_SESSION['userpic'] = $userdata['user_pic'];
                                    $_SESSION['userstatus'] = $userdata['user_status'];

                                    mysqli_stmt_close($stmt);
                                    header('location: dashboard.php');
                                    exit;
                                }
                            }
                            // Failure (no user found or password didn't match)
                            mysqli_stmt_close($stmt);
                            header("location: login.php?nouserfound=No user was found");
                            exit;
                        }
                        ?>
                    </form>
                </div>
                <div class="form-wrapper">
                    <button type="button" class="switcher switcher-signup">
                        Sign Up
                        <span class="underline"></span>
                    </button>
                    <form class="form form-signup" method="post">
                        <fieldset>
                            <legend>Please, enter your email, password and password confirmation for sign up.</legend>
                            <div class="input-block">
                                <label for="signup-email">E-mail</label>
                                <input id="signup-email" type="email" name="signup-email" required>
                            </div>
                            <div class="input-block">
                                <label for="signup-username">Username</label>
                                <input id="signup-username" type="text" name="signup-username" required>
                            </div>
                            <div class="input-block">
                                <label for="signup-password">Password</label>
                                <input id="signup-password" type="password" name="signup-password" required>
                            </div>
                        </fieldset>
                        <button type="submit" class="btn-signup" name="signupnewuser">Continue</button>
                        <?php
                        if (isset($_POST['signupnewuser'])) {
                            $newuserusername = $_POST['signup-username'];
                            $newusersignupemail = $_POST['signup-email'];
                            $rawPassword = $_POST['signup-password'];

                            // 1. Securely hash the raw password
                            $newusersignuppassword = password_hash($rawPassword, PASSWORD_DEFAULT);

                            // 2. Prepare the INSERT statement
                            $newuserquery = "INSERT INTO users (user_email, user_password, user_username) VALUES (?, ?, ?)";

                            // 3. Initialize and prepare the statement
                            $stmt = mysqli_prepare($connect, $newuserquery);
                            mysqli_stmt_bind_param($stmt, "sss", $newusersignupemail, $newusersignuppassword, $newuserusername); // "sss" for three strings

                            // 4. Execute the statement
                            if (mysqli_stmt_execute($stmt)) {
                                // Success
                                echo "<h1>New user created! You can now log in.</h1>";
                                // Optionally redirect the user to the login form
                                // header("Location: login.php?success=1");
                                // exit;
                            } else {
                                // Failure
                                echo "error creating user! " . mysqli_error($connect) . " detected during execution";
                            }

                            mysqli_stmt_close($stmt);
                        }
                        ?>
                    </form>
                </div>
                <div class="form-wrapper">
                    <button type="button" class="switcher switcher-secure-change">
                        Change Password
                        <span class="underline"></span>
                    </button>
                    <form class="form form-secure-change" method="post">
                        <fieldset>
                            <legend>Verify your identity and set a new password.</legend>

                            <div class="input-block">
                                <label for="change-username">Username</label>
                                <input id="change-username" type="text" name="change-username" required>
                            </div>

                            <div class="input-block">
                                <label for="old-password">Current Password</label>
                                <input id="old-password" type="password" name="old-password" required>
                            </div>

                            <div class="input-block">
                                <label for="new-password">New Password</label>
                                <input id="new-password" type="password" name="new-password" required>
                            </div>

                            <div class="input-block">
                                <label for="confirm-new-password">Confirm New Password</label>
                                <input id="confirm-new-password" type="password" name="confirm-new-password" required>
                            </div>
                        </fieldset>
                        <button type="submit" class="btn-change-password" name="secure-change-password-btn">Update Password</button>

                        <?php
                        // PHP logic for changing the password will be inserted here
                        ?>
                    </form>
                </div>
            </div>
        </section>
    </main>


    <?php
    if (isset($_POST['change-password-btn'])) {
        $currentPassword = $_POST['current-password'];
        $newPassword = $_POST['new-password'];
        $confirmPassword = $_POST['confirm-password'];
        $userId = $_SESSION['userid'];

        // --- Validation Checks ---
        if ($newPassword !== $confirmPassword) {
            echo "<p class='error'>New passwords do not match.</p>";
            // You might want to redirect with a URL parameter here instead of echoing.
        } elseif (strlen($newPassword) < 8) {
            echo "<p class='error'>New password must be at least 8 characters long.</p>";
        } else {
            // --- 1. Fetch Current Hash using Prepared Statement ---
            $fetchQuery = "SELECT user_password FROM users WHERE user_id = ? LIMIT 1";
            $stmt = mysqli_prepare($connect, $fetchQuery);
            mysqli_stmt_bind_param($stmt, "i", $userId); // "i" for integer
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && $user = mysqli_fetch_assoc($result)) {
                $storedHash = $user['user_password'];

                // --- 2. Verify Current Password ---
                if (password_verify($currentPassword, $storedHash)) {

                    // --- 3. Hash New Password ---
                    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    // --- 4. Update Database with New Hash using Prepared Statement ---
                    $updateQuery = "UPDATE users SET user_password = ? WHERE user_id = ?";
                    $updateStmt = mysqli_prepare($connect, $updateQuery);
                    mysqli_stmt_bind_param($updateStmt, "si", $newHashedPassword, $userId); // "s" for string, "i" for integer

                    if (mysqli_stmt_execute($updateStmt)) {
                        echo "<p class='success'>Password successfully changed! 🎉</p>";
                    } else {
                        echo "<p class='error'>Database error during update: " . mysqli_error($connect) . "</p>";
                    }
                    mysqli_stmt_close($updateStmt);
                } else {
                    echo "<p class='error'>Current password entered is incorrect.</p>";
                }
            } else {
                echo "<p class='error'>User not found.</p>";
            }
            mysqli_stmt_close($stmt);
        }
    }
    include "components/footer.php";
    ?>

</body>
<script>
    const switchers = [...document.querySelectorAll('.switcher')]
    const resetWrapper = document.querySelector('.form-wrapper:last-child'); // Target the new wrapper

    switchers.forEach(item => {
        item.addEventListener('click', function() {
            // Find the active form wrapper and remove the class
            document.querySelector('.form-wrapper.is-active').classList.remove('is-active');

            // Add the class to the clicked switcher's parent
            this.parentElement.classList.add('is-active');
        })
    })
</script>
<script src="../js/script.js">

</script>

</html>