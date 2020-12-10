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
    require_once('php_image_magician.php');

    // Get id
    if (isset($_GET['id'])) 
    {
        // Retrieve quote to be edited, if id GET parameter is in URL.
        // Sanitize the id. Like above but this time from INPUT_GET.
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if($id)
        {
            // Build the parametrized SQL query using the filtered id.
            $query = "SELECT * FROM heroes WHERE id = :id LIMIT 1";
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
        $query = "DELETE FROM heroes WHERE id = :id LIMIT 1";
        $statement = $db->prepare($query);
    
        if ($id)
        {
            //  Sanitize $_GET['id'] to ensure it's a number.
            $statement->bindValue('id', $id, PDO::PARAM_INT);
            $statement->execute();

            header("Location: admin.php");
            exit;
        }
        else 
        {
            header("Location: admin.php");
            exit; 
        }
    } 
    
    //  UPDATE hero if name and id are present in POST.
    else if ( isset($_POST['update']) && isset($_POST['name']) && isset($_POST['id']) ) 
    {
        // Delete image
        if (isset($_POST['delete_image']))
        {
            $query_delete_image = "DELETE FROM images WHERE fileName = :fileName LIMIT 1";
            $statement_delete_image = $db->prepare($query_delete_image);
            $statement_delete_image -> bindValue(':fileName', $row['image']);
            $statement_delete_image ->execute();

            $query_delete_fileName = "UPDATE heroes SET image = :image WHERE id = :id";
            $statement_delete_fileName = $db->prepare($query_delete_fileName);
            $statement_delete_fileName -> bindValue(':image', "");
            $statement_delete_fileName -> bindValue(':id', $id, PDO::PARAM_INT);
            $statement_delete_fileName -> execute();
        }
        

        // Image upload
        if ($row['image'] == NULL)
        {
            function file_upload_path($original_filename, $upload_subfolder_name = 'uploads') 
            {
                $current_folder = dirname(__FILE__);
                $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
                return join(DIRECTORY_SEPARATOR, $path_segments);
            }
        
            // image-ness filtering
            function file_is_an_image($temporary_path, $new_path) 
            {
                $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
                $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];
                
                $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
                $actual_mime_type        = getimagesize($temporary_path)['mime'];
                    
                $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
                $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);
                    
                return $file_extension_is_valid && $mime_type_is_valid;
            }
                
            $image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);
                    
            if ($image_upload_detected) 
            {        
                $image_filename       = $_FILES['image']['name'];
                $temporary_image_path = $_FILES['image']['tmp_name'];
                $new_image_path       = file_upload_path($image_filename);

                // Deal with duplicated file name
                $name = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                while (is_file('uploads/'.$image_filename))
                {
                    $increment++;
                    $image_filename = $name . '-' . $increment . '.' . $extension;
                    $new_image_path = file_upload_path($image_filename);
                }

                if (file_is_an_image($temporary_image_path, $new_image_path)) 
                {
                    move_uploaded_file($temporary_image_path, $new_image_path);

                    // Open image
                    $magicianObj = new imageLib($new_image_path);

                    // Resize
                    $magicianObj -> resizeImage(300, 200, 'crop');

                    // Save
                    $magicianObj -> saveImage($new_image_path);
                }

                // Add image to database
                $query_image = "INSERT INTO images(fileName) 
                    VALUES(:fileName)";
                $statement_image = $db -> prepare($query_image);
                $statement_image -> bindValue(':fileName', $image_filename);
                $statement_image -> execute();
            }
        }
        else
        {
            $image_filename = $row['image'];
        }
        
        // Sanitize user input to escape HTML entities and filter out dangerous characters.
        if(isset($_POST["hero-role"]))
        {
            $roleId = $_POST["hero-role"];
        }
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $skills = $_POST['skills'];
        $str = filter_input(INPUT_POST, 'str', FILTER_VALIDATE_FLOAT);
        $agi = filter_input(INPUT_POST, 'agi', FILTER_VALIDATE_FLOAT);
        $intel = filter_input(INPUT_POST, 'intel', FILTER_VALIDATE_FLOAT);
        $health = filter_input(INPUT_POST, 'health', FILTER_VALIDATE_INT);
        $mana = filter_input(INPUT_POST, 'mana', FILTER_VALIDATE_INT);
        $damage = filter_input(INPUT_POST, 'damage', FILTER_VALIDATE_INT);
        $image = $image_filename;
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

        if ($name != "")
        {
            // Build the parameterized SQL query and bind to the above sanitized values.
            $query     = "UPDATE heroes SET roleId = :roleId, name = :name, skills = :skills, str = :str, agi = :agi, intel = :intel, health = :health, mana = :mana, damage =:damage, image = :image WHERE id = :id";
            $statement = $db->prepare($query);
            $statement -> bindValue(':roleId', $roleId);
            $statement -> bindValue(':name', $name);
            $statement -> bindValue(':skills', $skills);
            $statement -> bindValue(':str', $str);
            $statement -> bindValue(':agi', $agi);
            $statement -> bindValue(':intel', $intel);
            $statement -> bindValue(':health', $health);
            $statement -> bindValue(':mana', $mana);
            $statement -> bindValue(':damage', $damage);
            $statement -> bindValue(':image', $image);
            $statement -> bindValue(':id', $id, PDO::PARAM_INT);
            
            // Execute the INSERT.
            $statement->execute();
            
            // Redirect after update.
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
    <link rel="stylesheet" type="text/css" href="styles/edit-comments.css">
    <link rel="stylesheet" type="text/css" href="styles/create-heroes.css">
    <script src="ckeditor/ckeditor.js" type="text/javascript"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <?php include('nav-logedin.php'); ?>
    
    <div class="container">
        <div class="justify-content-center h-100">
            <form method="post" enctype="multipart/form-data">
                <legend>Edit Hero <?= $row['name'] ?></legend>

                <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Hero Name</label>
                    <input type="text" class="form-control col-sm-10" id="name" name="name" value="<?= $row['name'] ?>"/>
                </div>

                <div class="form-group row">
                    <label for="skills" class="col-sm-2 col-form-label">Skills Description</label>
                    <textarea class="ckeditor form-control col-sm-10" id="skills" name="skills"><?= $row['skills'] ?></textarea>       
                </div>

                <div class="form-group row">
                    <label for="str" class="col-sm-2 col-form-label">Strength</label>
                    <input type="number" step="0.01" class="form-control col-sm-10" id="str" name="str" value="<?= $row['str'] ?>"/>    
                </div>

                <div class="form-group row">
                    <label for="agi" class="col-sm-2 col-form-label">Agility</label>
                    <input type="number" step="0.01" class="form-control col-sm-10" id="agi" name="agi" value="<?= $row['agi'] ?>"/>
                </div>

                <div class="form-group row">
                    <label for="intel" class="col-sm-2 col-form-label">Intelligence</label>
                    <input type="number" step="0.01" class="form-control col-sm-10" id="intel" name="intel" value="<?= $row['intel'] ?>"/>
                </div>

                <div class="form-group row">
                    <label for="health" class="col-sm-2 col-form-label">Health</label>
                    <input type="number" class="form-control col-sm-10" id="health" name="health" value="<?= $row['health'] ?>"/>
                </div>

                <div class="form-group row">
                    <label for="mana" class="col-sm-2 col-form-label">Mana</label>
                    <input type="number" class="form-control col-sm-10" id="mana" name="mana" value="<?= $row['mana'] ?>"/>
                </div>

                <div class="form-group row">
                    <label for="damage" class="col-sm-2 col-form-label">Damage</label>
                    <input type="number" class="form-control col-sm-10" id="damage" name="damage" value="<?= $row['damage'] ?>"/> 
                </div> 

                <div class="form-group row">
                    <label for="hero-role" class="col-sm-2 col-form-label">The Role of Hero</label>
                    <select id="hero-role" name="hero-role" class="form-control col-sm-10">
                        <?php
                            while($role = $statement_roles -> fetch()): 
                        ?>
                            <?php if ($row['roleId'] == $role['id']): ?>
                                <option id=<?= $role['id'] ?> value=<?= $role['id'] ?> selected ><?= $role['name'] ?></option>
                            <?php else: ?>   
                                <option id=<?= $role['id'] ?> value=<?= $role['id'] ?> ><?= $role['name'] ?></option>
                            <?php endif ?>   
                        <?php endwhile ?>
                    </select>
                </div>

                <?php if (isset($row['image']) && ($row['image'] != "")): ?>
                    <div class="row justify-content-center h-100">
                        <img src="uploads/<?= $row['image'] ?>" alt="<?= $row['name'] ?>-image">
                    </div>
                    <div class="row justify-content-center h-100">
                        <label><input type="checkbox" name="delete_image" value="delete_image"> Delete Image </label>
                    </div>
                <?php else: ?>
                    <label for="image">Image Upload:</label>
                    <input type="file" name="image" id="image">
                <?php endif ?>

                <div class="form-group row justify-content-center">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <div class="buttons">
                        <button type="submit" class="btn btn-outline-success" name="update">Update Hero</button>
                    </div>
                    <div class="buttons">
                        <button type="submit" class="btn btn-outline-success" name="delete">Delete Hero</button>   
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

