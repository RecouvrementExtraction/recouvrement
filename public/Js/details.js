document.addEventListener('DOMContentLoaded', function() {
    var dateTirageElement = document.getElementById('dateTirage');
    if (dateTirageElement) {
        var currentDate = new Date();
        var formattedDate = currentDate.toLocaleDateString('fr-FR'); // Format de date français
        dateTirageElement.textContent += formattedDate;
    }

    const presentAlertButton = document.getElementById('present-alert');
    if (presentAlertButton) {
        presentAlertButton.addEventListener('click', function() {
            prompt('Message du client...');
        });
    }
});

function imprimerPage() {
    window.print();
}

function retourPagePrecedente() {
    window.history.back();
}

$(document).ready(function() {
    $('#searchInput').on('input', function() {
        var searchText = $(this).val().toLowerCase(); // Récupérer le texte saisi dans le champ de recherche
        filterTable(searchText); // Appeler la fonction pour filtrer le tableau en fonction du texte saisi
    });

    function filterTable(searchText) {
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

    // Désactiver le bouton d'envoi
    $('input[type="text"]').on('keyup change', function() {
        let formId = $(this).attr('id').split('_')[1];  // Extraire le CT_Num de l'id
        let inputVal = $(this).val();  // Valeur de l'input
        let submitBtn = $('#submitBtn_' + formId);  // Bouton de soumission correspondant

        // Activer ou désactiver le bouton de soumission
        if (inputVal.trim() !== "") {
            submitBtn.prop('disabled', false);
        } else {
            submitBtn.prop('disabled', true);
        }
    });

    // Initialisation du datepicker
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true
    });

    $('.dateBtn').on('click', function() {
        var index = $(this).attr('id').split('_')[1];
        $('.datepicker_' + index).datepicker('show');
    });

    $('.datepicker').on('changeDate', function() {
        var index = $(this).attr('class').split(' ')[2].split('_')[1];
        var date = $(this).val();
        $('#date_' + index).val(date); // Mettre à jour la valeur du champ caché avec la date sélectionnée
    });

    $('.submitBtn').on('click', function() {
        var index = $(this).attr('id').split('_')[1];
        var dateFieldId = $(this).data('date-field');
        var date = $('#'+dateFieldId).val();
        $('#'+dateFieldId).val(date); // Mettre à jour la valeur du champ de date caché
        $('#form_' + index).submit();
    });

    // Récupération du solde depuis la page actuelle
    var soldeText = $('.solde').text();
    if (soldeText) {
        var solde = parseFloat(soldeText.replace('Solde : ', '').replace(/\s/g, '').replace(',', '.')); // Conversion en nombre
        $.ajax({
            type: 'GET',
            url: '/votre-autre-vue',
            data: { solde: solde },
            success: function(response) {
                // Gérer la réponse de la vue
            },
            error: function(xhr, status, error) {
                // Gérer les erreurs
            }
        });
    }
});



// // Fonction pour soumettre deux formulaires consécutivement dans la page rappel

function submitForms(id) {
    const enregistrerForm = document.getElementById('enregistrerForm_' + id);
    const deleteForm = document.getElementById('deleteForm_' + id);

    const formData = new FormData(enregistrerForm);
    fetch(enregistrerForm.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(response => {
        if (response.ok) {
            alert('Récouvrement effectué')
            deleteForm.submit();
        } else {
            alert('Erreur lors de l\'enregistrement.');
        }
    }).catch(error => {
        alert('Erreur lors de l\'enregistrement : ' + error.message);
    });
}
