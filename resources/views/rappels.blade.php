@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{asset('Css/details.css')}}">
<style>
    .mia{
        width: 150px;
        height: 50px;
    }
</style>


<div id="moa">
    <div class="container my-3">

        <h1 class="text-uppercase text-bg-primary text-center">Liste des Clients à rappeler pour le récouvrement</h1>
    </div>
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
    {{-- <button class="btn btn-primary recherche-bouton" onclick="retourPagePrecedente()">Retour</button> --}}

    <div class="card text-center">
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
                        <th>Montant</th>
                        <th class="hidden">Crédit</th>
                        <th>Date</th>
                        <th>Action</th> <!-- Nouvelle colonne pour les actions -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $donnee)
                        <tr id="ligne_{{ $donnee->id }}">
                            <td>{{ $donnee->ligne }}</td>
                            <td>{{ $donnee->idClient }}</td>
                            <td>{{ $donnee->libelle }}</td>
                            <td>{{ !empty($donnee->CT_EMail) ? $donnee->CT_EMail : 'emailClient@gmail.com' }}</td>
                            <td>{{ $donnee->telephone }}</td>
                            <td>{{ $donnee->num_facture }}</td>
                            <td>{{ number_format($donnee->debit, 0, ' ', ' ') }}</td>
                            <td class="hidden">{{ $donnee->credit }}</td>
                            <td>{{ $donnee->message }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <form id="deleteForm_{{ $donnee->id }}" action="{{ route('supprimer_ligne', $donnee->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <form id="enregistrerForm_{{ $donnee->id }}" action="{{ route('enregistrer_ligne') }}" method="post" class="text-center">
                                        @csrf
                                        <input type="hidden" name="id_agent" value="{{ auth()->user()->id }}">
                                        <input type="hidden" name="idClient" value="{{ $donnee->idClient}}">
                                        <input type="hidden" name="ligne" value="{{ $donnee->ligne }}">
                                        <input type="hidden" name="telephone" value="{{ $donnee->telephone }}">
                                        <input type="hidden" name="email" value="{{ !empty($donnee->CT_EMail) ? $donnee->CT_EMail : 'emailClient@gmail.com' }}">
                                        <input type="hidden" name="num_facture" value="{{  $donnee->num_facture}}">
                                        <input type="hidden" name="libelle" value="{{ $donnee->libelle }}">
                                        <input type="hidden" name="debit" value="{{ $donnee->debit }}">
                                        <input type="hidden" name="credit" value="{{ $donnee->credit }}">
                                        <button type="button" class="btn btn-info btn-sm" onclick="submitForms('{{ $donnee->id }}')">
                                            <i class="bi bi-check-circle" title="Récouvrer et Supprimer"></i>
                                        </button>
                                    </form>
                                </div>
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
            const retourButton = document.getElementById('retourButton');
            if (!retourButton.getAttribute('href')) {
                retourButton.addEventListener('click', retourPagePrecedente);
            }
        });




        // function submitForms(id) {
        //     const enregistrerForm = document.getElementById('enregistrerForm_' + id);
        //     const deleteForm = document.getElementById('deleteForm_' + id);

        //     const formData = new FormData(enregistrerForm);
        //     fetch(enregistrerForm.action, {
        //         method: 'POST',
        //         body: formData,
        //         headers: {
        //             'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //         }
        //     }).then(response => {
        //         if (response.ok) {
        //             alert('Récouvrement effectué')
        //             deleteForm.submit();
        //         } else {
        //             alert('Erreur lors de l\'enregistrement.');
        //         }
        //     }).catch(error => {
        //         alert('Erreur lors de l\'enregistrement : ' + error.message);
        //     });
        // }
 </script>
@endsection
