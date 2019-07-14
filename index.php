<?php
session_start();
spl_autoload_register();
ini_set('display_errors',1); 
error_reporting(E_ALL);

$baseURL = 'http://' . $_SERVER['HTTP_HOST'].'/'.basename(dirname(__FILE__)) . '/';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Test Web App</title>

        <link rel="stylesheet" href="<?php echo $baseURL ?>styles.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>
    <body>
        <a href="<?php echo $baseURL ?>company/54543456645">Try semantic url</a>
        <?php
            if ( isset($_GET['company_id']) ) echo "<div>Company: {$_GET['company_id']}</div>";
            
            
            $managerTasksComponents=new managertaskscomponents();
            echo $managerTasksComponents->getTasksTable();
            ?>


        <script type="text/javascript" src="<?php echo $baseURL ?>jquery-1.11.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="<?php echo $baseURL ?>index.js"></script>
        <script src="<?php echo $baseURL ?>ajaxupdate.js"></script>
    </body>
</html>
