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
