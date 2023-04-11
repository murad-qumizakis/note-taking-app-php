<?php
// Start the session
session_start();


// if the user has not been active for 10 minutes, log them out
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 600)) {
    // last request was more than 10 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
}

// Check if the user is not logged in
if (!isset($_SESSION['logged_in'])) {
    // Redirect to the register page
    header("Location: signup.php");
    exit();
}

$_SESSION['logged_in'] = true;
// header("Location: index.php");?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <title>Notes</title>
</head>
<body>



    <form method="POST" action="receive.php">
        <p>
            <input style="float: right;" type="submit" value="Log Out" name="logout" id="logout">
        </p>
    </form>

    <h1>Create Notes</h1>
    <form method="POST" action="receive.php">
        <p>
            <label for="title">Title</label>
            <input type="text" name="title" id="title">
        <p>
            <label for="note">Whats on your mind?</label>
            <textarea type="text" name="note" id="note"></textarea>
        </p>
        <p>
            <input type="submit" value="Create Note" style="float: right;" name="createnote" id="createnote">
        </p>
    </form>

    <br>
    <br>
    <hr>

    <h2>Notes:</h2>
<?php


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

// get user
$user = $_SESSION['user'];


// retrieve notes from database
$sql = "SELECT * FROM note";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<h3>" . $row['title'] . "</h3>";
        echo "<p>" . $row['content'] . "</p>";
        echo "<form method='POST' action='index.php'>";
        echo "<input type='hidden' style='float: right;'  name='id' value='" . $row['id'] . "'>";
        echo "<input type='submit'  style='float: right;' value='Update' name='updatenote' id='update'>";
        echo "</form>";
        echo "<form method='POST' action='index.php'>";
        echo "<input type='hidden' style='float: right;'  name='id' value='" . $row['id'] . "'>";
        echo "<input  type='submit' style='float: right;'  value='Delete' name='deletenote' id='delete'>";
        echo "</form>";
        echo "<br>";
        echo "<br>";
        echo "<hr>";
        echo "<br>";
    }
    } else {
    echo "No notes found";
    }

    if (isset($_POST["updatenote"])){
        $_SESSION['activity'] = time();
        // update the note with that id in a text area
        $id = $_POST['id'];
        $sql = "SELECT * FROM note WHERE id = '$id'";
        $result = $mysqli->query($sql);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $note_content = $row['content'];
        } else {
            echo "Error: Note not found.";
            exit;
        }
        
        // render a text area with the content of the note
        echo "<form method='post' action=''>
            <textarea name='note_content'>$note_content</textarea>
            <input type='hidden' name='id' value='$id'>
            <input type='submit' name='save_changes' value='Save Changes'>
        </form>";
    }
    
    if (isset($_POST["save_changes"])) {
        $_SESSION['activity'] = time();
        // update the note with new content
        $id = $_POST['id'];
        $note_content = $_POST['note_content'];
        // $note_content = mysqli_real_escape_string($conn, $_POST['note_content']);
        $sql = "UPDATE note SET content = '$note_content' WHERE id = '$id'";
        $result = $mysqli->query($sql);
        // header("Location: index.php");
        echo "<script>window.location.href = 'index.php';</script>";
        if ($result) {
            echo "Note updated successfully.";
        } else {
            echo "Error updating note: " . mysqli_error($conn);
        }
    }
    
    // where can i find php.ini
    // php.ini is located in /etc/php/7.4/apache2/php.ini
    // php info
    // phpinfo();
?>
<br>
<br>
<h2>Images:</h2>

<form method="POST" enctype="multipart/form-data" action="index.php">
    You can select up to 4 images! <br><br>
  <input type="file" name="image[]" multiple>
  <input type="submit" name="imageupload" value="Upload">
    <br>
    <br>
    <br>
</form>
<?php
// i want the ini to accept larger files
ini_set('post_max_size', '5M');
ini_set('upload_max_filesize', '10M');
// select all images from database
$mysqli->query("CREATE TABLE IF NOT EXISTS 
image(
id int primary key auto_increment, 
name varchar(700) not null
)");


$sql = "SELECT * FROM image";
$result = $mysqli->query($sql);

// display images
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<img src='./upload/" . $row['name'] . "' width='200px' height='200px'>";
        echo "<form method='POST' action='index.php'>";
        echo "<input type='hidden' style='float: right;'  name='id' value='" . $row['id'] . "'>";
        echo "<input type='submit'  style='float: right;' value='Delete' name='deleteimage' id='deleteimage'>";
        echo "</form>";
        echo "<br>";
        echo "<br>";
        echo "<hr>";
        echo "<br>";
    }
    } else {
    echo "No images found";
    echo "<br>";
    echo "<br>";
    echo "<br>";
    }

    // make the delete work
    if(isset($_POST['deleteimage'])) {
        $_SESSION['activity'] = time();
        $id = $_POST['id'];
        $sql = "DELETE FROM image WHERE id = '$id'";
        $result = $mysqli->query($sql);
        if($result) {
            echo "<script>window.location.href = 'index.php';</script>";
        }
    }


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

if (isset($_POST['imageupload'])) {
    $_SESSION['activity'] = time();

    $mysqli->query("CREATE TABLE IF NOT EXISTS 
    image(
    id int primary key auto_increment, 
    name varchar(700) not null
    )");

    $imageCount = count($_FILES['image']['name']);
        // make it so that the user can only upload 4 images
    if($imageCount > 4) {
        echo "You can only upload 4 images";
        exit();
    }


    for($i = 0; $i < $imageCount; $i++) {
        $imageName = $_FILES['image']['name'][$i];
        $imageTmpName = $_FILES['image']['tmp_name'][$i];
        $targetPath = "./upload/" . $imageName;
        if(move_uploaded_file($imageTmpName, $targetPath)) {
            $sql = "INSERT INTO image (name) VALUES ('$imageName')";
            $result = $mysqli->query($sql);
            if($result) {
                header("Location: index.php?msg=ins");
                echo "Image uploaded successfully";
            } else {
                echo "<h1>Image upload failed</h1>";
            }
        }
        
        echo $imageName . "<br>";
        echo $imageTmpName . "<br>";
    }
} else if (isset($_POST["deletenote"])){
    $_SESSION['activity'] = time();
    // delete note with that id
    $id = $_POST['id'];
    $sql = "DELETE FROM note WHERE id = '$id'";
    $query = $mysqli->query($sql);
    // header("Location: index.php");    
    echo "<script>window.location.href = 'index.php';</script>";

} 
?>
<br>
<br>
</body>
</html>


