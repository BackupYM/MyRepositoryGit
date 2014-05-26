/*
Fonctions gérant les champs cachés 
    - id (de la base de données)
    - action (ex: edit, delete, select, play)
*/

function getIdEdit(id)
{
    document.getElementById("ref_id_hidden").value = id;
    document.getElementById("ref_action_hidden").value = "edit";
    document.getElementById("form_Liste").submit();
}

function getIdDelete(id)
{
    document.getElementById("ref_id_hidden").value = id;
    document.getElementById("ref_action_hidden").value = "delete";
    document.getElementById("form_Liste").submit();
}

function getIdSelect(id)
{
    document.getElementById("ref_id_hidden").value = id;
    document.getElementById("ref_action_hidden").value = "select";
    document.getElementById("form_Liste").submit();
}

function getFilenamePlay(filename)
{
    $.ajax({
        type: 'POST',
        url: 'includes/pages/home/startPlay.php',
        dataType: "html",
        data: "filename=" + filename,
        timeout: 5000,
        success: function (data)
        {
            alert('Presentation successfully launched !');
        },
        error: function (data, textstatus, errorThrown)
        {
            // la page va toujours renvoyer un timeout car le script ne rend pas la main
            // si erreur = timeout, la présentation est tout de même envoyée.
            if (errorThrown == "timeout")
                alert('Presentation successfully launched !');
            else
                alert('Error');
        }
    });
}

function getFilenameStop(filename)
{
     $.ajax({
        type: 'POST',
        url: 'includes/pages/home/stopPlay.php',
        dataType: "html",
        timeout: 5000,
        success: function(data) {
            //alert(data); 
            },
        error: function(data) {
            //alert(data.responseText); 
            }
        });
}

function getRegister()
{
    document.getElementById("ref_action_hidden").value = "register";
}