@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{asset('Css/home.css')}}">
<div class="mt-5 mx-5">
    <div class="row justify-content-center">
        {{-- <h1>Soldes par Client</h1> --}}
        {{-- @foreach ($soldesParClient as $CT_Num => $client)
        @if ($client['total'] != 0)
            <h2>Client {{ $CT_Num }} - {{ $client['CT_Intitule'] }}</h2>
            <p><strong>Téléphone:</strong> {{ $client['CT_Telephone'] }}</p>
            <p><strong>Email:</strong> {{ $client['CT_EMail'] }}</p>
            <p><strong>Nom Collaborateur:</strong> {{ $client['CO_Nom'] }}</p>

            <table border="1">
                <thead>
                    <tr>
                        <th>EC_Intitule</th>
                        <th>Ec_Montant</th>
                        <th>EC_Lettre</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php $prevIntitule = null; @endphp
                    @foreach ($client['details'] as $detail)
                        @if ($prevIntitule !== $detail['EC_Intitule'])
                            <tr>
                                <td>{{ $detail['EC_Intitule'] }}</td>
                                <td>{{ $detail['Ec_Montant'] }}</td>
                                <td>{{ $detail['EC_Lettre'] }}</td>
                                <td>
                                    <a href="/details/{{$CT_Num}}" class="btn btn-primary">Voir les factures</a>
                                </td>
                            </tr>
                            @php $prevIntitule = $detail['EC_Intitule']; @endphp
                        @endif
                    @endforeach
                </tbody>
            </table>

            <p><strong>Total Débit:</strong> {{ $client['totalDebit'] }}</p>
            <p><strong>Total Crédit:</strong> {{ $client['totalCredit'] }}</p>
            <p><strong>Solde Courant:</strong> {{ $client['total'] }}</p>

            <hr>
        @endif
        @endforeach --}}

        <div class="mt-5 mx-5">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header text-center">{{ __("Liste des clients") }}</div>
                        <div class="card-body">
                            <form id="searchForm" class="mb-3">
                                <div class="form-group">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Rechercher...">
                                </div>

                            </form>

                            @if(count($soldesParClient) > 0)
                                <table class="table table-bordered text-center" id="myTable">
                                    <thead>
                                        <tr class="text-center">
                                            <th class="col-2">N°</th>
                                            <th class="col-2">Nom</th>
                                            <th class="col-2">Téléphone</th>
                                            <th class="col-2">Email</th>
                                            <th class="col-2">Ligne</th>
                                            <th class="col-1">Action</th>
                                            <th class="col-2">Solde</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($soldesParClient as $CT_Num => $client)
                                            @if ($client['total'] != 0)
                                                @php $firstRow = true; @endphp
                                                @foreach ($client['details'] as $detail)
                                                    @if ($firstRow)
                                                        <tr>
                                                            <td rowspan="{{ count($client['details']) }}">{{ $CT_Num }}</td>
                                                            <td rowspan="{{ count($client['details']) }}">{{ $client['CT_Intitule'] }}</td>
                                                            <td rowspan="{{ count($client['details']) }}">{{ $client['CT_Telephone'] }}</td>
                                                            <td rowspan="{{ count($client['details']) }}">{{ $client['CT_EMail'] }}</td>
                                                            <td rowspan="{{ count($client['details']) }}">{{ $client['CO_Nom'] }}</td>
                                                            <td rowspan="{{ count($client['details']) }}">
                                                                <a href="/details/{{ $CT_Num }}" class="btn btn-primary" title="voir les factures"><i class="bi bi-eye"></i></a>
                                                                <a href="/facturesClient/{{ $CT_Num }}" class="btn btn-primary" title="voir les factures"><i class="bi-back"></i></a>
                                                            </td>
                                                            <td rowspan="{{ count($client['details']) }}">{{ abs($client['total']) }}</td>
                                                        </tr>
                                                        @php $firstRow = false; @endphp

                                                    @endif
                                                @endforeach
                                                {{-- <tr>
                                                    <td colspan="7">
                                                        <p><strong>Total Débit:</strong> {{ $client['totalDebit'] }}</p>
                                                        <p><strong>Total Crédit:</strong> {{ $client['totalCredit'] }}</p>
                                                        <p><strong>Solde Courant:</strong> {{ $client['total'] }}</p>
                                                    </td>
                                                </tr> --}}
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>Aucune donnée disponible.</p>
                            @endif

                        </div>
                    </div>
                    {{-- <h1>Recouvrements par Client</h1>
                <table border="1">
                    <thead>
                        <tr>
                            <th>ID Client</th>
                            <th>Total Crédit</th>
                            <th>Total Débit</th>
                            <th>Solde</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recouvrements as $recouvrement)
                            <tr>
                                <td>{{ $recouvrement->idClient }}</td>
                                <td>{{ $recouvrement->total_credit }}</td>
                                <td>{{ $recouvrement->total_debit }}</td>
                                <td>{{ $recouvrement->solde }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table> --}}
                </div>
            </div>
        </div>


    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('Js/home.js') }}"></script>
@endsection
