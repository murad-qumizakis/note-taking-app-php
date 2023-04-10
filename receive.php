<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

try {
    $servername = "containers-us-west-7.railway.app";
    $username = "root";
    $password = "Qfyzz0QzwmxY3pjiOMDd";
    $database = "railway";
    $port = 7841;
    $mysqli = new mysqli($servername, $username, $password, $database, $port);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    echo mysqli_connect_error();
}

// echo the head of the page




// SIGN UP
if(isset($_POST['signup'])){
    echo "sign up" . "<br>";
    $validEmail = false;
    $validPassword = false;

    // create table if not exists
    $mysqli->query("CREATE TABLE IF NOT EXISTS 
    user(
    id int primary key auto_increment, 
    username varchar(255) not null unique,
    firstname varchar(9) not null, 
    lastname varchar(255) not null,
    password varchar(255) not null, 
    active boolean default false
    )"
);
   
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confirmPassword']) && isset($_POST['firstname']) && isset($_POST['lastname'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $confirmPassword = $_POST['confirmPassword'];
    } else {
    echo "No username";
    }
    
    if ($password == $confirmPassword) {
    $validPassword = true;
    // hash pasword
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    } else {
    echo "Passwords do not match";
    }   

    if (filter_var($username, FILTER_VALIDATE_EMAIL) && $mysqli->query("SELECT * FROM user WHERE username='$username'")->num_rows == 0) {
    $validEmail = true;
    echo("$username is a valid email address and is available for use." ) . "<br>";
    } else {
    echo("$username is not a valid email address or is already taken. Please choose a different username.") . "<br>";
    exit;
    }   


    if ($validEmail && $validPassword) {
        $stmt = $mysqli->prepare("INSERT INTO user (username, password, firstname, lastname) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashedPassword, $firstname, $lastname);
        $stmt->execute();
        $stmt->close();
        // send an email to the user with a hyper link that once clicked will activate their account
        $mail = new PHPMailer(true);
        try {
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username = "phpprojectcomp4515@gmail.com";
            $mail->Password = "cyfvzyyukclstryt";        
            $mail->SMTPSecure = "ssl";        
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            //Recipients
            $mail->setFrom("phpprojectcomp4515@gmail.com", "PHP Project");
            $mail->addAddress($username);     //Add a recipient
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Activate your account';
            $mail->Body    = 'Click the link below to activate your account: <a href="http://localhost:8080/activate.php?username=' . $username . '">Activate</a>'; // i dont think this will work.
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        echo "User added" . "<br>"; 
    } else {
        echo "User not added" . "<br>";
    }


    // Retrieve the user's details
    $result = $mysqli->query("SELECT * FROM user WHERE username='$username'");


    $users = $mysqli->query("SELECT * FROM user;");

    echo "Number of users: " . $users->num_rows . "<br>";

    while ($row = $users->fetch_assoc()) {
    echo "id: " . $row["id"] . " - username: " . $row["username"] . " - firstname: " . $row["firstname"] . " - lastname: " . $row["lastname"] . "<br>";
    }


    // Check if the user exists
    if ($result->num_rows > 0) {
        // User exists
        $user = $result->fetch_assoc();
        echo "User added successfully. ID: " . $user['id'] . "<br>";
    } else {
        // User does not exist
        echo "User not added" . "<br>";
    }

} 

// SIGN IN
else if(isset($_POST["signin"])){
    echo "sign in" . "<br>";
if (!empty($_POST['username']) && !empty($_POST['password'])) {
    echo "not empty" . "<br>";
    try {
        // set a session var for the attempts
        if(!isset($_SESSION["attempts"])){
            $_SESSION["attempts"] = 0;
            echo $_SESSION["attempts"] . "<br>";
        }
        $username = $_POST['username'];
        $password = $_POST['password'];

        // check if user exists
        $sql = "SELECT * FROM user WHERE username = '$username'";
        $query = $mysqli->query($sql);
        if ($query->num_rows == 0) {
            echo "no user found with this email" . "<br>";
            $_SESSION['error'] = 'User does not exists, please sign up.'. "<br>";
            // header('Location: signin.php');
        } else {

            echo "user found" . "<br>";
                $row = $query->fetch_assoc();
                // check if the account is activated
                if ($row['active'] != 1) {
                    echo "user not active" . "<br>";
                    $_SESSION['error'] = 'User is not active, please activate your account.'. "<br>";
                    // header('Location: signin.php');
                } else {
                    echo "user active" . "<br>";
                    $hash = $row['password'];
                    // check if the password is correct
                    if (password_verify($password, $hash)) {
                        echo "password verified" . "<br>";
                        $_SESSION['user'] = $row['id'];
                        $_SESSION['success'] = 'You are now logged in.' . "<br>";
                        unset($_SESSION['attempts']);
                        // set a session variable named logged_in to true
                        $_SESSION['logged_in'] = true;
                        header('Location: index.php');
                        // check if the password is not correct
                    } else if (!password_verify($password, $hash)) {
                        // wrong password, add 1 to the attempts
                        echo "password not verified" . "<br>";
                        $_SESSION['attempts'] += 1;
                        if($_SESSION['attempts'] >= 3){
                            echo "too many attempts" . "<br>";
                            $_SESSION['error'] = 'Too many attempts, please try again later.'. "<br>";
                            // header('Location: signin.php');
                        } 
                        // if the attempts are 3, lock the account
                        if($_SESSION['attempts'] == 3){
                            echo "account locked" . "<br>";

                            $_SESSION['error'] = 'Wrong password.'. "<br>";

                           // send an email to let the user know that their account has been locked
                            $mail = new PHPMailer(true);
                            try {
                                //Server settings
                                $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                                $mail->isSMTP();                                            //Send using SMTP
                                $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
                                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                                $mail->Username = "phpprojectcomp4515@gmail.com";
                                $mail->Password = "cyfvzyyukclstryt";        
                                $mail->SMTPSecure = "ssl";        
                                $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
                                //Recipients
                                $mail->setFrom("phpprojectcomp4515@gmail.com", "PHP Project");
                                $mail->addAddress($username);     //Add a recipient
                                //Content
                                $mail->isHTML(true);                                  //Set email format to HTML
                                $mail->Subject = 'Account is locked';
                                // include link to log in again
                                $mail->Body    = 'Your account has been locked, please try again later. <a href="http://localhost:8080/signin.php">Log in</a>';
                                $mail->send();
                                echo 'Message has been sent';
                            } catch (Exception $e) {
                                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                            }
                        }
                        $_SESSION['error'] = 'Wrong password.';
                    }
                }
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
}

else if (isset($_POST["newpassword"])){
    echo "new password" . "<br>";
    $username = $_POST['username'];

    // check if user exists
    $sql = "SELECT * FROM user WHERE username = '$username'";
    $query = $mysqli->query($sql);
    
    if ($query->num_rows == 0) {
        echo "no user found with this email" . "<br>";
        $_SESSION['error'] = 'User does not exists, please sign up.';
        header('Location: login.php');
    } 

    // genmerate new password and email it to the user:
    $newPassword = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("UPDATE user SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", $hashedPassword, $username);
    $stmt->execute();
    $stmt->close();


    echo "new password: " . $newPassword . "<br>";
    echo "hashed password: " . $hashedPassword . "<br>";

    // email the user the new password:

    $to = $username;
    $subject = "Your new password";
    $message = "Your new password is: " . $newPassword;

    try{

    

    $mail = new PHPMailer(true);
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;   

    $mail->isSMTP();
    $mail->SMTPAuth = true;

    $mail->Host = "smtp.gmail.com";
    $mail->SMTPSecure = "ssl";
    $mail->Port = 465;

    $mail->Username = "phpprojectcomp4515@gmail.com";
    $mail->Password = "cyfvzyyukclstryt";

    $mail->setFrom("phpprojectcomp4515@gmail.com", "PHP Project");
    $mail->addAddress($to);

    $mail->isHTML(true);

    $mail->Subject = $subject;
    $mail->Body = $message;

    $mail->send();
        echo "email sent" . "<br>";
    } catch (Exception $e) {
        
        echo "email not sent" . "<br>";
        echo "Mailer Error: " . $mail->ErrorInfo;
    }

}

else if(isset($_POST["createnote"])) {
    // create an activity log in a superglobal and set it to now
    $_SESSION['activity'] = time();
    

    echo "create note" . "<br>";
        // create table if not exists
        $mysqli->query("CREATE TABLE IF NOT EXISTS 
        note(
        id int primary key auto_increment, 
        user varchar(255) not null,
        title varchar(255) not null default 'Untitled',
        content varchar(255) not null
        )");

        $content = $_POST['note'];
        $user = $_SESSION['user'];
        $title = $_POST['title'];

        $sql = "SELECT * FROM user WHERE id = '$user'";
        $query = $mysqli->query($sql);
        $row = $query->fetch_assoc();
        $user = $row['firstname'] . " " . $row['lastname'];

        echo "user: " . $user . "<br>";


        $stmt = $mysqli->prepare("INSERT INTO note (user, content, title) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user, $content, $title);
        $stmt->execute();
        $stmt->close();
        header('Location: index.php');

}






// LOG OUT
else if(isset($_POST["logout"])){
    echo "log out" . "<br>";
    session_destroy();
    header('Location: signin.php');
}



?>