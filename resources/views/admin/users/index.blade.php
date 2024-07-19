@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-md-12">
            {{-- <div class="my-3" title="Ajouter un agent">
                <a href="{{ route('register') }}" class="btn btn-primary"> <i class="bi bi-plus"></i></a>
            </div> --}}


                <button class="btn btn-primary">
                    <a href="{{ route('register') }}" class="nav-link custom-register-link">
                        {{ __("Ajouter") }}
                    </a>
                </button>


            <div class="card mt-5">
                <div class="card-header"><h3>List des Agents</h3></div>

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
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-success">
                                        {{-- <button class="btn btn-success">Editer</button> --}}
                                        <i class="bi bi-pencil" title="Editer l'agent"></i>
                                    </a>
                                    @endcan

                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-warning" title="voir les factures">
                                        {{-- <button class="btn btn-warning">Factures</button> --}}
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @can('delete-users')
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="post" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger delete-button" title="supprimer l'agent">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @php
                                $ide += 1;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteButtons = document.querySelectorAll('.delete-button');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                if (confirm("Voulez-vous vraiment supprimer cet utilisateur ?")) {
                    this.closest('form').submit();
                }
            });
        });
    });
</script>
@endsection
