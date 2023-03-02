    @extends('layouts.main-dashboard')

    
    @section('page-title')
        |   {{ $apartment->title }}
    @endsection

    @section('content')
    <div id="admin-apartments-show">
        <div class="container p-0">
            <div class="card p-4">
                <div class="card-title">
                    <div class="apartment-title pb-3 d-flex justify-content-between align-items-center">
                        <h3 class="col-10 mb-0">{{ $apartment->title }}</h3>
                        {{-- Sponsorizzazione --}}
                        @php
                            $all_sponsors = $apartment->sponsorships->toArray();
                            $sponsor_name = [];
                
                            if( $all_sponsors !== [] ) {
                                foreach ($all_sponsors as $sponsor) {
                                    $date = new DateTime($sponsor['pivot']['sponsor_end']);
                                    $now = new DateTime();
                                    $now->format('Y-m-d H:i:s');  
                    
                                    if($date > $now) {
                                        if( !in_array($sponsor['name'], $sponsor_name) ) {
                                            $sponsor_name[] = $sponsor['name'];
                                        }
                                    }
                                }
                    
                                if ( in_array('Platinum', $sponsor_name) ) {
                                    echo "
                                        <div class='sponsor-badge-icon' style='color: rgb(229, 228, 226)'>
                                            <i class='fa-solid fa-gem me-1'></i> PLATINUM
                                        </div>
                                    ";
                                } else if ( in_array('Gold', $sponsor_name) ) {
                                    echo "
                                        <div class='sponsor-badge-icon' style='color: #FFD700'>
                                            <i class='fa-solid fa-crown me-1'></i> GOLD
                                        </div>
                                    ";
                                } else if( in_array('Silver', $sponsor_name) ) {
                                    echo "
                                        <div class='sponsor-badge-icon text-secondary'>
                                            <i class='fa-solid fa-medal me-1'></i> SILVER
                                        </div>
                                    ";
                                }
                            }
                        @endphp
                    </div>
                </div>    
                <div class="row">
                    <div class="apartment-images col-7 pe-1">
                        <img src="{{ str_contains($apartment->image, 'uploads') ? asset("storage/{$apartment->image}") : $apartment->image}}" alt="">
                    </div>
                    <div class="col-5 ps-1">
                        {{-- sezione mappa --}}
                        <div class="find-us">
                            <div class="img-container">
                                <img id="map-container" src="" class="figure-img img-fluid" alt="">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="apartment-details mt-4 mb-4 row">
                    <div>
                        <h6 class="field p-1 text-center text-white">Statistiche</h6>
                        <div class="bg-light details-body h-100 p-1 d-flex justify-content-around align-items-center">
                            <div>
                                Messaggi ricevuti: 
                                <span class="fw-bold">{{ count($apartment->messages) }}</span>
                            </div>
                            <div>
                                Visualizzazioni appartamento:
                                <span class="fw-bold">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="apartment-details mt-4 mb-5 row">
                    <div class="col-6">
                        <h6 class="field p-1 text-center text-white">Indirizzo</h6>
                        <div class="bg-light details-body h-100 p-1 d-flex justify-content-center align-items-center">
                            <i class="fa-solid fa-map-pin fa-lg fa-fw"></i>
                            <h6 class="mb-0 d-inline-block ms-1"> {{ $apartment->full_address }}</h6>
                        </div>
                    </div>
                    <div class="col-6">
                        <h6 class="field p-1 text-center text-white">Dettagli appartamento</h6>
                        <div class="bg-light details-body h-100 p-1 d-flex justify-content-around align-items-center">
                            <div class="me-3">{{ $apartment->rooms_num }} <i class="fa-solid fa-house fa-lg fa-fw"></i></div>
                            <div class="me-3">{{ $apartment->beds_num }} <i class="fa-solid fa-bed fa-lg fa-fw"></i></div>
                            <div class="me-3">{{ $apartment->baths_num }} <i class="fa-solid fa-shower fa-lg fa-fw"></i></div>
                            <div class="me-3">{{ $apartment->mq }}mq <i class="fa-solid fa-ruler-combined fa-lg fa-fw"></i></div>   
                        </div>
                    </div>
                </div>

                {{-- sezione servizi aggiuntivi singolo appartamento --}}
                <div class="apartment-details services mt-2 mb-4">
                    <h6 class="field p-1 text-center text-white">Servizi:</h6>
                    <div class="bg-light details-body h-100 py-3 d-flex flex-wrap justify-content-start align-items-center">
                        @if ($apartment->services->isEmpty())
                            <span>Non sono presenti servizi aggiuntivi</span>
                        @else
                            @foreach ($apartment->services as $service)
                            <div class="service-box d-flex flex-column justify-content-center align-items-center my-3 text-center">
                                <span class="fa-lg fa-fw">{!! $service->icon !!}</span>
                                {{ $service->name }}
                            </div>  
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Descrizione dell'appartamento --}}
                <div class="apartment-details description services mb-2">
                    <h6 class="field p-1 text-center text-white">Descrizione:</h6>
                    <p class="bg-light details-body h-100 p-2">
                        {{ $apartment->description }}
                    </p>
                </div>
                
                {{-- bottoni --}}
                <div class="d-flex">
                    <a class="btn btn-primary me-2" href="{{route ('admin.apartments.index') }}">Indietro</a>
                    <a class="btn btn-secondary me-2" href="{{route ('admin.apartments.edit', $apartment) }}">Modifica</a>
                    <a class="btn btn-success me-2" href="{{route ('admin.sponsors.index', $apartment) }}">Dai visibilità al tuo contenuto</a>
                </div> 
            </div>
        </div>         
    </div>           
    <div class="d-none">
        <div id="apartment-latitude">{{ $apartment->latitude }}</div>
        <div id="apartment-longitude">{{ $apartment->longitude }}</div>
    </div>
