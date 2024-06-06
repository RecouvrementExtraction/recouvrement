@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{asset('Css/details.css')}}">

<div id="moa">
    <div id="alertMessage" class="alert-message">
        <!-- Le contenu du message d'alerte sera inséré ici -->
    </div>

    <button class="btn btn-primary imprimer-bouton retour-bouton" onclick="retourPagePrecedente()">Retour</button>

    <div class="header">
        <div class="header-left">
            <p>Nom de la société: IGF</p>
            <p>Période: Janvier - décembre 2024</p>
        </div>
        <div class="header-right">
            <p>Extrait de compte compte tiers</p>
            <p>Tenue de compte: XOF</p>
            <p id="dateTirage">Date de tirage: </p>
        </div>
    </div>

    <div class="card">
        <div class="card-body rounded">
            <div class="row justify-content-center rounded">
                <div class="col-md-10">
                    @if(count($data) > 0)
                        <table class="table table-secondary table-ct">
                            <thead>
                                <tr>
                                    <th>Numéro</th>
                                    <th>Intitulé</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center justeM">
                                    <td class="text-center justeM">{{ $data[0]->CT_Num }}</td>
                                    <td class="text-center justeM">{{ $data[0]->CT_Intitule }}</td>
                                </tr>
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Formulaire de recherche -->
            <form method="GET" action="{{ url('/details', ['CT_Num' => $CT_Num]) }}">
                <div class="form-group imprimer-bouton">
                    <input type="text" id="searchInput" placeholder="Recherche...">
                </div>
                <button type="submit" class="btn btn-primary imprimer-bouton recherche-bouton" onclick="recherche()">Rechercher</button>
            </form>

            <table class="table table-bordered border-primary" id="myTable">
                <thead>
                    <tr>
                        <th>Ligne</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>N° facture</th>
                        <th>Libellé</th>
                        <th>Echeance</th>
                        <th>Rétard</th>
                        <th class="col-2">Débit</th>
                        <th class="col-2">Crédit</th>
                        <th>Action</th>
                        <th class="col-2">Message</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $currentCTNum = null;
                        $totalDebit = 0;
                        $totalCredit = 0;
                    @endphp
                    @foreach ($data as $donnee)
                        @php
                            $amount = $donnee->Ec_Montant;
                            $format = number_format($amount, 0, ' ', ' ');
                        @endphp
                        @if(!in_array($donnee->EC_Intitule, session('addedLibelles', [])))
                            <tr data-ct-num="{{ $donnee->CT_Num }}" class="text-center">
                                <td>{{ $donnee->CO_Nom }}</td>
                                <td>{{ $donnee->CT_Telephone }}</td>
                                <td>{{ !empty($donnee->CT_EMail) ? $donnee->CT_EMail : 'emailClient@gmail.com' }}</td>
                                <td>{{ $donnee->EC_RefPiece }}</td>
                                <td>{{ $donnee->EC_Intitule }}</td>
                                <td>{{ (new DateTime($donnee->EC_Echeance))->format('d/m/Y') }}</td>
                                <td class="retard" style="color: #ff0000">
                                    @php
                                        $date1 = new DateTime($donnee->EC_Echeance);
                                        $date2 = new DateTime();
                                        $intervalle = $date2->diff($date1);
                                        $nj = $intervalle->format('%a');
                                        echo ($nj > 100) ? '' : $nj;
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        if ($donnee->EC_sens <= 0) {
                                            echo $format;
                                        } else {
                                            echo 0;
                                        }
                                    @endphp
                                </td>
                                <td class="hidden">
                                    @php
                                        $debitValue = ($donnee->EC_sens <= 0) ? $donnee->Ec_Montant : 0;
                                        $totalDebit += $debitValue;
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        if ($donnee->EC_sens > 0) {
                                            echo $format;
                                        } else {
                                            echo 0;
                                        }
                                    @endphp
                                </td>
                                <td class="hidden">
                                    @php
                                        $creditValue = ($donnee->EC_sens > 0) ? $donnee->Ec_Montant : 0;
                                        $totalCredit += $creditValue;
                                    @endphp
                                </td>
                                <td>
                                    <form action="{{ route('enregistrer_ligne') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="id_agent" value="{{ auth()->user()->id }}">
                                        <input type="hidden" name="idClient" value="{{ $donnee->CT_Num }}">
                                        <input type="hidden" name="ligne" value="{{ $donnee->CO_Nom }}">
                                        <input type="hidden" name="telephone" value="{{ $donnee->CT_Telephone }}">
                                        <input type="hidden" name="email" value="{{ !empty($donnee->CT_EMail) ? $donnee->CT_EMail : 'emailClient@gmail.com' }}">
                                        <input type="hidden" name="num_facture" value="{{ $donnee->EC_RefPiece }}">
                                        <input type="hidden" name="libelle" value="{{ $donnee->EC_Intitule }}">
                                        <input type="hidden" name="echeance" value="{{ $donnee->EC_Echeance }}">
                                        <input type="hidden" name="debit" value="{{ $totalDebit }}">
                                        <input type="hidden" name="credit" value="{{ $totalCredit }}">
                                        <button type="submit" class="btn btn-primary">Recouvre</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="{{ route('enregistrer_commentaire') }}" method="post" class="mx-5">
                                        @csrf
                                        <textarea id="commentaire_{{ $donnee->CT_Num}}" class="commentaire" name="message" cols="20" rows="2"></textarea>
                                        <input type="hidden" name="id_agent" value="{{ auth()->user()->id }}">
                                        <input type="hidden" name="idClient" value="{{ $donnee->CT_Num }}">
                                        <input type="hidden" name="ligne" value="{{ $donnee->CO_Nom }}">
                                        <input type="hidden" name="telephone" value="{{ $donnee->CT_Telephone }}">
                                        <input type="hidden" name="email" value="emailClient@gmail.com">
                                        <input type="hidden" name="num_facture" value="{{ $donnee->EC_RefPiece }}">
                                        <input type="hidden" name="libelle" value="{{ $donnee->EC_Intitule }}">
                                        <input type="hidden" name="echeance" value="{{ $donnee->EC_Echeance }}">
                                        <input type="hidden" name="debit" value="{{ $totalDebit }}">
                                        <input type="hidden" name="credit" value="{{ $totalCredit }}">
                                        <button id="submitBtn_{{ $donnee->CT_Num}}" type="submit" class="btn btn-primary submitBtn" disabled>commentaire</button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $data->links() }}
            </div>

            <div class="mb-3 justify-content-center">
                @php
                    $solde = $totalDebit - $totalCredit;
                @endphp
                <span class="total-debit">Total Débit : {{ number_format($totalDebit, 0, ' ', ' ') }}</span>
                <span class="total-credit">Total Crédit : {{ number_format($totalCredit, 0, ' ', ' ') }}</span>
                <span class="solde">Solde : {{ number_format($solde, 0, ' ', ' ') }}</span>
            </div>
        </div>
    </div>

    <!-- Boutons d'impression et de navigation -->
    <div class="btn-group m-2">
        <button class="btn btn-success imprimer-bouton m-3" onclick="imprimerPage()">Imprimer</button>
        <a href="/client_recouvre/{{ auth()->user()->id }}" class="btn btn-warning imprimer-bouton m-3">Clients récouvrés</a>
        <a href="/client_rappel/{{ auth()->user()->id }}" class="btn btn-info imprimer-bouton m-3">Client à rappeler</a>
    </div>
</div>

<script src="{{ asset('Js/details.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var errorMessage = "{{ session('message') }}";
    if (errorMessage) {
        alert(errorMessage);
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


    // Récupérer tous les champs de commentaire et les boutons de soumission
    var commentaires = document.querySelectorAll('.commentaire');
    var submitBtns = document.querySelectorAll('.submitBtn');

    // Ajouter un écouteur d'événement à chaque champ de commentaire
    commentaires.forEach(function(commentaire, index) {
        commentaire.addEventListener("input", function() {
            var submitBtn = submitBtns[index];
            var commentaireValue = commentaire.value.trim();

            if (commentaireValue !== "") {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        });
    });
</script>
@endsection
