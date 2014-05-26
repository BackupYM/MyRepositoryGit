<?php
    //Vérifie qu'il est administrateur
    confirm_is_admin();

    //Nom de la session, Id de l'utilisateur
    $sessionName = $_SESSION['sessionName'];
    $id_user = $_SESSION['userId'];

    for($item = 0 ; $item < count($_SESSION[$sessionName]) ; $item++)
    {
        if($_SESSION[$sessionName][$item]['id'] == $id_user)
        {
            $username = $_SESSION[$sessionName][$item]['username'];
            $user_name =  $_SESSION[$sessionName][$item]['user_name'];
            $user_firstname = $_SESSION[$sessionName][$item]['user_firstname'];
        }
    }
    
    if (isset($_POST['submit']))
    {
        $id = $_SESSION['userId'];
        $username = $_POST['username'];
        $password = $_POST['password'].'?$#à9';
        $user_name = $_POST['name'];
        $user_firstname = $_POST['first_name'];

        $query = "UPDATE users SET username = ?, password = SHA(?), user_name = ?, user_firstname = ?  WHERE id = ?";

        $statement = $databaseConnection->prepare($query);
        $statement->bind_param('ssssd', $username, $password, $user_name, $user_firstname, $id);
        $statement->execute();
        $statement->store_result();

        if ($statement->error)
        {
            die('Database query failed: ' . $statement->error);
        }

        $creationWasSuccessful = $statement->affected_rows == 1 ? true : false;
        if ($creationWasSuccessful)
        {
            header ("Location: ".$_SESSION['pageBefor']);
        }
        else
        {
            echo 'Failed to edit user';
        }
    }
?>

<!---->
<div id="main">
    <h2>Edit an account</h2>
        <form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
            <fieldset>
                <legend>Edit an account</legend>
                <ol>
                    <li>
                        <label for="name">Name:</label> 
                        <input type="text" name="name" value="<?php echo $user_name;?>" id="name" />
                    </li>
                    <li>
                        <label for="first_name">First name:</label> 
                        <input type="text" name="first_name" value="<?php echo $user_firstname;?>" id="first_name" />
                    </li>
                    <li>
                        <label for="username">Username:</label> 
                        <input type="text" name="username" value="<?php echo $username;?>" id="username" />
                    </li>
                    <li>
                        <label for="password">Password:</label>
                        <input type="password" name="password" value="" id="password" />
                    </li>
                </ol>
                <input type="submit" name="submit" value="Submit" />
                <p>
                    <a href="<?php echo $_SESSION['pageBefor']; ?>">Cancel</a>
                </p>
            </fieldset>
        </form>
     </div>
</div> <!-- End of outer-wrapper which opens in header.php -->
