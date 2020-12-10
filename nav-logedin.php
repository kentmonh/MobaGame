<!--
	The nav for user
-->

<nav class="navbar navbar-expand-sm">
    <ul class="navbar-nav mr-auto">
        <li class="nav-item">
            <a class="nav-link" href="admin.php">Home</a>
        </li>
        <?php
            if(isset($_SESSION['adminloggedin'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="users.php">Users Manager</a>
                </li>
        <?php endif ?>
        <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
        </li>
    </ul>

    <form class="form-inline my-2 my-lg-0" method="post" action="search.php" value="search">
        <input class="form-control mr-sm-2" type="text" placeholder="Search.." name="search">

        <select class="form-control mr-sm-2 id="hero-role" name="hero-role">
            <option id="0" value="0">All Roles</option>
            <?php
                while($role = $statement_roles -> fetch()): 
            ?>
                <option id=<?= $role['id'] ?> value=<?= $role['id'] ?> ><?= $role['name'] ?></option>
            <?php endwhile ?>
        </select>

        <label for="nop">Number of Record each Page &nbsp; </label>
        <input class="form-control mr-sm-2" type="number" id="nop" name="nop" placeholder="Default is 3">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit" value="search">Search</button>
    </form>
</nav>