<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <title>Sign In</title>
</head>
<body>

    <h1>Sign in</h1>
    <form method="POST" action="receive.php">
        <label for="username">Username</label>
        <p>
            <input type="text" name="username" id="username">
        </p>

        <label for="password">Password</label>
        <p>
            <input type="password" name="password" id="password">
            <a href="newpassword.php" value="New Password">New Password</a>
        </p>
        <p>
            <input type="submit" value="Sign In" name="signin" id="signIn">
        </p>
        <p>
            <a href="signup.php">Register</a>
        </p>
    </form>
</body>
</html>
