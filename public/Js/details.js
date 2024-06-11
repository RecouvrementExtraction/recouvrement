document.addEventListener('DOMContentLoaded', function() {
    var dateTirageElement = document.getElementById('dateTirage');
    var currentDate = new Date();
    var formattedDate = currentDate.toLocaleDateString('fr-FR'); // Format de date français

    dateTirageElement.textContent += formattedDate;
});

function imprimerPage() {
    // Déclencher la fenêtre d'impression
    window.print();
}



document.addEventListener('DOMContentLoaded', function() {
    const presentAlertButton = document.getElementById('present-alert');

    presentAlertButton.addEventListener('click', function() {
        const name = prompt('Message du client...');
});
});



    // Fonction pour retourner à la page précédente en actualisant
    function retourPagePrecedente() {
        window.history.back();
    }


    $(document).ready(function() {
        $('#searchInput').on('input', function() {
            var searchText = $(this).val().toLowerCase(); // Récupérer le texte saisi dans le champ de recherche
            filterTable(searchText); // Appeler la fonction pour filtrer le tableau en fonction du texte saisi
        });
    });

    function filterTable(searchText) {
        // Parcourir chaque ligne du tableau
        $('#myTable tbody tr').each(function() {
            var rowText = $(this).text().toLowerCase(); // Récupérer le texte de la ligne en minuscules

            // Si le texte de la ligne contient le texte de recherche, afficher la ligne, sinon la cacher
            if (rowText.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
