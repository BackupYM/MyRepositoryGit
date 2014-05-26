<?php 
    require_once ("includes/simplecms-config.php"); 
    require_once  ("includes/connectDB.php");
    include("includes/header.php"); 
?>

<div id="main">
    <?php
        $pageid = $_GET['pageid'];
        $query = 'SELECT menulabel, content FROM pages WHERE id = ? LIMIT 1';
        $statement = $databaseConnection->prepare($query);
        $statement->bind_param('s', $pageid);
        $statement->execute();
        $statement->store_result();
        if ($statement->error)
        {
            die('Database query failed: ' . $statement->error);
        }

        if ($statement->num_rows == 1)
        {
            $statement->bind_result($menulabel, $content);
            $statement->fetch();
            
            //Titre de la page
            echo "<h2>$menulabel</h2>";// $content";

            //Inclure la page
            include("$content");
        }
        else
        {
            echo 'Page Not Found';
        }
    ?>
</div>
</div> <!-- End of outer-wrapper which opens in header.php -->
<?php 
    include ("includes/footer.php");
 ?>