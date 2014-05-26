<?php
    //Vérifie qu'il est utilisateur connecté
    confirm_is_connect();

    $error = NULL;
    if (isset($_POST['submit'], $_FILES['uploadFile']) && $_FILES['uploadFile']['error'] === 0)
    {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $creation_date = date('Y-m-d H:i:s');
        $user_uploaded_presentation = $_SESSION['username'];
        $module = $_POST['menulabel'];
        
        $filename = utf8_decode($_FILES['uploadFile']['name']);
        //On stocke le chemin où enregistrer le fichier
        $targetpath = getcwd().'/presentation/'.$filename;
        //On fait un tableau contenant les extensions autorisées.
        $extensions = array('.odp');//array('.odp', '.gif', '.jpg', '.jpeg')
        // récupère la partie de la chaine à partir du dernier . pour connaître l'extension.
        $extension = strrchr($_FILES['uploadFile']['name'], '.');

        if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
        {
            echo "You must upload a file type .odp"."<br/>";
        }
        else
        {
            //Si le fichier dépasse 100 Mo
            if ($_FILES['uploadFile']['size'] > 100000000)
            {
                echo "Exceeded filesize limit !";
                //throw new RuntimeException('Exceeded filesize limit.');
            }
            else
            {
                //echo "Type file : ".$extension."<br/>";
                //echo "Size : ".$_FILES['uploadFile']['size']."<br/>";

                //On déplace le fichier depuis le répertoire temporaire vers $targetpath
                if(move_uploaded_file($_FILES['uploadFile']['tmp_name'], $targetpath)){

                    $query = "INSERT INTO presentations (title, description, filename, creation_date, user_uploaded_presentation, module) VALUES (?, ?, ?, ?, ?, ?)";
                    $statement = $databaseConnection->prepare($query);
                    $statement->bind_param('ssssss', $title, $description, $filename, $creation_date, $user_uploaded_presentation, $module);
                    $statement->execute();
                    $statement->store_result();

                    $creationWasSuccessful = $statement->affected_rows == 1 ? true : false;
                    if ($creationWasSuccessful)
                    {
                        //Si ça fonctionne
                        header ("Location:".$_SESSION['pageBefor']);
                    }
                    else
                    {
                        //Si l'enregitrement ne fonctionne pas
                        echo "Failed registration";
                    }
                }
                else
                {
                    //Si l'upload ne fonctionne pas
                    echo "Failed to move uploaded file.";
                }
            } 
        }
    }
?>

<!-- HTML -->
<div id="main">
    <h2>Register a Play</h2>
        <form action="<?php echo $_SERVER['REQUEST_URI'];?>" enctype="multipart/form-data" method="post">
            <fieldset>
            <legend>Register a Play</legend>
            <ol>
                <li>
                    <label for="title">Title:</label> 
                    <input type="text" name="title" value="" id="title" />
                </li>
                <li>
                    <label for="description">Description:</label>
                    <textarea name="description" id="description"></textarea>
                </li>
                <li>
                    <label for="choicePi">Raspberry Pi:</label>
                    <select id="menulabel" name="menulabel">
                            <?php
                                $statementPi = $databaseConnection->prepare("SELECT id, ip FROM modules");
                                $statementPi->execute();

                                if($statementPi->error)
                                {
                                    die("Database query failed: " . $statementPi->error);
                                }

                                $statementPi->bind_result($id, $menulabel);
                                while($statementPi->fetch())
                                {
                                    echo "<option value=\"$id\">$menulabel</option>\n";
                                }
                            ?>
                        </select>
                    </li>
                <li>
                    <label for="uploadFile">File :</label>
                    <input name="uploadFile" type="file" id="uploadFile" />
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