<?php
    //Vérifie qu'il est administrateur
    confirm_is_admin();
    
    //Nom de la session, Id de l'utilisateur
    $userId = $_SESSION['userId'];
    
    //Efface l'utilisateur(user_id) de la partie de la table users_in_roles
    $query_users_in_roles = "DELETE FROM users_in_roles WHERE user_id = ?";
    $statement_users_in_roles = $databaseConnection->prepare($query_users_in_roles);
    $statement_users_in_roles->bind_param('d', $userId);
    $statement_users_in_roles->execute();
    $statement_users_in_roles->store_result();

    if ($statement_users_in_roles->error)
    {
        die('Database query failed: ' . $statement_users_in_roles->error);
    }

    //Efface l'utilisateur(id) de la partie de la table users
    $query_user = "DELETE FROM users WHERE id = ?";
    $statement_user = $databaseConnection->prepare($query_user);
    $statement_user->bind_param('d', $userId);
    $statement_user->execute();
    $statement_user->store_result();

    if ($statement_user->error)
    {
        die('Database query failed: ' . $statement_user->error);
    }

    // TODO: Check for == 1 instead of > 0 when page names become unique.
    $deletionWasSuccessful_users_in_roles = $statement_users_in_roles->affected_rows > 0 ? true : false;
    $deletionWasSuccessful_user = $statement_user->affected_rows > 0 ? true : false;
    
    if ($deletionWasSuccessful_users_in_roles && $deletionWasSuccessful_user)
    {
        header ("Location: ".$_SESSION['pageBefor']);
    }
    else
    {
        echo "Failed deleting page";
    }    
?>