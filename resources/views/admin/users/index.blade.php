@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="my-3">
                <a href="/ajoute_user" class="btn btn-primary">Ajouter un utilisateur</a>
            </div>
            <div class="card">
                <div class="card-header">List des Agents</div>

                <div class="card-body">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôles</th>
                            <th>Portefeuilles</th>
                            <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $ide = 1;
                            @endphp
                            @foreach ($users as $user)
                            <tr>
                                <th scope="row"> {{$ide}} </th>
                                <td> {{$user->name}} </td>
                                <td> {{$user->email}} </td>
                                <td> {{ implode(' / ',$user->roles()->get()->pluck('name')->toArray()) }} </td>
                                <td> {{ implode(' / ',$user->portefeuilles()->get()->pluck('name')->toArray()) }} </td>
                                <td>
                                    @can('edit-users')
                                    <a href="{{route('admin.users.edit', $user->id)}}"><button class="btn btn-success">Editer</button></a>
                                    @endcan
                                    {{-- permet de masquer le lien vers les utilisateurs si c'est pas un admin connecté --}}

                                    <a href="{{route('admin.users.show', $user->id)}}"><button class="btn btn-warning">Factures</button></a>

                                    @can('delete-users')
                                    <form action="{{route('admin.users.destroy', $user->id)}}" method="post" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger">Supprimer</button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @php
                                $ide +=1;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
