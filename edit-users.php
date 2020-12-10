<?php
    ob_start();

    // Admin Page
    // We need to use sessions.
    session_start();
    // If the user is not logged in as admin redirect to the login page...
    if (!isset($_SESSION['loggedin'])) {
        header('Refresh:2; url = login.html');
        echo 'Only User or Admin can acess!';
    }

    require('connect.php');

    // Get id
    if (isset($_GET['id'])) 
    {
        // Retrieve quote to be edited, if id GET parameter is in URL.
        // Sanitize the id. Like above but this time from INPUT_GET.
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if($id)
        {
            // Build the parametrized SQL query using the filtered id.
            $query = "SELECT * FROM users WHERE id = :id LIMIT 1";
            $statement = $db->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            
            // Execute the SELECT and fetch the single row returned.
            $statement->execute();
            $row = $statement->fetch();
        }
        else
        {
            header("Location: admin.php");
            exit; 
        }
    }

    //  User click DELETE
    if (isset($_POST['delete']) && filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) 
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        // Build and prepare SQL String with :id placeholder parameter.
        $query = "DELETE FROM users WHERE id = :id LIMIT 1";
        $statement = $db->prepare($query);
    
        if ($id)
        {
            //  Sanitize $_GET['id'] to ensure it's a number.
            $statement->bindValue('id', $id, PDO::PARAM_INT);
            $statement->execute();

            header("Location: users.php");
            exit;
        }
        else 
        {
            header("Location: users.php");
            exit; 
        }
    } 

    //  UPDATE 
    if ( isset($_POST['update']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['id']) ) 
    {
        // Sanitize user input to escape HTML entities and filter out dangerous characters.
        $username = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

        if ($username != "" && $email != "")
        {
            // Build the parameterized SQL query and bind to the above sanitized values.
            $query = "UPDATE users SET username = :username, email = :email WHERE id = :id";
            $statement = $db->prepare($query);
            $statement -> bindValue(':username', $username);
            $statement -> bindValue(':email', $email);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            
            // Execute the INSERT.
            $statement->execute();
            
            // Redirect after update.
            header("Location: users.php");
            exit;
            }
        else 
        {
            header("Location: users.php");
            exit;
        }
    }

    // Take Roles in roles table
    $query_roles = "SELECT * FROM roles ORDER BY name";
    $statement_roles = $db->prepare($query_roles);
    $statement_roles -> execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit <?= $row['username'] ?></title>
    <link rel="stylesheet" type="text/css" href="styles/edit-comments.css">
    <link rel="stylesheet" type="text/css" href="styles/create-heroes.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <?php include('nav-logedin.php'); ?>
    <div class="container">
        <div class="justify-content-center h-100">
            <form method="post" enctype="multipart/form-data">
                <legend>Edit User <?= $row['username'] ?></legend>

                <div class="form-group row">
                    <label for="username" class="col-sm-2 col-form-label">Username</label>
                    <input type="text" class="form-control col-sm-10" id="username" name="username" value="<?= $row['username'] ?>"/>
                </div>

                <div class="form-group row">
                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                    <input type="text" class="form-control col-sm-10" id="email" name="email" value="<?= $row['email'] ?>"/>
                </div>

                <div class="form-group row justify-content-center">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <div class="buttons">
                        <button type="submit" class="btn btn-outline-success" name="update">Update User</button>
                    </div>
                    <div class="buttons">
                        <button type="submit" class="btn btn-outline-success" name="delete">Delete User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

