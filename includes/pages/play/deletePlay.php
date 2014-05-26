<?php
    //Vérifie qu'il est utilisateur connecté
    confirm_is_connect();

    //Nom de la session, Id de l'utilisateur
    $sessionName = $_SESSION['sessionName'];
    $id_presentation = $_SESSION['presentationId'];

    for($item = 0 ; $item < count($_SESSION[$sessionName]) ; $item++)
    {
        if($_SESSION[$sessionName][$item]['id'] == $id_presentation)
        {
            $filename = $_SESSION[$sessionName][$item]['filename'];

            for($item = 0 ; $item < count($_SESSION[$sessionName]) ; $item++)
            {
                if($_SESSION[$sessionName][$item]['filename'] == $filename)
                {
                    $countFilename++;
                }    
            }
        }
    }
    
    //Stocke le chemin et formatte le chemin "\" en "/"
    $targetpath = str_replace("\\", "/", getcwd()."/presentation/");
    //Mettre le chemin du fichier dans une variable.
    $path = $targetpath.$filename;

    //Test si le fichier et utilisé par une autre présentation
    if($countFilename == 1)
    {
        //Si le fichier n'est pas un répertoire
        if($filename!="." AND $filename!=".." AND !is_dir($filename))
        {
            //Supprime le fichier
            unlink($path);
        }
    }
    
    //Efface la présentation (presentations_id) de la partie de la table presentations_in_modules
    $query_users_in_roles = "DELETE FROM presentations_in_modules WHERE presentations_id = ?";
    $statement_users_in_roles = $databaseConnection->prepare($query_users_in_roles);
    $statement_users_in_roles->bind_param('d', $id_presentation);
    $statement_users_in_roles->execute();
    $statement_users_in_roles->store_result();

    if ($statement_users_in_roles->error)
    {
        die('Database query failed: '.$statement_users_in_roles->error);
    }

    //Efface la présentation(id) de la partie de la table presentations
    $query_user = "DELETE FROM presentations WHERE id = ?";
    $statement_user = $databaseConnection->prepare($query_user);
    $statement_user->bind_param('d', $id_presentation);
    $statement_user->execute();
    $statement_user->store_result();

    if ($statement_user->error)
    {
        die('Database query failed: '.$statement_user->error);
    }

    // TODO: Check for == 1 instead of > 0 when page names become unique.
    //$deletionWasSuccessful_users_in_roles = $statement_users_in_roles->affected_rows > 0 ? true : false;
    $deletionWasSuccessful_user = $statement_user->affected_rows > 0 ? true : false;
    
    //if (/*$deletionWasSuccessful_users_in_roles &&*/ $deletionWasSuccessful_user)
    if ($deletionWasSuccessful_user)
    {
        header ("Location: ".$_SESSION['pageBefor']);
    }
    else
    {
        echo "Failed deleting page";
    }
?>
