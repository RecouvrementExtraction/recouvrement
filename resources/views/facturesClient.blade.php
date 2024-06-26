@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('Css/details.css') }}">

<div id="moa">
    <div id="alertMessage" class="alert-message">
        <!-- Le contenu du message d'alerte sera inséré ici -->
    </div>

    <button class="btn btn-primary imprimer-bouton"><a href="/home" class="text-light"><i class="bi bi-arrow-left">Retour</i></a></button>

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
            <form method="GET" action="{{ url('/details', ['CT_Num' => $CT_Num]) }}">
                <div class="form-group imprimer-bouton p-1">
                    <input type="text" id="searchInput" placeholder="Recherche...">
                </div>
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
                        <th>Débit</th>
                        <th>Crédit</th>
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
                            <tr data-ct-num="{{ $donnee->CT_Num }}">
                                <td class="table-cell">{{ $donnee->CO_Nom }}</td>
                                <td class="table-cell">{{ $donnee->CT_Telephone }}</td>
                                <td class="table-cell">{{ !empty($donnee->CT_EMail) ? $donnee->CT_EMail : 'emailClient@gmail.com' }}</td>
                                <td class="table-cell">{{ $donnee->EC_RefPiece }}</td>
                                <td class="table-cell">{{ $donnee->EC_Intitule }}</td>
                                <td class="table-cell">{{ (new DateTime($donnee->EC_Echeance))->format('d/m/Y') }}</td>
                                <td class="retard" style="color: #ff0000">
                                    @php
                                        $date1 = new DateTime($donnee->EC_Echeance);
                                        $date2 = new DateTime();
                                        $intervalle = $date2->diff($date1);
                                        $nj = $intervalle->format('%a');
                                        echo ($nj > 100) ? '+100' : $nj;
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
                    $solde1 = $totalDebit - $totalCredit;
                @endphp
                <span class="total-debit">Total Débit : {{ number_format($totalDebit, 0, ' ', ' ') }}</span>
                <span class="total-credit">Total Crédit : {{ number_format($totalCredit, 0, ' ', ' ') }}</span>
                <span class="solde">Solde : {{ number_format($solde1, 0, ' ', ' ') }}</span>
            </div>
        </div>
    </div>

    <!-- Boutons d'impression et de navigation -->
    <div class="btn-group m-2">
        <button class="btn btn-success imprimer-bouton" onclick="imprimerPage()" title="Imprimer la page"><i class="bi bi-printer"></i></button>
        {{-- <a href="/client_recouvre/{{ auth()->user()->id }}" class="btn btn-warning imprimer-bouton m-3" title="Factures récouvrées"><i class="bi bi-people"></i></a> --}}
        {{-- <a href="/client_rappel/{{ auth()->user()->id }}" class="btn btn-info imprimer-bouton m-3" title="Clients à rappeler"><i class="bi bi-chat-dots"></i></a> --}}
    </div>
</div>

<script src="{{ asset('Js/details.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>
    var errorMessage = "{{ session('message') }}";
    if (errorMessage) {
        alert(errorMessage);
    }


</script>
@endsection
