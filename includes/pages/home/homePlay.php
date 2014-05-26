<?php
    //Vérifie qu'il est utilisateur connecté
    confirm_is_connect();

    //Titre de la page
    echo "<h2>Summary :</h2>";

    // Nom de variable de session 
    $sessionName = 'homePlay';

    //Préparer la requête
    /*$query = "SELECT user_id FROM users_in_roles UIR 
                  INNER JOIN roles R ON UIR.role_id = R.id 
                  WHERE R.name = 'admin' AND UIR.user_id = ? LIMIT 1";*/
    $query = '  SELECT P.id, P.title, P.filename, M.ip 
                FROM presentations P 
                INNER JOIN modules M
                ON P.module = M.id 
                INNER JOIN presentations_in_modules PIM 
                ON P.id = PIM.presentations_id WHERE PIM.status = "1";';

    //Connecter et executer la requête SQL 
    $statement = $databaseConnection->prepare($query);
    $statement->execute();//Execute la requête SQL

    //Vérifie que la requête se soit bien déroulée
    if($statement->error)
    {
        die("Database query failed: " . $statement->error);
    }

    //Stocke le résultat de la requête dans un tableau de variables
    $statement->bind_result($id, $titre, $filename, $ip);//retour de requête SQL

    //Détruit la variable session si elle existe, évite d'ajouter à des données erronée
    if (isset($_SESSION[$sessionName])){
        unset ($_SESSION[$sessionName]);                
    }
            
    //Copie les données dans un tableau de session
    while($statement->fetch())
    {
        $_SESSION[$sessionName][]= array(
			"id" => $id,
            "titre" => $titre,
			"filename" => $filename,
			"ip" => $ip);
    }
            
    //---------------------------------------------------------------------------------------------
    //Affiche la requête sous forme de list dynamique
    if (isset($_SESSION[$sessionName]))
    {
        // Initialisation des variables
	    $database_titles=array( 'Titre', 
                                'Raspberry Pi');// Titres des Items de la base
	    $database=array();						// Liste des utilisateurs pour authentification
	    $choice=array("5","10","20","50","100");// Option du choix d'affichage d'article par page
	    $Nb_Tot_Page = 1;						// Nombre total de page du catalogue
	    $email = DEFAULT_EMAIL;	// Adresse mail pour contact
	
	    //////////////////////////////////////////////////////////////////////////////////////////////////
	    // Chargement des données de la requête (dans un tableau multi-dimensionnel)
	    //////////////////////////////////////////////////////////////////////////////////////////////////

        // Récupération des données de la base de donnée
        // en listant le tableau 2 dimensions $_SESSION['presentation'][]
        $item = 0;
        foreach($_SESSION[$sessionName] as $list)
        {
            $index = 0;
            foreach($list as $value)
            {
                $database[$item][$index]=$value;
                // Incrémente compteur donnée suivante
		        $index++;
            }
            // Incrémente compteur donnée suivante
            $item++;
        }

	    //////////////////////////////////////////////////////////////////////////////////////////////////
	    // Traitement des informations transmises par ($_POST) et calculs de formattage des pages
	    //////////////////////////////////////////////////////////////////////////////////////////////////
	    // Récupération de la page sélectionnée
	    if ( isset($_POST['Page']) && ($_POST['Page'] > 1) )
	    {			
		    $Page_Courante = $_POST['Page'];
	    }
	    else
	    {
		    $Page_Courante = 1;// Numéro de la page courante par défaut(catalogue)
	    }
	
	    // Récupération du nombre d'articles par page sélectionnés
	    if ( isset($_POST['Nb_Article']) && ( $_POST['Nb_Article'] > 1 ) )
	    {
		    $Nb_Art_Page = $_POST['Nb_Article'];
	    }
	    else
	    {
		    $Nb_Art_Page = $choice[2];// Choix par défaut du nombre d'articles par page
	    }
	
	    // Nombre d'articles totaux (catalogue)
	    $Nb_Art_Total = count($database);
	
	    // Nombre total de pages (catalogue)
	    $Nb_Tot_Page = ceil($Nb_Art_Total / $Nb_Art_Page);
	
	    // Nombre d'Items par articles
	    $Nb_Items_Art = count($database[0]);
	
	    // Détection du nième passage et correction du dépassement de page en fonction du nombre d'articles par page
	    if ( isset($_POST['Page_Courante']) )
	    {
		    if ( $Page_Courante > $Nb_Tot_Page )
		    {
			    $Page_Courante = 1;	// Numéro de la page courante par défaut(catalogue)
		    }
	    }
	
	    // Calcul des intervalles pour les articles (catalogue)
	    $Dernier_Art_Page = ($Page_Courante * $Nb_Art_Page);
	    $Premier_Art_Page = ($Dernier_Art_Page - $Nb_Art_Page);
	
	    // Détection de la fin de liste (page non complète)
	    if ( $Dernier_Art_Page > $Nb_Art_Total ) 
	    { 
		    $Dernier_Art_Page = $Nb_Art_Total;
	    };

    //////////////////////////////////////////////////////////////////////////////////////////////////
    // Génération de la partie HTML (dynamiquement)
    //////////////////////////////////////////////////////////////////////////////////////////////////
    echo '<hr>';
		    // Envoi du script sur lui-même indépendament du nom du fichier( $_SERVER[PHP_SELF'] )
            echo '<form method="post" action="'.$_SERVER['REQUEST_URI'].'" name="Liste" id="form_Liste">
            <table width="100%" align="center" border="0">
		    <tr>
			    <td>Items per page : <select name="Nb_Article" onchange="document.Liste.submit();">';
				    foreach ($choice as $Nb_Art)
				    {
					    echo '<option';
					
					    // Affichage avec focus correct du nombre d'articles dans la liste
					    if ( isset ($_POST['Nb_Article']) &&  ($_POST['Nb_Article'] == $Nb_Art) )
					    {
						    echo ' selected';
					    }elseif ($Nb_Art == $Nb_Art_Page )
					    {
						    echo ' selected';
					    }
					    echo'>'.$Nb_Art.'</option>';
				    }
				    echo '</select>';
			
			    echo '</td>
			
			    <td align="right">Page : <select name="Page" onchange="document.Liste.submit();">';
				    for ( $Page=1; $Page <= $Nb_Tot_Page; $Page++ )
				    {
					    echo '<option';
					
					    // Affichage avec focus correct du nombre de pages dans la liste
					    if ( $Page==$Page_Courante )
					    {
						    echo ' selected';
					    }
					    echo'>'.$Page.'</option>';
				    }
				    echo '</select>';
			    echo '</td>
		    </tr>
		    </table>
		    </br>
		    <hr>';
			
			    // Affichage du contenu de la page (articles du catalogue)
			    echo '<table width="100%" align="center" border="0" bgcolor="dcdcdc">';
			
			    // Affichage des titres du tableau
			    echo '<tr>';
			    foreach ($database_titles as $Titre)
			    {
				    echo '<th align="left" bgcolor="dcdcdc">'.$Titre.'</th>';
			    }
			    echo '</tr>';
			
			    // Affichage des valeurs
			    echo'<tr valign=center>';
                $collone = 1;
			    for ($Article=$Premier_Art_Page; $Article < $Dernier_Art_Page; $Article++)
			    {
					    /*for ( $Item=1; $Item < $Nb_Items_Art; $Item++)
					    {
                            echo'<td align="left" bgcolor="ffffff">'.$database[$Article][$Item].'</td>';
					    }*/
                        echo'<td align="left" bgcolor="ffffff">'.$database[$Article][1].'</td>';
                        echo'<td align="left" bgcolor="ffffff">'.$database[$Article][3].'</td>';
                                
                        echo'<td align="left" bgcolor="ffffff">'
                                .'<a href="javascript:getFilenamePlay(\''.$_SESSION[$sessionName][$Article]["filename"].'\')">Play</a>'
                                .'<a href="javascript:getFilenameStop(\''.$_SESSION[$sessionName][$Article]["filename"].'\')">Stop</a>'.
                            '</td>';
					            
                        echo'</tr>';
			    }
			
			    echo'</table>';
			
			    echo '<hr>';
			    echo '<input type="hidden" name="Page_Courante" value="'.$Page_Courante.'">'; // Mémorisation de la page courante
			    echo '<input type="hidden" name="submit_link" id="ref_id_hidden" value="">'; // Mémorisation de l'Id du champ modifier
                echo '<input type="hidden" name="submit_action" id="ref_action_hidden" value="">'; // Mémorisation de l'action à accomplir
                echo'</form>';
			
			    // Affichage de bas de liste
			    echo '<center>|&nbsp&nbsp<a href="javascript:window.print()">Print this page</a>&nbsp&nbsp|&nbsp&nbsp<a
			    href="mailto:'.$email.'">Contact</a>&nbsp&nbsp|</center></br>';
    //////////////////////////////////////////////////////////////////////////////////////////////////
    // Fin du script
    //////////////////////////////////////////////////////////////////////////////////////////////////
                
    }
    else
    {
        echo "Not selected presentations !";
    }
?>