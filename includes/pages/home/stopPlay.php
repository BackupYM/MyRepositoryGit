<?php
    //Vérifie qu'il est utilisateur connecté
    //confirm_is_connect();

    /*
        Stop la présentation avec un fichier batch "stop.sh"
        Nom du fichier de présentation.
    */
    include_once('../../simplecms-config.php');

    $batchFileStop = DEFAULT_PATH_BATCH."stop.sh";
    shell_exec("sh ".$batchFileStop);
?>