@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{asset('Css/details.css')}}">

<div id="moa">
    <div class="container my-3">
        <h1 class="text-uppercase text-bg-primary text-center">Les clients récouvrés</h1>
    </div>
    {{-- <button class="btn btn-primary" onclick="retourPagePrecedente()">Retour</button> --}}
    <button class="btn btn-primary"><a href="{{route('admin.users.index')}}" class="text-light"><i class="bi bi-arrow-left">Retour</i></a></button>

    <div class="card">
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
                        <th>Montant</th>
                        <th class="hidden">Crédit</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $donnee)
                        @php
                            $amount = $donnee->Ec_Montant;
                            $format = number_format($amount, 0, ' ', ' ');
                        @endphp
                        <tr data-ct-num="{{ $donnee->CT_Num }}" class="text-center">
                            <td>{{ $donnee->ligne }}</td>
                            <td>{{ $donnee->idClient }}</td>
                            <td>{{ $donnee->libelle }}</td>
                            <td>{{ !empty($donnee->CT_EMail) ? $donnee->CT_EMail : 'emailClient@gmail.com' }}</td>
                            <td>{{ $donnee->telephone }}</td>
                            <td>{{ $donnee->num_facture }}</td>
                            <td>{{ number_format($donnee->debit, 0, ' ', ' ') }}</td>
                            <td class="hidden">{{ $donnee->credit }}</td>
                            <td>
                                <form action="/factures_recouvrees/{{$donnee->idClient}}" method="post" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger delete-button"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="{{asset('Js/details.js')}}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteButtons = document.querySelectorAll('.delete-button');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                if (confirm("Voulez-vous vraiment supprimer cete facture ?")) {
                    this.closest('form').submit();
                }
            });
        });
    });

    function retourPagePrecedente() {
        window.history.back();
    }
</script>
@endsection
