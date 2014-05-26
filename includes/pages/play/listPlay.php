<?php
    //Vérifie qu'il est utilisateur connecté
    confirm_is_connect();

    //---------------------------------------------------------------------------------------------
    // Préparation de la requête
        
    // Nom de variable de session 
    $sessionName = 'listPlay';
            
    //Lancer la requête et la stocker dans un tableau de session
    $query = "SELECT id, title, description, filename, creation_date, module FROM presentations";
        
    //Prépare la requête SQL 
    $statement = $databaseConnection->prepare($query);
    $statement->execute();//Execute la requête SQL

    //Vérifie que la requête se soit bien déroulée
    if($statement->error)
    {
        die("Database query failed: " . $statement->error);
    }

    //Stocke le résultat de la requête dans un tableau de variables
    $statement->bind_result($id, $title, $description, $filename, $creation_date, $module);//retour de requête SQL

    //Détruit la variable session si elle existe, évite d'ajouter à des données erronée
    if (isset($_SESSION[$sessionName])){
        unset ($_SESSION[$sessionName]);                
    }
            
    //Copie les données dans un tableau de session
    while($statement->fetch())
    {
        $_SESSION[$sessionName][]= array(
            "id" => $id,
			"title" => $title,
			"description" => $description,
			"filename" => $filename,
			"creation_date" => $creation_date,
            "module" => $module);
    }

    //---------------------------------------------------------------------------------------------
    //Teste les opérations à effectuer
    if (isset($_POST['submit_link']))
    {
        unset($_SESSION['sessionName']);
        unset($_SESSION['presentationId']);
        unset($_SESSION['pageBefor']);

        switch ($_POST['submit_action'])
        {
            case "select":
                $_SESSION['sessionName'] = $sessionName;//Sauve
                $_SESSION['presentationId'] = $_POST['submit_link'];//Sauve l'id de l'utilisateur actuel 
                $_SESSION['pageBefor'] = $_SERVER['REQUEST_URI'];//Sauve l'adresse de la page actuelle
                header ("Location: page.php?pageid=8");//selectPlay.php
                break;

            case "edit":
                $_SESSION['sessionName'] = $sessionName;//Sauve le nom du tableau $_SESSION
                $_SESSION['presentationId'] = $_POST['submit_link'];//Sauve l'id de l'utilisateur actuel 
                $_SESSION['pageBefor'] = $_SERVER['REQUEST_URI'];//Sauve l'adresse de la page actuelle
                header ("Location: page.php?pageid=6");//editPlay.php
                break;

            case "delete":
                $_SESSION['sessionName'] = $sessionName;//Sauve le nom du tableau $_SESSION
                $_SESSION['presentationId'] = $_POST['submit_link'];//Sauve l'id de l'utilisateur actuel 
                $_SESSION['pageBefor'] = $_SERVER['REQUEST_URI'];//Sauve l'adresse de la page actuelle
                header ("Location: page.php?pageid=5");//deletePlay.php
                break;

            case "register":
                $_SESSION['pageBefor'] = $_SERVER['REQUEST_URI'];//Sauve la page actuelle
                header ("Location: page.php?pageid=4");//registerPlay.php
                break;
        }
    }
            
    //---------------------------------------------------------------------------------------------
    //Affiche la requête sous forme de list dynamique
    if (isset($_SESSION[$sessionName]))
    {
        // Initialisation des variables
	    $database_titles=array( 'Titre', 
                                'Déscription',
                                'Raspberry Pi',  
                                'Date');// Titres des Items de la base
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
			    foreach ($database_titles as $Title)
			    {
				    echo '<th align="left" bgcolor="dcdcdc">'.$Title.'</th>';
			    }
			    echo '</tr>';
			
			    // Affichage des valeurs
			    echo'<tr valign=center>';
			    for ($Article=$Premier_Art_Page; $Article < $Dernier_Art_Page; $Article++)
			    {
					    /*for ( $Item=1; $Item < $Nb_Items_Art; $Item++)
					    {
                            echo'<td align="left" bgcolor="ffffff">'.$database[$Article][$Item].'</td>';
					    }*/
                        
                        echo'<td align="left" bgcolor="ffffff">'.$database[$Article][1].'</td>';
                        echo'<td align="left" bgcolor="ffffff">'.$database[$Article][2].'</td>';

                        $statementModule = $databaseConnection->prepare("SELECT ip FROM modules WHERE id=? LIMIT 1");
                        $statementModule->bind_param('d', $database[$Article][5]);
                        $statementModule->execute();

                        if($statementModule->error)
                        {
                            die("Database query failed: ".$statementModule->error);
                        }

                        $statementModule->bind_result($moduleIp);

                        while($statementModule->fetch())
                        {
                            echo'<td align="left" bgcolor="ffffff">'.$moduleIp.'</td>';
                        }

                        echo'<td align="left" bgcolor="ffffff">'.$database[$Article][4].'</td>';
                                
                        echo'<td align="left" bgcolor="ffffff">'
                                .'<a href="javascript:getIdEdit('.$_SESSION[$sessionName][$Article]["id"].')">Edit</a>'
                                .'<a href="javascript:getIdDelete('.$_SESSION[$sessionName][$Article]["id"].')">Delete</a>'
                                .'<a href="javascript:getIdSelect('.$_SESSION[$sessionName][$Article]["id"].')">Select</a>'.
                            '</td>';
					            
                        echo'</tr>';
			    }
			
			    echo'</table>';
			
			    echo '<hr>';

                echo '<input class="float-right" type="submit" onclick="javascript:getRegister()" value="Add presentation">'; // Bouton ajouter utilisateur
                //echo '<input class="float-right" type="submit" name="submit_addPresentation" value="Add presentation">'; // Bouton ajouter présentation
                     
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
        echo "There is no presentations !<br><br>";
        echo '<form method="post" action="'.$_SERVER['REQUEST_URI'].'" name="Liste" id="form_Liste">';
        echo '<input class="float-left" type="submit" onclick="javascript:getRegister()" value="Add presentation">'; // Bouton ajouter utilisateur
		echo '<input type="hidden" name="submit_link" id="ref_id_hidden" value="">'; // Mémorisation de l'Id du champ modifier
        echo '<input type="hidden" name="submit_action" id="ref_action_hidden" value="">'; // Mémorisation de l'action à accomplir
        echo '</form>';
    }

?>