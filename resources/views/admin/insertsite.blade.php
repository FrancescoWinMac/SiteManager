@section('title', 'Inserisci Sito')
@section('path', 'Inserisci Sito')

@extends('masteradmin')

@section('content')
    <div class="box-content">
        <form action="{{ route('add', 'site') }}" method="POST">
            {{ csrf_field() }}
            <div class="table-container">
                <table class="table is-datatable dataTable">
                    <thead>
                    <tr>
                        <th>Dati</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Nome</td>
                        <td><input type="text" id="Name" name="Name" class="form-control" required></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Descrizione</td>
                        <td><input type="text" id="Description" name="Description" class="form-control" required></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Nazione</td>
                        <td><input type="text" id="Country" name="Country" class="form-control" required></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Provincia</td>
                        <td><input type="text" id="Province" name="Province" class="form-control" required></td>
                        <td></td>
                        @if ($errors->has('Province'))
                            <div class="alert alert-danger">
                                <strong>Attenzione! </strong>{{ $errors->first('Province') }}
                            </div>
                        @endif
                    </tr>
                    <tr>
                        <td>Città</td>
                        <td><input type="text" id="City" name="City" class="form-control" required></td>
                        <td></td>
                        @if ($errors->has('City'))
                            <div class="alert alert-danger">
                                <strong>Attenzione! </strong>{{ $errors->first('City') }}
                            </div>
                        @endif
                    </tr>
                    <tr>
                        <td>Via</td>
                        <td><input type="text" id="Street" name="Street" class="form-control"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Civico</td>
                        <td><input type="text" id="StreetNumber" name="StreetNumber" class="form-control"></td>
                        <td></td>
                        @if ($errors->has('StreetNumber'))
                            <div class="alert alert-danger">
                                <strong>Attenzione! </strong>{{ $errors->first('StreetNumber') }}
                            </div>
                        @endif
                    </tr>
                    <tr>
                        <td>CodicePostale</td>
                        <td><input type="text" id="ZipCode" name="ZipCode" class="form-control"></td>
                        <td></td>
                        @if ($errors->has('ZipCode'))
                            <div class="alert alert-danger">
                                <strong>Attenzione! </strong>{{ $errors->first('ZipCode') }}
                            </div>
                        @endif
                    </tr>
                    <tr>
                        <td>Utente</td>
                        <td>
                            <select name="user_id" id="user_id" class="form-control">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->Name }} {{ $user->Surname }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td></td>
                    </tr>

                    @if ($errors->has('Address'))
                        <div class="alert alert-danger">
                            <strong>Attenzione! </strong>{{ $errors->first('Address') }}
                        </div>
                    @endif

                    </tbody>
                </table>
            </div>
            <button id="insert_site" type="submit" class="btn btn-primary">Salva</button>
        </form>
    </div>
@endsection