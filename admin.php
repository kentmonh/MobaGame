<?php
    ob_start();

    // Admin Page
    // We need to use sessions.
    session_start();
    // If the user is not logged in as admin redirect to the login page...
    if (!isset($_SESSION['loggedin'])) {
        header('Refresh:2; url = login.html');
        echo 'Only Admins or Users can acess!';
    }

    require('connect.php');
    
    // SQL is written as a String.
    $query_heroes_name_order = "SELECT * FROM heroes ORDER BY name";
    $query_heroes_health_order = "SELECT * FROM heroes ORDER BY health DESC";
    $query_heroes_mana_order = "SELECT * FROM heroes ORDER BY mana DESC";
    $query_roles = "SELECT * FROM roles ORDER BY name";

    // A PDO::Statement is prepared from the query.
    $statement_heroes_name_order = $db->prepare($query_heroes_name_order);
    $statement_heroes_health_order = $db->prepare($query_heroes_health_order);
    $statement_heroes_mana_order = $db->prepare($query_heroes_mana_order);
    $statement_roles = $db->prepare($query_roles);

    // Execution on the DB server is delayed until we execute().
    $statement_heroes_name_order -> execute();
    $statement_heroes_health_order -> execute(); 
    $statement_heroes_mana_order -> execute(); 
    $statement_roles -> execute();
?>

<!DOCTYPE html>
<html>
<head>
    <title>MOBA Project</title>
    <link rel="stylesheet" type="text/css" href="styles/index.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
    
<body>
    <?php include('nav-logedin.php'); ?>
    
    <div class="d-flex justify-content-center">
        <div class="create-btn"> 
            <form action="create-heroes.php">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit" value="Create Hero">Create Hero</button>
            </form>
        </div>
        
        <div class="create-btn"> 
            <form action="create-roles.php">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit" value="Create Role">Create Role</button>
            </form>
        </div>
    </div>

    <form class="form-inline my-2 my-lg-0 sorting-bar">
        <label for="sorting">Sort Heroes By &nbsp; </label>
        <select class="form-control mr-sm-2 id="sorting" name="sorting" onchange="this.form.submit()">
            <option id="" value="">--Select--</option>
            <option id="name" value="name">Name</option>
            <option id="health" value="health">Health</option>
            <option id="mana" value="mana">Mana</option>
        </select>
    </form>

    <div class="row">
        <div class="col-md-9">
            <?php
                if(isset($_GET["sorting"]))
                {
                    $sorting = $_GET["sorting"];
                }
            ?>

            <?php if (!isset($sorting )): ?>
                <?php
                    while($hero = $statement_heroes_name_order -> fetch()): 
                ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6"> 
                                    <h3 class="card-title"><a href="show-heroes.php?id=<?= $hero['id'] ?>"><?= $hero['name'] ?></a></h3>
                                    <p class="card-text">
                                        Strength: <?= $hero['str'] ?> <br>
                                        Agility: <?= $hero['agi'] ?> <br>
                                        Intelligence: <?= $hero['intel'] ?>
                                    </p>
                                    <a href="edit-heroes.php?id=<?= $hero['id'] ?>" class="btn btn-primary">Edit Heroes</a>
                                </div>
                                <div class="col-md-6">
                                    <?php if (isset($hero['image']) && ($hero['image'] != "")): ?>
                                        <img src="uploads/<?= $hero['image'] ?>" alt="<?= $hero['name'] ?>-image">
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile ?>
            
            <?php elseif ($sorting == "name" || !isset($sorting )): ?>
                <?php
                    while($hero = $statement_heroes_name_order -> fetch()): 
                ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6"> 
                                    <h3 class="card-title"><a href="show-heroes.php?id=<?= $hero['id'] ?>"><?= $hero['name'] ?></a></h3>
                                    <p class="card-text">
                                        Strength: <?= $hero['str'] ?> <br>
                                        Agility: <?= $hero['agi'] ?> <br>
                                        Intelligence: <?= $hero['intel'] ?>
                                    </p>
                                    <a href="edit-heroes.php?id=<?= $hero['id'] ?>" class="btn btn-primary">Edit Heroes</a>
                                </div>
                                <div class="col-md-6">
                                    <?php if (isset($hero['image']) && ($hero['image'] != "")): ?>
                                        <img src="uploads/<?= $hero['image'] ?>" alt="<?= $hero['name'] ?>-image">
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile ?>

            <?php elseif ($sorting == "health"): ?>
                <?php
                    while($hero = $statement_heroes_health_order -> fetch()): 
                ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6"> 
                                    <h3 class="card-title"><a href="show-heroes.php?id=<?= $hero['id'] ?>"><?= $hero['name'] ?></a></h3>
                                    <p class="card-text">
                                        Strength: <?= $hero['str'] ?> <br>
                                        Agility: <?= $hero['agi'] ?> <br>
                                        Intelligence: <?= $hero['intel'] ?>
                                    </p>
                                    <a href="edit-heroes.php?id=<?= $hero['id'] ?>" class="btn btn-primary">Edit Heroes</a>
                                </div>
                                <div class="col-md-6">
                                    <?php if (isset($hero['image']) && ($hero['image'] != "")): ?>
                                        <img src="uploads/<?= $hero['image'] ?>" alt="<?= $hero['name'] ?>-image">
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile ?>

            <?php elseif ($sorting == "mana"): ?>
                <?php
                    while($hero = $statement_heroes_mana_order -> fetch()): 
                ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6"> 
                                    <h3 class="card-title"><a href="show-heroes.php?id=<?= $hero['id'] ?>"><?= $hero['name'] ?></a></h3>
                                    <p class="card-text">
                                        Strength: <?= $hero['str'] ?> <br>
                                        Agility: <?= $hero['agi'] ?> <br>
                                        Intelligence: <?= $hero['intel'] ?>
                                    </p>
                                    <a href="edit-heroes.php?id=<?= $hero['id'] ?>" class="btn btn-primary">Edit Heroes</a>
                                </div>
                                <div class="col-md-6">
                                    <?php if (isset($hero['image']) && ($hero['image'] != "")): ?>
                                        <img src="uploads/<?= $hero['image'] ?>" alt="<?= $hero['name'] ?>-image">
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile ?>
            <?php endif ?>   
        </div> 

        <div class="col-md-3">
            <?php include('sidebar.php'); ?>                  
        </div>
    </div>
</body>
</html>