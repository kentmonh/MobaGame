<?php
    ob_start();
    // Admin Page
    // We need to use sessions.
    session_start();
    // If the user is not logged in as admin redirect to the login page...
    if (!isset($_SESSION['loggedin'])) {
        header('Refresh:2; url = login.html');
        echo 'Only Users or Admins can acess!';
    }

    require('connect.php');
    require_once('php_image_magician.php');
    
    if ($_POST && !empty($_POST['name'])) 
    {
        // Image upload
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
        
        //  Build the parameterized SQL query and bind to the above sanitized values.
        $query = "INSERT INTO heroes(roleId, name, skills, str, agi, intel, health, mana, damage, image) 
            VALUES(:roleId, :name, :skills, :str, :agi, :intel, :health, :mana, :damage, :image)";
        $statement = $db -> prepare($query);
        
        //  Bind values to the parameters
        $statement -> bindValue(':roleId', $roleId);
        $statement -> bindValue(':name', $name);
        $statement -> bindValue(':skills', $skills);
        $statement -> bindValue(':str', $str);
        $statement -> bindValue(':agi', $agi);
        $statement -> bindValue(':intel', $intel);
        $statement -> bindValue(':health', $health);
        $statement -> bindValue(':mana', $mana);
        $statement -> bindValue(':damage', $damage);
        $statement -> bindValue(':image', $image_filename);
        
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
    <title>Create Hero</title>
    <link rel="stylesheet" type="text/css" href="styles/create-heroes.css" />
    <script src="ckeditor/ckeditor.js" type="text/javascript"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <?php include('nav-logedin.php'); ?>

    <div class="container">
        <div class="justify-content-center h-100">
            <form method="post" enctype='multipart/form-data'>
                <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Hero Name</label>
                    <input type="text" class="form-control col-sm-10" id="name" name="name" placeholder="Enter the Hero Name"/>
                </div>

                <div class="form-group row">
                    <label for="skills" class="col-sm-2 col-form-label">Skills Description</label>
                    <textarea class="ckeditor form-control col-sm-10" id="skills" name="skills"></textarea>       
                </div>

                <div class="form-group row">
                    <label for="str" class="col-sm-2 col-form-label">Strength</label>
                    <input type="number" step="0.01" class="form-control col-sm-10" id="str" name="str" placeholder="Enter the Strength of Hero. Allow 2 decimal places"/>    
                </div>

                <div class="form-group row">
                    <label for="agi" class="col-sm-2 col-form-label">Agility</label>
                    <input type="number" step="0.01" class="form-control col-sm-10" id="agi" name="agi" placeholder="Enter the Agility of Hero. Allow 2 decimal places"/>    
                </div>

                <div class="form-group row">
                    <label for="intel" class="col-sm-2 col-form-label">Intelligence</label>
                    <input type="number" step="0.01" class="form-control col-sm-10" id="intel" name="intel" placeholder="Enter the Intelligence of Hero. Allow 2 decimal places"/>    
                </div>

                <div class="form-group row">
                    <label for="health" class="col-sm-2 col-form-label">Health</label>
                    <input type="number" class="form-control col-sm-10" id="health" name="health" placeholder="Enter the Health of Hero"/>    
                </div>

                <div class="form-group row">
                    <label for="mana" class="col-sm-2 col-form-label">Mana</label>
                    <input type="number" class="form-control col-sm-10" id="mana" name="mana" placeholder="Enter the Mana of Hero"/>    
                </div>

                <div class="form-group row">
                    <label for="damage" class="col-sm-2 col-form-label">Damage</label>
                    <input type="number" class="form-control col-sm-10" id="damage" name="damage" placeholder="Enter the Damage of Hero"/>    
                </div> 

                <div class="form-group row">
                    <label for="hero-role" class="col-sm-2 col-form-label">The Role of Hero</label>
                    <select id="hero-role" name="hero-role" class="form-control col-sm-10">
                        <option>--Select--</option>
                        <?php
                            while($role = $statement_roles -> fetch()): 
                        ?>
                            <option id=<?= $role['id'] ?> value=<?= $role['id'] ?> ><?= $role['name'] ?></option>
                        <?php endwhile ?>
                    </select>
                </div>

                <div class="form-group row">
                    <label for="image" class="col-sm-2 col-form-label">Image Upload</label>
                    <input type="file" class="form-control col-sm-10" name="image" id="image">
                </div>
                
                <div class="form-group row justify-content-center">
                    <button type="submit" class="btn btn-outline-success">Create Hero</button>   
                </div>
            </form>
        </div>
    </div>  
</body>
</html>