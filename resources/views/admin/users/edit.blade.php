@extends('layouts.app')

@section('content')
<div class="container">
    <button class="btn btn-primary imprimer-bouton retour-bouton mt-2" onclick="retourPagePrecedente()"><i class="bi bi-arrow-left">Retour</i></button>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Modifier <strong> {{$user->name}} </strong></div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')


                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label">{{ __('Nom') }}</label>

                            <div class="col-md-12">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') ??  $user->name}}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label">{{ __('Email') }}</label>

                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') ?? $user->email }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>



                        <div class="roles-section">
                            <h4 class="fw-bold">Rôles</h4>
                            @foreach ($roles as $role)
                                <div class="form-group form-check">
                                    <input type="checkbox" class="form-check-input" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}" {{ in_array($role->id, $user->roles->pluck('id')->toArray()) ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}" class="form-check-label">{{ $role->name }}</label>
                                </div>
                            @endforeach
<hr>
                            <h4 class="fw-bold">Portefeuilles</h4>
                                @foreach ($portefeuillesDisponibles as $portefeuille)
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" name="portefeuilles[]" value="{{ $portefeuille->id }}" id="portefeuille_{{ $portefeuille->id }}" {{ in_array($portefeuille->id, $user->portefeuilles->pluck('id')->toArray()) ? 'checked' : '' }}>
                                        <label for="portefeuille_{{ $portefeuille->id }}" class="form-check-label">{{ $portefeuille->name }}</label>
                                    </div>
                                @endforeach

                        </div>
                        <button type="submit" class="btn btn-primary">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
        // Fonction pour retourner à la page précédente en actualisant
        function retourPagePrecedente() {
        window.history.back();
    }
</script>
@endsection
