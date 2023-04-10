<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <title>New Password</title>
</head>
<body>

    <h1>Get a new password emailed to you</h1>
    <form method="POST" action="receive.php">
        <label for="username">Username</label>
        <p>
            <input type="text" name="username" id="username">
        </p>
        <p>
            <input type="submit" value="Get New Password" name="newpassword" id="newpassword">
        </p>
    </form>
</body>
</html>
