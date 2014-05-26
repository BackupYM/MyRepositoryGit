<?php
    //Vérifie qu'il est administrateur
    confirm_is_admin();

    if (isset($_POST['submit']))
    {
        $name = $_POST['name'];
        $first_name = $_POST['first_name'];
        $username = $_POST['username'];
        $password = $_POST['password'].'?$#à9';

        $query = "INSERT INTO users (user_name, user_firstname, username, password) VALUES (?, ?, ?, SHA(?))";

        $statement = $databaseConnection->prepare($query);
        $statement->bind_param('ssss', $name, $first_name, $username, $password);
        $statement->execute();
        $statement->store_result();

        $creationWasSuccessful = $statement->affected_rows == 1 ? true : false;
        if ($creationWasSuccessful)
        {
            $userId = $statement->insert_id;

            $addToUserRoleQuery = "INSERT INTO users_in_roles (user_id, role_id) VALUES (?, ?)";
            $addUserToUserRoleStatement = $databaseConnection->prepare($addToUserRoleQuery);

            // TODO: Extract magic number for the 'user' role ID.
            $userRoleId = 2;
            $addUserToUserRoleStatement->bind_param('dd', $userId, $userRoleId);
            $addUserToUserRoleStatement->execute();
            $addUserToUserRoleStatement->close();

            header ("Location:".$_SESSION['pageBefor']);
        }
        else
        {
            echo "Failed registration";
        }
    }
?>

<!-- HTML -->
<div id="main">
    <h2>Register an account</h2>
        <form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
            <fieldset>
                <legend>Register an account</legend>
                <ol>
                    <li>
                        <label for="name">Name:</label> 
                        <input type="text" name="name" value="" id="name" />
                    </li>
                    <li>
                        <label for="first_name">First name:</label> 
                        <input type="text" name="first_name" value="" id="first_name" />
                    </li>
                    <li>
                        <label for="username">Username:</label> 
                        <input type="text" name="username" value="" id="username" />
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