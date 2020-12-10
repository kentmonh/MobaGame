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
            $query = "SELECT * FROM roles WHERE id = :id LIMIT 1";
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

    
    //  UPDATE 
    if ( isset($_POST['update']) && isset($_POST['name']) && isset($_POST['id']) ) 
    {
        // Sanitize user input to escape HTML entities and filter out dangerous characters.
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

        if ($name != "")
        {
            // Build the parameterized SQL query and bind to the above sanitized values.
            $query     = "UPDATE roles SET name = :name WHERE id = :id";
            $statement = $db->prepare($query);
            $statement -> bindValue(':name', $name);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            
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

    // Take Roles in roles table
    $query_roles = "SELECT * FROM roles ORDER BY name";
    $statement_roles = $db->prepare($query_roles);
    $statement_roles -> execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit <?= $row['name'] ?></title>
    <link rel="stylesheet" type="text/css" href="styles/create-heroes.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <?php include('nav-logedin.php'); ?>

    <div class="container">
        <div class="justify-content-center h-100">
            <form method="post" enctype="multipart/form-data">
                <legend>Edit Role <?= $row['name'] ?></legend>

                <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Role Name</label>
                    <input type="text" class="form-control col-sm-10" id="name" name="name" value="<?= $row['name'] ?>"/>
                </div>

                <div class="form-group row justify-content-center">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <div class="buttons">
                        <button type="submit" class="btn btn-outline-success" name="update">Update Hero</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

