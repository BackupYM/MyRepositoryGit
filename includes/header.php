<?php require_once ("includes/session.php"); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>My Site's Title</title>
        
        <!-- On inclut la bibliothèque depuis les serveurs de Google-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>

        <!--Insertion des pages CSS et Javascript-->
        <link href="styles/site.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="includes/javascript/script.js"></script>
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <div class="outer-wrapper">
        <header>
            <div class="content-wrapper">
                <div class="float-left">
                    <p class="site-title"><a href="/index.php"><img src="../images/logoORIF_207x100.png" alt="logo orif"></a></p>
                </div>
                <div class="float-right">
                    <section id="login">
                        <ul id="login">
                        <?php
                        if (logged_on())
                        {
                            $admin = FALSE;
                            echo '<li><a href="/logoff.php">Sign out</a></li>' . "\n";
                            
                            if (is_admin())
                            {
                                $admin = TRUE;
                                echo '<li><a href="/addpage.php">Add</a></li>' . "\n";
                                echo '<li><a href="/selectpagetoedit.php">Edit</a></li>' . "\n";
                                echo '<li><a href="/deletepage.php">Delete</a></li>' . "\n";
                            }
                        }
                        else
                        {
                            //echo '<li><a href="/logon.php">Login</a></li>' . "\n";
                            //echo '<li><a href="/register.php">Register</a></li>' . "\n";
                        }
                        ?>
                        </ul>
                        <?php if (logged_on()) {
                            echo "<div class=\"welcomeMessage\">Welcome, <strong>{$_SESSION['username']}</strong></div>\n";
                        } ?>
                    </section>
                </div>

                <div class="clear-fix"></div>
            </div>

                <section class="navigation" data-role="navbar">
                    <nav>
                        <ul id="menu">
                            <li><a href="/index.php">Play</a></li>
                            <?php
                                $statement = $databaseConnection->prepare("SELECT id, menulabel, niveau FROM pages");
                                $statement->execute();

                                if($statement->error)
                                {
                                    die("Database query failed: " . $statement->error);
                                }

                                $statement->bind_result($id, $menulabel, $niveau);
                                while($statement->fetch())
                                {
                                    if($menulabel!="")
                                    {
                                        if(logged_on() && $niveau=="2")
                                        {
                                            echo "<li><a href=\"/page.php?pageid=$id\">$menulabel</a></li>\n";
                                        }

                                        if(logged_on() && $admin && $niveau=="1")
                                        {
                                            echo "<li><a href=\"/page.php?pageid=$id\">$menulabel</a></li>\n";
                                        }
                                    }
                                }
                            ?>
                        </ul>
                    </nav>
            </section>
        </header>
