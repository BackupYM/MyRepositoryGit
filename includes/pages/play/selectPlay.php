<?php
    //Vérifie qu'il est utilisateur connecté
    confirm_is_connect();

    //Nom de la session, Id de l'utilisateur
    $sessionName = $_SESSION['sessionName']; //Nom du tableau
    $id_presentation = $_SESSION['presentationId']; //id de la présentation

    for($item=0;$item<count($_SESSION[$sessionName]);$item++)
    {
        if($_SESSION[$sessionName][$item]['id'] == $id_presentation)
        {
            $id_module = $_SESSION[$sessionName][$item]['module'];
        }    
    }

    echo "Id ".$id_presentation;
    echo "<br>";
    echo "Module : ".$id_module;

    $query = "DELETE FROM presentations_in_modules";
    $statement = $databaseConnection->prepare($query);
    $statement->execute();
    $statement->store_result();

    if ($statement->error)
    {
        die('Database query failed: ' . $statement->error);
    }

    $status = 1;
        
    $query = "INSERT INTO presentations_in_modules (status, modules_id, presentations_id) VALUES (?, ?, ?)";

    $statement = $databaseConnection->prepare($query);
    $statement->bind_param('ddd', $status, $id_module, $id_presentation);
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
?>