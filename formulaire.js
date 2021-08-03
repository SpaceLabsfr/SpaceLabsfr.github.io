// Affichage selon les choix du formulaire

function selectfunction(id) {
    // Liste des options de ma liste déroulante
    const optionList = document.getElementById(id).options;
    const nb_options = document.getElementById(id).options.length;

    // Mon choix
    var getOption = document.getElementById(id).value;
    console.log(optionList)
    console.log(getOption)
    for (i = 0; i < nb_options; i++) {
        // Pour chaque option
        option = optionList[i].value
            // On compare avec le choix et on applique le style correspondant
        if (getOption == option) {
            document.getElementById(option).style.display = "block";
        } else {
            document.getElementById(option).style.display = "none";
        }
    }
}

// Gestion de l'envoi du mail