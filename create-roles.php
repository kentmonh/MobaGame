<?php
    ob_start();

    // Admin Page
    // We need to use sessions.
    session_start();
    // If the user is not logged in as admin redirect to the login page...
    if (!isset($_SESSION['loggedin'])) {
        header('Refresh:2; url = login.html');
        echo 'Only User of Admin can acess!';
    }

    require('connect.php');
    
    if ($_POST && !empty($_POST['name'])) 
    {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        //  Build the parameterized SQL query and bind to the above sanitized values.
        $query = "INSERT INTO roles(name) VALUES(:name)";
        $statement = $db -> prepare($query);
        
        //  Bind values to the parameters
        $statement -> bindValue(':name', $name);
        
        //  Execute the INSERT.
        //  execute() will check for possible SQL injection and remove if necessary
        if ($statement -> execute())
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
<html>
<head>
    <title>Create A Role</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <?php include('nav-logedin.php'); ?>

    <div class="container">
        <form method="post" enctype='multipart/form-data'>
            <div class="form-group">
                <label for="name" class="col-sm-2 col-form-label">Name of Role</label>
                <input type="text" class="form-control col-sm-10" id="name" name="name" placeholder="Enter the Role">
            </div>

            <div class="form-group row justify-content-center">
                <button type="submit" class="btn btn-outline-success">Create New Role</button>   
            </div>
        </form>
    </div>
</body>
</html>