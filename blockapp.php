<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="common.css">
    <link rel="stylesheet" type="text/css" href="app_content.css">
</head>

<body>
    <h2 class="maintitle">Application de développement Python en programmation par bloc</h2>

    <!-- Affichage des blocks à drag -->
    <div class="emplacement" id="origin">

        <div id="102" class="drag" draggable="true" ondragstart="return dragStart(event)">
            <p class="action">Démarrer</p>
        </div>
        <div id="103" class="drag" draggable="true" ondragstart="return dragStart(event)">
            <p class="action">Tourner à gauche</p>
        </div>
        <div id="104" class="drag" draggable="true" ondragstart="return dragStart(event)">
            <p class="action">Tourner à droite</p>
        </div>
        <div id="105" class="drag" draggable="true" ondragstart="return dragStart(event)">
            <p class="action">Avancer</p>
        </div>
        <div id="106" class="drag" draggable="true" ondragstart="return dragStart(event)">
            <p class="action">Reculer</p>
        </div>
    </div>

    <!-- Affichage des emplacements de drop -->
    <div class="emplacement" id="trame">
        <div class="bloc" id=1 ondragenter="return dragEnter(event)" ondrop="return dragDrop(event)" ondragover="return dragOver(event)">
            <button class="delete" onclick="reset(1)">X</button>
        </div>
        <div class="bloc" id=2 ondragenter="return dragEnter(event)" ondrop="return dragDrop(event)" ondragover="return dragOver(event)">
            <button class="delete" onclick="reset(2)">X</button>
        </div>
        <div class="bloc" id=3 ondragenter="return dragEnter(event)" ondrop="return dragDrop(event)" ondragover="return dragOver(event)">
            <button class="delete" onclick="reset(3)">X</button>
        </div>
        <div class="bloc" id=4 ondragenter="return dragEnter(event)" ondrop="return dragDrop(event)" ondragover="return dragOver(event)">
            <button class="delete" onclick="reset(4)">X</button>
        </div>
    </div>

    <br/><br/><br/>
    <button class="submit" onclick="generate()">Valider</button>


  
	<?php 
		if(isset($_POST["sauvegarder"])) {
			$file = '/home/jetson/Desktop/KDesir_Tests/projet.py';
			$output = $_COOKIE['output'];
			//echo $output;
            $output = str_replace("<br/>","\n",$output);
            $output = str_replace("<br>","\n",$output);
			$myfile = fopen($file, "w");
			fwrite($myfile, $output);
			fclose($myfile);
		}

	?>
    <form method="post">
        <input type="submit" name="sauvegarder" value="Enregistrer le fichier python" >
    </form>


    <div class="section">
        <div class="big">
            <p class="title">Résultats :</p>
            <p><span id="result">. . .</span></p>
        </div>
    </div>
</body>


<script type="text/javascript ">
    // Reset du contenu des emplacements
    function reset(id) {
        console.log(id);
        console.log(document.getElementById(id));
        node = document.getElementById(id);
        if (node.childNodes[2]) {
            node.removeChild(node.childNodes[2]);
            node.removeChild(node.childNodes[2]);
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

        // On empêche de drop si le parent a un élément qui existe déjà!
        //console.log(ev.target.parentNode.childElementCount)
        //console.log(src)
        //console.log(origin_id)
        //console.log(ev.target.id)
        //console.log(ev.target.childElementCount)
        if (ev.target.childElementCount == 1) {
            var copy = document.getElementById(src).cloneNode(true);
            copy.id = copy.id + "-copy";
            // On ne peut pas déplacer un élément copié
            // Donc on rend la copie impossible à drag
            copy.draggable = false;
            copy.ondragstart = false;
            ev.target.appendChild(copy);
        }
        ev.stopPropagation();
        return false;
    }

    // Génération du code Python selon les actions choisies

    function generate() {
        actions_list = [] // liste des actions voulues
        actions = document.getElementById("trame");

        element = document.getElementsByClassName('bloc')
        console.log(element)
        console.log("element.length" + element.length)

        for (var i = 0; i < element.length; i++) {
            //console.log(element[i]) // Liste des actions prises

            children = element[i].childNodes;
            //console.log(children)

            for (var j = 0; j < children.length; j++) {
                if (children[j].className == 'drag') {
                    // Succès si le sous-élément a la class "drag"
                    actions_list.push(children[j].id) // Alors on a l'action dans l'ID
                }
            }
        }
        //console.log(actions_list);

        output = ""; // Code Python à ressortir
        actions_list.forEach(function(element) {
            switch (element) {
                case "101-copy":
                    output += "Logo Youtube<br/>";
                    break;
                case "102-copy":
                    output += "from jetracer.nvidia_racecar import NvidiaRacecar<br/>car = NvidiaRacecar()<br/>";
                    break;
                case "103-copy":
                    output += "car.steering = 0.3<br/>";
                    break;
                case "104-copy":
                    output += "car.steering = -0.3<br/>";
                    break;
                case "105-copy":
                    output += "car.rolls = \"test\"<br/>";
                    break;
            }
        })

        document.getElementById("result").innerHTML = output;

        // Update 19/11/2021 : Generation du fichier Python

     //   output = output.replaceAll("<br>", "\n");
     //   output = output.replaceAll("<br/>", "\n");
        // Génération du téléchargement du fichier projet.py
        //var codePython = "from jetracer.nvidia_racecar import NvidiaRacecar\n";
        //codePython += "car = NvidiaRacecar()";
        //var filename = "projet.py";
        //download(filename, output);
    document.cookie="output="+output.toString();
    }


    function download(filename, text) {
        var element = document.createElement('a');
        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
        element.setAttribute('download', filename);

        element.style.display = 'none';
        document.body.appendChild(element);

        element.click();

        document.body.removeChild(element);
    }
</script>

</html>