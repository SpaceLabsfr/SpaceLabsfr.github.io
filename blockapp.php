<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="common.css">
    <link rel="stylesheet" type="text/css" href="app_content.css">

    <script src='https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.js'></script>
    <script type="text/javascript">

    var nb_emplacement = 3;

    function addBlock(myId) {
        $(document).ready(function() {
            $('#trame').append('<div class="bloc" id=' + myId + ' ondragenter="return dragEnter(event)" ondrop="return dragDrop(event)" ondragover="return dragOver(event)"><button class="delete" onclick="reset(' + myId + ')">X</button></div>');
        });
    }
    </script>

</head>

<?php
// Définition des paramètres PHP 

$actions = [
    "Démarrer" => array(null,null,null,"from jetracer.nvidia_racecar import NvidiaRacecar<br/>import time<br/>import sys<br/><br/>car = NvidiaRacecar()<br>time.sleep(1)<br/>car.steering_gain = -0.65<br/>car.steering_offset = -0.25<br/>if car.steering_offset != -0.25 : exit()<br/>"),
    "Avancer" => array(1,9,"s","car.throttle = -0.5<br/>time.sleep(VAR)"),
    "Reculer" => array(1,9,"s","car.throttle = 0.5<br/>time.sleep(VAR)"),
    "S'arrêter" => array(null,null,null,"car.throttle = 0.001<br/>car.throttle = 0"),
    "Tourner à gauche" => array(null, null, null,"car.steering = 1"),
    "Tourner à droite" => array(null, null, null,"car.steering = -1"),
    "Reset direction" => array(null, null, null,"car.steering = 0.001<br/>car.steering = 0"),
    "Tourner" => array(-35,35,"°","car.steering = VAR/35"),
    "Attendre" => array(1,9,"s","time.sleep(VAR)"),
    "Fin" => array(null,null,null,"<br/>sys.exit('Fin du programme')"),
];

$nb_emplacements = 5;

//$file = '/home/jetson/Desktop/KDesir_Tests/projet.py';
$file = '/KDesir_Tests/projet.py';
?>

<body>
    <h2 class="maintitle">Application de développement Python en programmation par bloc</h2>

    <!-- Affichage des blocks à drag -->
    <div class="emplacement" id="origin">
        <?php 
        $i = 100;
        foreach($actions as $key => $value){
            $i++;
            ?>
        <div id=<?=$i?> class="drag" draggable="true" ondragstart="return dragStart(event)">
            <p class="action"><?= $key ?>
            <?php
            if($value[0] != null){
                echo '<input type="number" min="'.$value[0].'" max="'.$value[1].'" value="1">'.$value[2];
            }
            ?>
            </p>
        </div>
        <?php } ?>
    </div>

    <!-- Affichage des emplacements de drop -->
    <div class="emplacement" id="trame">
        <!--
        <div class="bloc" id=2 ondragenter="return dragEnter(event)" ondrop="return dragDrop(event)" ondragover="return dragOver(event)">
            <button class="delete" onclick="reset(2)">X</button>
        </div>-->
        <script type="text/javascript">
            for (i = 1; i <= nb_emplacement; i++) {
                addBlock(i);
            }
        </script>
    </div>

    <br/><br/>
    <button class="submit" onclick="generate()">Valider</button>

    <form method="POST" > <!-- action="/#" pour empêcher de re-exécuter lorsqu'on rafraîchit -->
        <br/><input type="submit" name="sauvegarder" value="Exécuter" >
    </form>

	<?php 
		if(isset($_POST['sauvegarder'])) {
			//$file = '/home/jetson/Desktop/KDesir_Tests/projet.py';
			$output = $_COOKIE['output'];
			$output = str_replace("<br/>","\n",$output);
			$output = str_replace("<br>","\n",$output);
			$myfile = fopen($file, "w");
			fwrite($myfile, $output);
			fclose($myfile);

			shell_exec('sudo python3 /KDesir_Tests/projet.py');
			//echo shell_exec('sudo python3 /KDesir_Tests/projet.py 2>&1'); //for debug
			echo '<meta http-equiv="refresh" content="1; URL=blockapp.php" />';
		}
	?>
    
    <div class="section">
        <div class="big">
            <p class="title">Résultats :</p>
            <p><span id="result">. . .</span></p>
        </div>
    </div>

