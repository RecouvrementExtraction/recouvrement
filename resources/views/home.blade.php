@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{asset('Css/home.css')}}">
<div class="mt-5 mx-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __("Tableau d'affichage") }}</div>
                <div class="card-body">
                    <form id="searchForm" class="mb-3">
                        <div class="form-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Rechercher...">
                        </div>
                        <button type="submit" class="btn btn-primary retour-bouton">Rechercher</button>
                    </form>
                    @if(count($data) > 0)
                    <table class="table table-bordered" id="myTable" >
                        <thead class=" text-cente">
                            <tr>
                                <th>Nom</th>
                                <th>Téléphone</th>
                                <th>Email</th>
                                <th>Ligne</th>
                                <th>Libellé</th>
                                {{-- <th>N° Facture</th> --}}
                                <th class="col-1">Action</th>
                                <th>Solde</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $currentCTNum = null;
                                $totalDebit = 0;
                                $totalCredit = 0;
                                $currentSolde = 0;
                            @endphp

                            @foreach($data as $item)
                                @if($currentCTNum !== $item->CT_Num)
                                    {{-- Fermer la ligne précédente avant d'ouvrir une nouvelle --}}
                                    @if($currentCTNum !== null)
                                        <td>{{ $totalDebit - $totalCredit }}</td>
                                        </tr>
                                    @endif

                                    {{-- Ouvrir une nouvelle ligne --}}
                                    <tr>
                                        <td>{{ $item->CT_Intitule }}</td>
                                        <td>{{ $item->CT_Telephone }}</td>
                                        {{-- <td>{{ $item->CT_EMail }}</td> --}}
                                        <td>{{ !empty($item->CT_EMail) ? $item->CT_EMail : 'emailClient@gmail.com' }}</td>
                                        <td>{{ $item->CO_Nom }}</td>
                                        <td>{{ $item->EC_Intitule }}</td>
                                        {{-- <td>{{ $item->EC_RefPiece }}</td> --}}

                                        <td>
                                            <a href="/details/{{$item->CT_Num}}" class="btn btn-primary" >voir les factures</a>
                                        </td>

                                        @php
                                            // Réinitialise les totaux pour la nouvelle facture
                                            $totalDebit = 0;
                                            $totalCredit = 0;
                                            $currentSolde = 0;
                                            $currentCTNum = $item->CT_Num;

                                            // Vérifie le EC_sens pour déterminer le débit ou le crédit
                                            if ($item->EC_sens > 0) {
                                                $totalCredit += $item->Ec_Montant;
                                            } else {
                                                $totalDebit += $item->Ec_Montant;
                                            }
                                            $currentSolde += $item->Ec_Montant;
                                        @endphp
                                @else
                                    @php
                                        // Vérifie le EC_sens pour déterminer le débit ou le crédit
                                        if ($item->EC_sens > 0) {
                                            $totalCredit += $item->Ec_Montant;
                                        } else {
                                            $totalDebit += $item->Ec_Montant;
                                        }
                                        $currentSolde += $item->Ec_Montant;
                                    @endphp
                                @endif
                            @endforeach

                            {{-- Fermer la dernière ligne --}}
                            <td>{{ $totalDebit - $totalCredit }}</td>
                            {{-- <td> {{ number_format($solde, 0, ' ', ' ') }}</td> --}}
                            </tr>
                        </tbody>
                    </table>
                    @else
                    <p>Aucune donnée disponible.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
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

</script>
@endsection
