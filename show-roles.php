<?php
    ob_start();

    session_start();
    //  Screipt use to show each article base on id
    require('connect.php');
    
    // Build and prepare SQL String with :id placeholder parameter.
    $query = "SELECT * FROM roles WHERE id = :id LIMIT 1";
    $query_heroes = "SELECT * FROM heroes WHERE roleId = :id";

    $statement = $db->prepare($query);
    $statement_heroes = $db -> prepare($query_heroes);
    
    // Sanitize $_GET['id'] to ensure it's a number.
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if ($id) {

        // Bind the :id parameter in the query to the sanitized
        // $id specifying a binding-type of Integer.
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();
        $statement_heroes->bindValue('id', $id, PDO::PARAM_INT);
        $statement_heroes -> execute();
        
        // Fetch the row selected by primary key id.
        $row = $statement->fetch();
    }
    else {
        header("Location: admin.php");
        exit;
    }

    $query_roles = "SELECT * FROM roles ORDER BY name";
    $statement_roles = $db->prepare($query_roles);
    $statement_roles -> execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $row['name'] ?></title>
    <link rel="stylesheet" type="text/css" href="styles/edit-comments.css">
    <link rel="stylesheet" type="text/css" href="styles/create-heroes.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>    
    <?php if (isset($_SESSION['loggedin'])): ?>
        <?php include('nav-logedin.php'); ?>
    <?php else: ?>
        <?php include('nav.php'); ?>
    <?php endif ?>

    <div class="container">
        <div class="row justify-content-center h-100">
            <h2><?= $row['name'] ?> Role</a></h2>
        </div>
    </div>

    <div class="container">
        <div class="justify-content-center h-100">
            <?php
		    while($hero = $statement_heroes -> fetch()): 
	        ?>
                <p><a href="show-heroes.php?id=<?= $hero['id'] ?>"><?= $hero['name'] ?></a></p>
            <?php endwhile ?>
        </div>
    </div>

</body>
</html> 