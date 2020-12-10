 <?php
    //  Script use to connect the server
    define('DB_DSN','mysql:host=localhost;dbname=mnguy234_mobagame;charset=utf8');
    define('DB_USER','mnguyen');
    define('DB_PASS','Admin01!');    
     
    try {
        // Try creating new PDO connection to MySQL.
        $db = new PDO(DB_DSN, DB_USER, DB_PASS);
        //,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    } catch (PDOException $e) {
        print "Error: " . $e->getMessage();
        die(); 
        // Force execution to stop on errors.
    }
?>