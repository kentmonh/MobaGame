<!--
	Sidebar Menu
-->
<?php  
    // SQL is written as a String.
    $query_roles = "SELECT * FROM roles ORDER BY name";

    // A PDO::Statement is prepared from the query.   
    $statement_roles = $db->prepare($query_roles);

    // Execution on the DB server is delayed until we execute().
    $statement_roles -> execute();
?>

<ul class="list-group">
    <?php
        while($role = $statement_roles -> fetch()): 
    ?>
    <li class="list-group-item">
        <div class="row">
            <div class="col-md-8">
                <a href="show-roles.php?id=<?= $role['id'] ?>"><?= $role['name'] ?></a>
            </div>
            <?php if (isset($_SESSION['adminloggedin'])): ?>
                <div class="col-md-4">
                    <a href="edit-roles.php?id=<?= $role['id'] ?>" class="btn btn-primary">Edit</a>
                </div>
            <?php endif ?>
            
        </div>
        <ul class="list-group list-group-flush">  
            <?php
                $query_heroes = "SELECT * FROM heroes WHERE roleId = {$role['id']}"; 
                $statement_heroes = $db->prepare($query_heroes);
                $statement_heroes -> execute(); 
            ?>

            <?php
                while($hero = $statement_heroes -> fetch()): 
            ?>            
                <li class="list-group-item"><a href="show-heroes.php?id=<?= $hero['id'] ?>"><?= $hero['name'] ?></a></li>
            <?php endwhile ?>
        </ul> 
    </li>
    <?php endwhile ?>
</ul>