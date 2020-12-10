<?php
    ob_start();

    session_start();
    require('connect.php');

    if ($_POST['login'] && isset($_POST['username'], $_POST['password']))
    {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password_input = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      
        $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
        $statement = $db->prepare($query);
        
        // Execute the SELECT and fetch the single row returned.
        $statement->execute();
        $row = $statement->fetch();

        if ($row) 
        {
            // Account exists, now we verify the password.
            // Note: remember to use password_hash in your registration file to store the hashed passwords.
            if (password_verify($_POST['password'], $row['password'])) 
            {
                // Verification success! User has loggedin!
                // Create sessions so we know the user is logged in, they basically act like cookies but remember the data on the server.
                session_regenerate_id();
                $_SESSION['username'] = $row['username'];
                $_SESSION['id'] = $row['id']; 
                $_SESSION['loggedin'] = TRUE;
                if ($row['isAdmin'] == 0)
                {
                    header('Refresh:2; url = admin.php');
                    echo 'User login successful!';
                }
                else 
                {
                    $_SESSION['adminloggedin'] = TRUE;
                    header('Refresh:2; url = admin.php');
                    echo 'Admin login successful!';
                }
            }
            else
            {
                header('Refresh:2; url = login.html');
                echo 'Incorrect password for user!';
            }
        } 
        else 
        {
            header('Refresh:2; url = login.html');
            echo 'Incorrect username!';
        }
    }
?>