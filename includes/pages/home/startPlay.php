<?php
    //Vérifie qu'il est utilisateur connecté
    //confirm_is_connect();

    /*
        Démarre l'application avec un fichier batch "start.sh"
        Paramètre du chemin du fichier de présentation.
    */

    include_once('../../simplecms-config.php');

    $file = $_POST['filename'];
    $batchFile = DEFAULT_PATH_BATCH."start.sh";
    $presentationFile = DEFAULT_PATH_PRESENTATION.$file;
    
    shell_exec("sh ".$batchFile." ".$presentationFile);
?>