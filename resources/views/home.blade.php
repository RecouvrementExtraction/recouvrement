@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{asset('Css/home.css')}}">

<div  class="">
    <h5 class="text-center fw-bold"><u>Solde Global : </u><i>{{ number_format(abs($soldeGlobal), 0, ' ', ' ') }}</i> FCFA</h5>
</div>
<div class="mt-5 mx-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center">
                    <h5>{{__("Liste des clients")}}</h5>
                </div>
                <div class="card-body">
                    {{-- <form id="searchForm" class="mb-3">
                        <div class="form-group">
                            <input type="text" id="searchInput" name="search" class="form-control" placeholder="Rechercher...">
                        </div>
                    </form> --}}

                    @if($soldesParClient->count() != 0)
                        <table class="table table-bordered text-center" id="#">
                            <thead>
                                <tr class="text-center">
                                    <th>N°</th>
                                    <th>Nom</th>
                                    <th>Téléphone</th>
                                    <th>Email</th>
                                    <th>Collaborateur</th>
                                    <th>Action</th>
                                    <th>Solde</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($soldesParClient as $CT_Num => $client)
                                    @if ($client['total'] != 0)
                                        <tr>
                                            <td>{{ $CT_Num }}</td>
                                            <td>{{ $client['CT_Intitule'] }}</td>
                                            <td>{{ $client['CT_Telephone'] }}</td>
                                            <td>{{ $client['CT_EMail'] }}</td>
                                            <td>{{ $client['CO_Nom'] }}</td>
                                            <td>
                                                <a href="/details/{{ $CT_Num }}" class="btn btn-primary" title="Voir les détails"><i class="bi bi-eye"></i></a>
                                                <a href="/facturesClient/{{ $CT_Num }}" class="btn btn-primary" title="voir les factures"><i class="bi-back"></i></a>
                                            </td>
                                            <td>
                                               {{ number_format(abs($client['total']), 0, ' ', ' ') }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Pagination links -->
                        {{ $soldesParClient->links() }}
                    @else
                        <p>Aucune donnée disponible.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('Js/home.js') }}"></script>
@endsection
