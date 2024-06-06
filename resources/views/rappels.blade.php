@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{asset('Css/details.css')}}">


<div id="moa">
    <div class="container my-3">

        <h1 class="text-uppercase text-bg-primary text-center">Liste des Clients à rappeler pour le récouvrement</h1>
    </div>
    <button class="btn btn-primary recherche-bouton" onclick="retourPagePrecedente()">Retour</button>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th>Ligne</th>
                        <th>id_Client</th>
                        <th>Libellé</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>N° facture</th>
                        <th>Débit</th>
                        <th>Crédit</th>
                        <th>Message</th>
                        <th>Action</th>
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
                            <td>{{ $donnee->message }}</td>
                            <td>
                                <button class="btn btn-danger delete-row">Supprimer</button> <!-- Bouton de suppression -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


</div>
 <script src="{{asset('Js/details.js')}}"></script>
@endsection
