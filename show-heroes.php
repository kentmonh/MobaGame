<?php
    ob_start();

    session_start();
    //  Screipt use to show each article base on id

    require('connect.php');
    
    // Build and prepare SQL String with :id placeholder parameter.
    $query = "SELECT * FROM heroes WHERE id = :id LIMIT 1";
    
    // Appear comment
    if (isset($_SESSION['adminloggedin']))
    {
        $query_comments = "SELECT * FROM comments WHERE heroId = :id ORDER BY time DESC";
    }
    else 
    {
        $query_comments = "SELECT * FROM comments WHERE heroId = :id AND isHide = 0 ORDER BY time DESC";
    }

    $statement = $db->prepare($query);
    $statement_comments = $db->prepare($query_comments);

    
    // Sanitize $_GET['id'] to ensure it's a number.
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if ($id) 
    {
        // Bind the :id parameter in the query to the sanitized
        // $id specifying a binding-type of Integer.
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();
        $statement_comments -> bindValue('id', $id, PDO::PARAM_INT);
        $statement_comments -> execute();
        
        // Fetch the row selected by primary key id.
        $row = $statement->fetch();
    }
    else {
        header("Location: index.php");
        exit;
    }

    // For create comment
    if ($_POST && !empty($_POST['content']))
    {
        $_SESSION['content'] = $_POST['content'];
        if ($_POST['captcha'] == $_SESSION['captcha'])
        {
            $heroId = $row['id'];
            if (!isset($_SESSION['loggedin']))
            {
                $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
            else
            {
                $name = $_SESSION['username'];
            }
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
            //  Build the parameterized SQL query and bind to the above sanitized values.
            $query = "INSERT INTO comments(heroId, name, content) VALUES(:heroId, :name, :content)";
            $statement = $db -> prepare($query);
    
            //  Bind values to the parameters
            $statement -> bindValue(':heroId', $heroId);
            $statement -> bindValue(':name', $name);
            $statement -> bindValue(':content', $content);
    
            if ($statement -> execute())
            {
                header("Refresh:0");
                exit; 
            }
        }
        else
        {
            echo '<p>You entered an incorrect Captcha.</p>';
        }
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
            <h1><?= $row['name'] ?></a></h1>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center h-100">
            <?php if (isset($row['image']) && ($row['image'] != "")): ?>
                <img src="uploads/<?= $row['image'] ?>" alt="<?= $row['name'] ?>-image">
            <?php endif ?>
        </div>
    </div>

    <div class="container">
        <div class="justify-content-center h-100">
            <p><?= $row['skills'] ?></p>
        </div>
    </div>

    <div class="container">
        <div class="justify-content-center h-100">
            <p>Strength: <?= $row['str'] ?></p>
            <p>Agility: <?= $row['agi'] ?></p>
            <p>Intelligence: <?= $row['intel'] ?></p>
            <p>Health: <?= $row['health'] ?></p>
            <p>Mana: <?= $row['mana'] ?></p>
            <p>Damage: <?= $row['damage'] ?></p>
        </div>
    </div>

    <div class="container">
        <form method="post" enctype='multipart/form-data'>
            <?php if (!isset($_SESSION['loggedin'])): ?>
                <div class="form-group">
                    <label for="name" class="col-sm-2 col-form-label">Name</label>
                    <input type="text" class="form-control col-sm-10" id="name" name="name" placeholder="Enter Your Name">
                </div>
            <?php endif ?>

            <div class="form-group">
                <label for="content" class="col-sm-12 col-form-label">Comment</label>
                <textarea name="content" class="form-control col-sm-12" id="content" name="content"><?php if (!empty($_POST['content'])) echo strip_tags($_POST['content']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="captcha" class="col-sm-12 col-form-label">Please Enter The captcha</label>
                <img src="captcha.php" alt="Captcha">
                <br>
                <input type="text" class="form-control col-sm-4" id="captcha" name="captcha" placeholder="Enter Captcha Here">
            </div>

            <div class="form-group row justify-content-center">
                <button type="submit" class="btn btn-outline-success">Post Comment</button>   
            </div>
        </form>
    </div>

    <div class="container">
        <?php
		    while($comment = $statement_comments -> fetch()): 
	    ?>
            <p><?= $comment['name'] ?></p>
            <p><?= $comment['content'] ?></p>
            <?php if (isset($_SESSION['adminloggedin'])): ?>
                <p>
                    <small>
              	            <a href="edit-comments.php?id=<?= $comment['id'] ?>">edit comment</a>
                    </small>
                </p>
            <?php endif ?>
            <hr>
        <?php endwhile ?>
    </div>
</div>
</body>
</html> 