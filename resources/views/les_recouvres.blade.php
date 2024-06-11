@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{asset('Css/details.css')}}">
<style>
    .mia{
        width: 150px;
        height: 50px;
    }
</style>




<div class="container my-3">
    <h1 class="text-uppercase text-bg-primary text-center">Les clients récouvrés</h1>
</div>
<div id="moa">
    @if ($data->isNotEmpty())
        @php
            // Récupération du CT_Num du premier élément de la collection
            $firstCTNum = $data->first()->idClient;
        @endphp
        <div class="container my-3 mia">
            <!-- Bouton de retour avec le lien dynamique si les données existent -->
            <a id="retourButton" href="/details/{{ $firstCTNum }}" class="btn btn-primary imprimer-bouton retour-bouton mx-0">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    @else
        <div class="container my-3">
            <!-- Bouton de retour qui appelle une fonction JavaScript si les données n'existent pas -->
            <button class="btn btn-primary imprimer-bouton retour-bouton mx-0" onclick="retourPagePrecedente()">
                <i class="bi bi-arrow-left"></i> Retour
            </button>
        </div>
    @endif
    <div class="card text-center">
        <div class="card-body">
            <table class="table table-bordered" id="myTable">
                <thead class="text-uppercase">
                    <tr>
                        <th>Ligne</th>
                        <th>id_Client</th>
                        <th>Libellé</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>N° facture</th>
                        <th>Débit</th>
                        <th>Crédit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $donnee)
                        @php
                            $amount = $donnee->Ec_Montant;
                            $format = number_format($amount, 0, ' ', ' ');
                        @endphp
                        <tr data-ct-num="{{ $donnee->CT_Num }}">
                            <td>{{ $donnee->ligne }}</td>
                            <td>{{ $donnee->idClient }}</td>
                            <td>{{ $donnee->libelle }}</td>
                            <td>{{ !empty($donnee->CT_EMail) ? $donnee->CT_EMail : 'emailClient@gmail.com' }}</td>
                            <td>{{ $donnee->telephone }}</td>
                            <td>{{ $donnee->num_facture }}</td>
                            <td>{{ $donnee->credit }}</td>
                            <td>{{ $donnee->debit }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const retourButton = document.getElementById('retourButton');
        if (!retourButton.getAttribute('href')) {
            retourButton.addEventListener('click', retourPagePrecedente);
        }
    });

    function retourPagePrecedente() {
        window.history.back();
    }
</script>

 <script src="{{asset('Js/details.js')}}"></script>

@endsection
