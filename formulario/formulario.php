<?php
include_once 'header.php';
include 'locations_model.php';
//get_unconfirmed_locations();exit;
?>
    <style>

        input[type=text], select {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type=submit] {
            width: 100%;
            background-color: #0000;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type=submit]:hover {
            background-color: #222A3F;
        }

        .container {
            border-radius: 12px;
            box-shadow: 0 0 2px rgba(41, 40, 40, 0.569);
            background: rgba(0, 0, 0, 0.479);
            color: #ffffff;
            padding: 20px;
            margin-left: 20%;
            width:50%
        }
        #map { width: auto;height: 500px; }
        .geocoder {
            width: auto;}
        .marker {
            border: none;
            cursor: pointer;
            height: 56px;
            width: 56px;
            background-image: url(../imagenes/marker.png);
            background-color: rgba(0, 0, 0, 0);
        }
    </style>

    <h3 class= "titulo">Crear marcador Deseado</h3>

    <div class="container">
        <form action="" id="signupForm">
            <label for="lat">Latitud</label>
            <input type="text" id="lat" name="lat" placeholder="Ingrese la Latitud">
            <label for="lng">longitud</label>
            <input type="text" id="lng" name="lng" placeholder="Ingrese la Longitud">
           
            <label for="direccion">Direccion</label>
            <input type="text" id="adress" name="direccion" placeholder="Ingrese la Direccion">
            <label for="nombre">Nombre del lugar</label>
            <input type="text" id="storeName" name="lng" placeholder="Ingrese el Nombre del Lugar">

            <label for="direccion">numero de telefono</label>
            <input type="text" id="phoneFormatted" name="direccion" placeholder="Ingrese el numero de telefono">
            <label for="nombre">ciudad</label>
            <input type="text" id="city" name="lng" placeholder="Ingrese la ciudad">
       

            <input type="submit" value="Ingresar" >
        </form>
    </div>

    <div class="geocoder">
        <div id="geocoder" ></div>
    </div>

    <div id="map"></div>


    <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.48.0/mapbox-gl.js'></script>
    <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.48.0/mapbox-gl.css' rel='stylesheet' />
    <style>
    </style>

    <script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.3.0/mapbox-gl-geocoder.min.js'></script>
    <link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.3.0/mapbox-gl-geocoder.css' type='text/css' />

    <script>

        var saved_markers = <?= get_saved_locations() ?>;
        var user_location = [-69.583335,-35.4744];
        mapboxgl.accessToken = 'pk.eyJ1IjoiZmFraHJhd3kiLCJhIjoiY2pscWs4OTNrMmd5ZTNra21iZmRvdTFkOCJ9.15TZ2NtGk_AtUvLd27-8xA';
        var map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v9',
            center: user_location,
            zoom: 13
        });
        //  geocoder here
        var geocoder = new MapboxGeocoder({
            accessToken: mapboxgl.accessToken,
          
        });

        var marker ;
           
        // una ves que el mapa haya cargado agrega una capa origen(donde va a aparecer el punto por defecto)
        
        map.on('load', function() {
            addMarker(user_location,'load');
            add_markers(saved_markers);

            // al buscar una ubicacion en el buscador de mapbox nos mostrara una alerta si se encontro.
            geocoder.on('result', function(ev) {
                alert("Ubicacion Encontrada con exito!");
                console.log(ev.result.center);

            });
        });
        // al hacer click colocamos el marcador rojo en la posicion deseada mostrando la lat y lng
        map.on('click', function (e) {
            marker.remove();
            addMarker(e.lngLat,'click');
            //console.log(e.lngLat.lat);
            document.getElementById("lat").value = e.lngLat.lat;
            document.getElementById("lng").value = e.lngLat.lng;

        });

        function addMarker(ltlng,event) {

            if(event === 'click'){
                user_location = ltlng;
            }
            marker = new mapboxgl.Marker({draggable: true,color:"#d02276"})
                .setLngLat(user_location)
                .addTo(map)
                .on('dragend', onDragEnd);
        }
        // toma los datos guardados en la bd y añade los marcadores con las cordenadas dichas
        function add_markers(coordinates) {

            var geojson = (saved_markers == coordinates ? saved_markers : '');

            console.log(geojson);
            // añaadir marcador al mapa
            geojson.forEach(function (marker) {
                console.log(marker);
                // hace un marcador para cada elemento y lo agrega al mapa
                new mapboxgl.Marker()
                    .setLngLat(marker)
                    .addTo(map);
            });

        }
            // la funcion ondragend toma los valores de lat y long del mapa y los muestra en el formulario
        function onDragEnd() {
            var lngLat = marker.getLngLat();
            document.getElementById("lat").value = lngLat.lat;
            document.getElementById("lng").value = lngLat.lng;
            console.log('lng: ' + lngLat.lng + '<br />lat: ' + lngLat.lat);
        }
            // al apretar guardar en el formulario, esta funcion guarda los datos en la bd
        $('#signupForm').submit(function(event){
            event.preventDefault();
            var lat = $('#lat').val();
            var lng = $('#lng').val();
            var url = 'locations_model.php?add_location&lat=' + lat + '&lng=' + lng;
            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function(data){
                    alert(data);
                    location.reload();
                }
            });
        });

        document.getElementById('geocoder').appendChild(geocoder.onAdd(map));

    </script>



<?php
include_once 'footer.php';

?>