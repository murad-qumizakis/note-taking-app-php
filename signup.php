<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <title>Sign Up</title>
</head>
<body>
    <h1>Sign Up</h1>
    <form method="POST" action="receive.php">
        <label for="username">Username</label>
        <p>
            <input type="text" name="username" id="username">
        </p>
        <label for="username">First Name</label>
        <p>
            <input type="text" name="firstname" id="firstname">
        </p>
        <label for="username">Last Name</label>
        <p>
            <input type="text" name="lastname" id="lastname">
        </p>
        <label for="password">Password</label>
        <p>
            <input type="password" name="password" id="password">
        </p>
        <label for="confirmPassword">Confirm Password</label>
        <p>
            <input type="password" name="confirmPassword" id="confirmPassword">
        </p>
        <p>
            <input type="submit" value="Sign Up" name="signup" id="signUp">
        </p>
        <p>
        Already have an account? 
            <a href="signin.php">Sign In</a>
        </p>
    </form>
</body>
</html>
