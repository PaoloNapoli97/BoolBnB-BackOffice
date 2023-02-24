@extends('layouts.main-dashboard')

@section('content')
    @if (session('message'))
        <div class="alert alert-success m-2">
            {{ session('message') }}
        </div>
    @endif

    <div class="sponsor-container">
        <div class="sponsor-card mb-3 d-flex">
            <div class="sponsor-image-box">
                <img class="sponsor-image {{$sponsor->name}}" src="{{ Vite::asset('resources/img/IMG-20230220-WA0002_origin.png')}}" alt="">
                <div class="sponsor-type {{$sponsor->name}}"><h4>{{$sponsor->name}}</h4></div>
            </div>

            <div class="sponsor-info d-flex justify-content-between w-100">
                <div class="sponsor-dettagli d-flex flex-column ">
                    <h3>Pacchetto {{ $sponsor->name }}</h3>
                    <p>Il pacchetto {{ $sponsor->name }} ti garantisce fino a {{ $sponsor->duration }} Lorem, ipsum dolor sit amet consectetur adipisicing elit. </p>
                    <small class="mt-auto">Durata promozione: {{ $sponsor->duration}} ore</small>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.sponsors.buy', $sponsor->name) }}">
            <select name="apartment_sponsored" id="" class="form-select">
                <option value="" selected hidden>Lista appartamenti</option>
                @if ( $apartments->isEmpty() )
                    <option value="no_apartments" disabled>Pare che tu non abbia ancora creato un appartamento. Creane subito uno!</option>
                @else 
                    @foreach ($apartments as $apartment)
                        @if ($apartment->is_visible) 
                            <option value="{{$apartment->id}}">{{ $apartment->title }} - {{ $apartment->full_address }}</option>
                        @else 
                            <option class="text-muted select_disabled" value="{{$apartment->id}}" disabled>{{ $apartment->title }} - {{ $apartment->full_address }}</option>
                        @endif
                    @endforeach
                @endif
            </select>
            @error('apartment_sponsored')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            <div class="my-3 alert alert-warning">
                <small><strong>Nota bene:</strong> Gli appartamenti non visibili non potranno essere sponsorizzati.</small>
            </div>

            <div class="sponsor-price">
                <button type="submit" class="btn btn-success w-25">Aquista {{ $sponsor->price }} €</button>
                <a href="{{ route('admin.sponsors.index') }}" class="btn btn-secondary">Indietro</a>
            </div>
        </form>
    </div>
@endsection