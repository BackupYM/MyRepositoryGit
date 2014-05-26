<?php
    //Vérifie qu'il est utilisateur connecté
    confirm_is_connect();

    //Nom de la session, Id de l'utilisateur
    $sessionName = $_SESSION['sessionName'];
    $id_presentation = $_SESSION['presentationId'];

    $countFilename = 0;
    for($item = 0 ; $item < count($_SESSION[$sessionName]) ; $item++)
    {
        if($_SESSION[$sessionName][$item]['id'] == $id_presentation)
        {
            $title = $_SESSION[$sessionName][$item]['title'];
            $description = $_SESSION[$sessionName][$item]['description'];
            $filename = $_SESSION[$sessionName][$item]['filename'];
            $idModule = $_SESSION[$sessionName][$item]['module'];

            for($item = 0 ; $item < count($_SESSION[$sessionName]) ; $item++)
            {
                if($_SESSION[$sessionName][$item]['filename'] == $filename)
                {
                    $countFilename++;
                }    
            }
        }
    }

    if (isset($_POST['submit']))
    {
        
        if(empty($_FILES['uploadFile']['name']))
        {
            //Si upload est vide reprendre le nom de fichier inchangé
            $filename = $_POST['filename'];
        }
        else
        {
            //Si upload contient un nouveau fichier, le télécharger et l'inscrire 
            //dans la nouvelle base en vérifiant la taille et le type et supprimer 
            //l'ancienne présentation
            
            //On détruit l'ancien
            $filename = $_POST['filename'];
            //On stocke le chemin où enregistrer le fichier
            $targetpath = str_replace('\\', '/', getcwd().'/presentation/'.$filename);
            
            //Test si le fichier et utilisé par une autre présentation
            if($countFilename == 1)
            {
                //Si le fichier n'est pas un répertoire
                if($filename!="." AND $filename!=".." AND !is_dir($filename))
                {
                    //Supprime le fichier
                    unlink($targetpath);
                }
            }

            //Le nouveau nom du fichier
            $filename = utf8_decode($_FILES['uploadFile']['name']);
            //On stocke le chemin où enregistrer le fichier
            $targetpath = str_replace('\\', '/', getcwd().'/presentation/'.$filename);

            //On fait un tableau contenant les extensions autorisées.
            $extensions = array('.odp'); //array('.odp', '.gif', '.jpg', '.jpeg')
            // récupère la partie de la chaine à partir du dernier . pour connaître l'extension.
            $extension = strrchr($_FILES['uploadFile']['name'], '.');
            
            if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
            {
                echo "You must upload a file type .odp"."<br>";
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
                    //On déplace le fichier depuis le répertoire temporaire vers "$targetpath"
                    if(!move_uploaded_file($_FILES['uploadFile']['tmp_name'], $targetpath))
                    {
                        //Si l'upload ne fonctionne pas
                        echo "Failed to move uploaded file.";
                    }
                }
            }
        }

        $id = $_SESSION['presentationId'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $creation_date = date('Y-m-d H:i:s');
        $user_uploaded_presentation = $_SESSION['username'];
        $module = $_POST['menulabel'];
        
        $query = "UPDATE presentations SET  title = ?, 
                                            description = ?, 
                                            filename = ?, 
                                            creation_date = ?,
                                            user_uploaded_presentation = ?,
                                            module = ?
                                       WHERE id = ?";

        $statement = $databaseConnection->prepare($query);
        $statement->bind_param('sssssdd',   $title, 
                                            $description, 
                                            $filename, 
                                            $creation_date,
                                            $user_uploaded_presentation,
                                            $module, 
                                            $id);
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
            echo 'Failed to edit page';
        }
    }
?>

<div id="main">
    <h2>Edit an play</h2>
        <form action="<?php echo $_SERVER['REQUEST_URI'];?>" enctype="multipart/form-data" method="post">
            <fieldset>
                <legend>Edit an play</legend>
                <ol>
                    <li>
                        <label for="title">Titre:</label> 
                        <input type="text" name="title" value="<?php echo $title;?>" id="title" />
                    </li>
                    <li>
                        <label for="description">Description:</label>
                        <textarea name="description" id="description"><?php echo $description;?></textarea>
                    </li>
                    <li>
                        <label for="choicePi">Raspberry Pi:</label>
                        <select id="menulabel" name="menulabel">
                            <?php
                                $statementModuleActive = $databaseConnection->prepare("SELECT id, ip FROM modules WHERE id=? LIMIT 1");
                                $statementModuleActive->bind_param('d', $idModule);
                                $statementModuleActive->execute();

                                if($statementModuleActive->error)
                                {
                                    die("Database query failed: ".$statementModuleActive->error);
                                }

                                $statementModuleActive->bind_result($idActive, $moduleIpActive);

                                while($statementModuleActive->fetch())
                                {
                                    echo "<option value=\"$idActive\">$moduleIpActive</option>\n";
                                }
                            
                                $statementModule = $databaseConnection->prepare("SELECT id, ip FROM modules WHERE id<>?");
                                $statementModule->bind_param('d', $idModule);
                                $statementModule->execute();

                                if($statementModule->error)
                                {
                                    die("Database query failed: ".$statementModule->error);
                                }

                                $statementModule->bind_result($id, $moduleIp);

                                while($statementModule->fetch())
                                {
                                    echo "<option value=\"$id\">$moduleIp</option>\n";
                                }        
                            ?>
                        </select>
                    </li>
                    <li>
                        <label for="uploadFile">Current file :</label>
                        <input type="text" name="filename" value="<?php echo $filename;?>" id="filename" readonly/>
                    </li>
                    <li>
                        <label for="uploadFile">Change file :</label>
                        <input type="file" name="uploadFile" id="uploadFile" />
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