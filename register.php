<?php
    ob_start();
    require('connect.php');
    
    $errors = array();
    
    if ($_POST) 
    {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password_1 = filter_input(INPUT_POST, 'password_1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password_2 = filter_input(INPUT_POST, 'password_2', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if ($password_1 != $password_2) 
        {
	        array_push($errors, "The two passwords do not match");
        }

        // Check the database to make sure a user does not already exist with the same username and/or email
        $user_check_query = "SELECT * FROM users WHERE username = $username OR email = $email";
        $statement = $db->prepare($user_check_query);

        // Execute the SELECT and fetch the use returned.
        $statement->execute();
        $user = $statement->fetch();

        if ($user) { // if user exists
            if ($user['username'] == $username) {
                array_push($errors, "Username already exists");
            }
        
            if ($user['email'] == $email) {
                array_push($errors, "Email already exists");
            }
        }


        if (count($errors) == 0) 
        {
            $password = password_hash($password_1, PASSWORD_DEFAULT);
        
            //  Build the parameterized SQL query and bind to the above sanitized values.
            $query = "INSERT INTO users(username, email, password) VALUES(:username, :email, :password)";
            $statement = $db -> prepare($query);
            
            //  Bind values to the parameters
            $statement -> bindValue(':username', $username);
            $statement -> bindValue(':email', $email);
            $statement -> bindValue(':password', $password);
            
            //  Execute the INSERT.
            //  execute() will check for possible SQL injection and remove if necessary
            if ($statement -> execute())
            {
                header("Location: index.php");
                exit; 
            }
        }
    }
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
        <title>Users Sign Up</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="styles/register.css">
	</head>
	<body>
        <div class="container">
            <div class="d-flex justify-content-center h-100">
                <div class="card">
                    <div class="card-header">
                        <h3>User Sign up</h3>
                    </div>
                
                    <div class="card-body">
                        <form action="register.php" method="post">

                        <?php  if (count($errors) > 0) : ?>
                            <div>
                                <?php foreach ($errors as $error) : ?>
                                <p><?php echo $error ?></p>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>

                            <div class="input-group form-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" name="username" class="form-control" placeholder="Username" required>   
                            </div>

                            <div class="input-group form-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" name="email" class="form-control" placeholder="Email" required>   
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" name="password_1" class="form-control" placeholder="Password" required>
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" name="password_2" class="form-control" placeholder="Confirm Password" required>
                            </div>
                
                            <div class="input-group justify-content-center">
                                <input type="submit" name="register" class="btn-primary" value="Register">
                            </div>
                        </form>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-center links">
                            Already a member? &nbsp; <a href="login.html"> Sign in</a>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
	</body>
</html>