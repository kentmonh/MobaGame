<?php
    ob_start();

    session_start();
    
    require('connect.php');

    // There are input for search form
    if (isset($_POST['search']) && isset($_POST['hero-role']))
    {
        $keyword = $_POST['search'];
        $_SESSION['keyword'] = $keyword;
        $roleId = $_POST['hero-role'];
        $_SESSION['hero-role'] = $roleId;
    }

    $keyword = $_SESSION['keyword'];
    $roleId = $_SESSION['hero-role'];

    // Get number of record per page
    if (isset($_POST['nop']))
    {
        if ($_POST['nop'] == 0)
        {
            $no_of_records_per_page = 3; 
        }
        else $no_of_records_per_page = $_POST['nop'];
        $_SESSION['nop'] = $no_of_records_per_page;
    }

    $no_of_records_per_page = $_SESSION['nop'];

    if ($roleId == 0)
    {
        $query = "SELECT * FROM heroes WHERE name LIKE '%$keyword%'"; 
    }
    else 
    {
        $query = "SELECT * FROM heroes WHERE name LIKE '%$keyword%' AND roleId = $roleId";
    }

        $statement = $db->prepare($query);
        $statement -> execute();

    if (isset($_GET['pageno'])) 
        {
            $pageno = $_GET['pageno'];
        } else 
        {
            $pageno = 1;
        }
    
        $total_heroes = 0;
        while($hero = $statement -> fetch())
        {
            $total_heroes++;
        }
        $total_pages = ceil($total_heroes / $no_of_records_per_page);
    
        $offset = ($pageno-1) * $no_of_records_per_page; 

        if ($roleId == 0)
        {
            $query_pages = "SELECT * FROM heroes WHERE name LIKE '%$keyword%' LIMIT $offset, $no_of_records_per_page"; 
        }
        else 
        {
            $query_pages = "SELECT * FROM heroes WHERE name LIKE '%$keyword%' AND roleId = $roleId LIMIT $offset, $no_of_records_per_page"; 
        }
        $statement_pages = $db->prepare($query_pages);
        $statement_pages -> execute();

    $query_roles = "SELECT * FROM roles ORDER BY name";
    $statement_roles = $db->prepare($query_roles);
    $statement_roles -> execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Search Result</title>
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
            <h2>Result of searching:</h2>
        </div>
    </div>

    <div class="container">
        <div class="justify-content-center h-100">
            <?php
                while($hero = $statement_pages -> fetch()): 
            ?>
                <p><a href="show-heroes.php?id=<?= $hero['id'] ?>"><?= $hero['name'] ?></a></p>
            <?php endwhile ?>   
        </div>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="container">
            <ul class="pagination">
                <li class="page-item"><a class="page-link" href="?pageno=1">First</a></li>
                <li class="page-item">
                    <a class="page-link" href="<?php if($pageno > 1) { echo "?pageno=".($pageno - 1); } ?>">Prev</a>
                </li>
                <?php
                    for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item">
                        <a class="page-link" href="search.php?pageno=<?= $i ?>">Page <?= $i ?></a>
                    </li> 
                <?php endfor; ?>
                <li class="page-item">
                    <a class="page-link" href="<?php if($pageno < $total_pages) {echo "?pageno=".($pageno + 1); } ?>">Next</a>
                </li>
                <li class="page-item"><a class="page-link" href="?pageno=<?php echo $total_pages; ?>">Last</a></li>
            </ul>
        </div>
    <?php endif ?>  

</body>
</html>

