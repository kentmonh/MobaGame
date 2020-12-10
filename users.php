<?php
    ob_start();
    // Admin Page
    // We need to use sessions.
    session_start();
    // If the user is not logged in as admin redirect to the login page...
    if (!isset($_SESSION['adminloggedin'])) {
        header('Refresh:2; url = login.html');
        echo 'Only Admins can acess!';
    }

    require('connect.php');
    
    // SQL is written as a String.
    $query = "SELECT * FROM users";

    // A PDO::Statement is prepared from the query.
    $statement = $db->prepare($query);

    // Execution on the DB server is delayed until we execute().
    $statement -> execute();

    // Take Roles in roles table
    $query_roles = "SELECT * FROM roles ORDER BY name";
    $statement_roles = $db->prepare($query_roles);
    $statement_roles -> execute();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>List of Registed Users</title>
    <link rel="stylesheet" type="text/css" href="styles/edit-comments.css">
    <link rel="stylesheet" type="text/css" href="styles/create-heroes.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <?php include('nav-logedin.php'); ?>

    <div class="container">
        <div class="row justify-content-center h-100">
        <h2>List of Users</h2>
        </div>
    </div>

    <div class="container">
        <div class="justify-content-center h-100">
            <?php
                while($user = $statement -> fetch()): 
            ?>
                <p>Username: <?= $user['username'] ?></p>
                <p>Email: <?= $user['email'] ?></p>
                <p>
                    <small>
              	        <a href="edit-users.php?id=<?= $user['id'] ?>">edit</a>
                    </small>
                </p>
                <hr>
            <?php endwhile ?>
        </div>
    </div>
            <?php
                while($user = $statement -> fetch()): 
            ?>
                <p>Username: <?= $user['username'] ?></p>
                <p>Email: <?= $user['email'] ?></p>
                <p>
                    <small>
              	        <a href="edit-users.php?id=<?= $user['id'] ?>">edit</a>
                    </small>
                </p>
                <hr>
            <?php endwhile ?>
		</div>
        
        <div class="justify-content-center h-100">
            <form action="register.php">
                <div class="form-group row justify-content-center">
                    <button type="submit" class="btn btn-outline-success">Add New User</button>   
                </div>
            </form>
        </div>
	</body>
</html>