@endsection


<script>
    window.addEventListener("load", (event) => {
        const div = document.getElementById('map-container');
        const latitude = document.getElementById('apartment-latitude');
        const longitude = document.getElementById('apartment-longitude');

        let coords = this.latLonToTileZXY(parseFloat(latitude.innerHTML), parseFloat(longitude.innerHTML), 16);
        // coords = zoom / latitude / longitude

        const mapImage = document.getElementById('map-container').src = `https://api.tomtom.com/map/1/tile/basic/main/16/${coords.split('/')[1]}/${coords.split('/')[2]}.png?tileSize=512&view=Unified&language=NGT&key=S7Di8WQbB2pqxqTH8RYmhO63cZwgtNgp`;
    });

    function latLonToTileZXY(lat, lon, zoomLevel) {
        const MIN_ZOOM_LEVEL = 0
        const MAX_ZOOM_LEVEL = 22
        const MIN_LAT = -85.051128779807
        const MAX_LAT = 85.051128779806
        const MIN_LON = -180.0
        const MAX_LON = 180.0

        if (
            zoomLevel == undefined ||
            isNaN(zoomLevel) ||
            zoomLevel < MIN_ZOOM_LEVEL ||
            zoomLevel > MAX_ZOOM_LEVEL
        ) {
            throw new Error(
            "Zoom level value is out of range [" +
                MIN_ZOOM_LEVEL.toString() +
                ", " +
                MAX_ZOOM_LEVEL.toString() +
                "]"
            )
        }

        if (lat == undefined || isNaN(lat) || lat < MIN_LAT || lat > MAX_LAT) {
            throw new Error(
            "Latitude value is out of range [" +
                MIN_LAT.toString() +
                ", " +
                MAX_LAT.toString() +
                "]"
            )
        }

        if (lon == undefined || isNaN(lon) || lon < MIN_LON || lon > MAX_LON) {
            throw new Error(
            "Longitude value is out of range [" +
                MIN_LON.toString() +
                ", " +
                MAX_LON.toString() +
                "]"
            )
        }

        let z = Math.trunc(zoomLevel)
        let xyTilesCount = Math.pow(2, z)
        let x = Math.trunc(Math.floor(((lon + 180.0) / 360.0) * xyTilesCount))
        let y = Math.trunc(
            Math.floor(
            ((1.0 -
                Math.log(
                Math.tan((lat * Math.PI) / 180.0) +
                    1.0 / Math.cos((lat * Math.PI) / 180.0)
                ) /
                Math.PI) /
                2.0) *
                xyTilesCount
            )
        )

        return z.toString() + "/" + x.toString() + "/" + y.toString()
    }
</script>