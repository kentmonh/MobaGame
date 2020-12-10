<?php
    ob_start();

    // Admin Page
    // We need to use sessions.
    session_start();
    // If the user is not logged in as admin redirect to the login page...
    if (!isset($_SESSION['adminloggedin'])) {
        header('Refresh:2; url = login.html');
        echo 'Only Admin can acess!';
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
            $query = "SELECT * FROM comments WHERE id = :id LIMIT 1";
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
        $query = "DELETE FROM comments WHERE id = :id LIMIT 1";
        $statement = $db->prepare($query);
    
        if ($id)
        {
            //  Sanitize $_GET['id'] to ensure it's a number.
            $statement->bindValue('id', $id, PDO::PARAM_INT);
            $statement->execute();

            // Redirect after delete.
            header("Location: admin.php");
            exit;
        }
        else 
        {
            header("Location: admin.php");
            exit;
        }
    } 

    //  UPDATE 
    if ( isset($_POST['update']) && isset($_POST['name']) && isset($_POST['content']) && isset($_POST['id']) ) 
    {
        // Sanitize user input to escape HTML entities and filter out dangerous characters.
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if ($name != "" && $content != "")
        {
            // Build the parameterized SQL query and bind to the above sanitized values.
            $query = "UPDATE users SET name = :name, content = :content WHERE id = :id";
            $statement = $db->prepare($query);
            $statement -> bindValue(':name', $name);
            $statement -> bindValue(':content', $content);
            $statement -> bindValue(':id', $id, PDO::PARAM_INT);
            
            // Execute the INSERT.
            $statement->execute();
            
            // Redirect after update.
            header("Location: admin.php");
            exit;
            }
        else 
        {
            header("Location: admin.php");
            exit;
        }
    }

    //  User click Hide
    if (isset($_POST['hide']) && filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) 
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        // Build and prepare SQL String with :id placeholder parameter.
        $query = "UPDATE comments SET isHide = 1 WHERE id = :id LIMIT 1";
        $statement = $db->prepare($query);
    
        if ($id)
        {
            //  Sanitize $_GET['id'] to ensure it's a number.
            $statement->bindValue('id', $id, PDO::PARAM_INT);
            $statement->execute();

            // Redirect after delete.
            header("Location: admin.php");
            exit;
        }
        else 
        {
            header("Location: admin.php");
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
    <title>Edit Comment</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles/edit-comments.css">
    <link rel="stylesheet" type="text/css" href="styles/create-heroes.css">
</head>
<body>
    <?php include('nav-logedin.php'); ?>
    
    <div class="container">
        <form method="post" enctype='multipart/form-data'>
            <div class="form-group">
                <label for="name" class="col-sm-2 col-form-label">Username</label>
                <input type="text" class="form-control col-sm-10" id="name" name="name" placeholder="<?= $row['name'] ?>">
            </div>

            <div class="form-group">
                <label for="content" class="col-sm-2 col-form-label">Comment</label>
                <textarea class="form-control col-sm-10" id="content" name="content" rows="4"><?= $row['content'] ?></textarea>
            </div>

            <div class="form-group row justify-content-center">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <div class="buttons">
                    <button type="submit" class="btn btn-outline-success" name="update">Update</button>
                </div>
                <div class="buttons">
                    <button type="submit" class="btn btn-outline-success" name="hide">Hide</button>
                </div>
                <div class="buttons">
                    <button type="submit" class="btn btn-outline-success" name="delete">Delete</button>   
                </div>
            </div>
        </form>
    </div>
</body>
</html>