</body>


<script type="text/javascript">
    // Reset du contenu des emplacements
    function reset(id) {
        //console.log(document.getElementById(id));
        node = document.getElementById(id);
        //console.log(node.childNodes[1]);
        if (node.childNodes[2]) {
            node.removeChild(node.childNodes[2]);
            node.removeChild(node.childNodes[2]);
        } else if (node.childNodes[1]) {
            node.removeChild(node.childNodes[1]);
        } else if (id > nb_emplacement) { // Si aucun élément n'existe dans la case (sauf la croix) et que la case a été créé par l'utilisateur
            document.getElementById(id).parentNode.removeChild(document.getElementById(id));
        }
    }

    // Drag & Drop

    function dragStart(ev) {
        ev.dataTransfer.effectAllowed = 'move';
        ev.dataTransfer.setData("Text", ev.target.getAttribute('id'));
        ev.dataTransfer.setData("Origin", ev.target.parentNode.getAttribute('id'))
        ev.dataTransfer.setDragImage(ev.target, 0, 0);
        return true;
    }

    function dragEnter(ev) {
        event.preventDefault();
        return true;
    }

    function dragOver(ev) {
        return false;
    }

    function dragDrop(ev) {

        event.preventDefault();

        var src = ev.dataTransfer.getData("Text");
        var tempo = document.createElement('span');
        tempo.className = 'hide';

        var origin_id = ev.dataTransfer.getData("Origin")
    
        if (!ev.target.id || ev.target.childNodes[0].nodeName == "#text") {
            // Si on drop pas dans une case prévue à cet effet...
            console.log("Oups, vous n'avez pas posé le block dans un emplacement valide !");
            // Exemple : La croix, ou une case avec un block, ou un block
            return false;
        }

        //console.log(ev.target.parentNode.childElementCount)
        //console.log(src)
        //console.log(origin_id)
        //console.log(ev.target.id)
        //console.log(ev.target.childElementCount)
        if (ev.target.childElementCount == 1) {
            var copy = document.getElementById(src).cloneNode(true);
            copy.id = copy.id + "-copy";
            // On ne peut pas déplacer un élément copié
            // Pour cela, on rend la copie impossible à drag
            copy.draggable = false;
            copy.ondragstart = false;
            ev.target.appendChild(copy);
        }
        ev.stopPropagation();

        // Ajout d'un nouveau block
        if (document.getElementById(parseInt(ev.target.id) + 1) == null) {
            addBlock(parseInt(ev.target.id) + 1);
        }

        return false;
    }

    // Génération du code Python selon les actions choisies

    function generate() {
        actions_list = [] // liste des actions voulues
        var actions = <?php echo json_encode($actions); ?>; // Récupération de la liste des actions possibles en PHP

        element = document.getElementsByClassName('bloc')
        //console.log(element)
        //console.log("element.length" + element.length)
        output = ""; // Code Python à ressortir


        for (var i = 0; i < element.length; i++) {
            //console.log(element[i]) // Liste des actions prises

            children = element[i].childNodes;
            //console.log(children)

            for (var j = 0; j < children.length; j++) {
                if (children[j].className == 'drag') {
                    action = children[j].textContent.trim();

                    //console.log(action.split(" ").slice(-1)[0]);
                    if(action.split(" ").splice(-1)[0].length == 1){ // Si on a un espace
                        value = children[j].childNodes[1].childNodes[1].value; // La valeur dans l'input
                        //console.log("Valeur:",value);

                        //actions_list.push(children[j].id)
                        text = children[j].textContent;
                        action = text.trim();
                        action = action.split(" ")[0];
                        //console.log(action);
                    }
                    else{ 
                        value = null;
                    }
                    
                    console.log(action)
                    PythonScript = actions[action][3];
                    PythonScript = PythonScript.replace("VAR",value);

                    output += PythonScript;            
                    output += "<br/>";

                    //actions_list.push(text);
                }
            }
        }
        
        document.getElementById("result").innerHTML = output;
        document.cookie="output="+output.toString();
    }

</script>

</html>
