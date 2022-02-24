 /* This will let you use the .remove() function later on */
      if (!('remove' in Element.prototype)) {
        Element.prototype.remove = function() {
          if (this.parentNode) {
            this.parentNode.removeChild(this);
          }
        };
      }

     var apiToken = "pk.eyJ1IjoibWF0aWFzYWJ1cnRvIiwiYSI6ImNrYWtmNWI4aDAwZGMzNW4wN3Q2cDhrbWEifQ.P8WlyE_ie40piVBSL2kwrA";
     var defaultMap = [-89.2436391, 13.6894234]; //Coordenas para que cargue el mapa por default
      mapboxgl.accessToken =apiToken;
      

      /**
       * Add the map to the page
       */
      var map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [-69.583335,-35.4744],
        zoom: 13,
        scrollZoom: true
      });
      
      

	  var nav = new mapboxgl.NavigationControl();
    map.addControl(nav, 'top-right');
    
    
  
	  
	

  


	
	function getStores(){
		var jsonData= $.ajax({
			url: 'stores.php',
			dataType: 'json',
			async: false
		}).responseText;
		return jQuery.parseJSON(jsonData);
	}
	
					
	   var stores = getStores();

      /**
       * Assign a unique id to each store. You'll use this `id`
       * later to associate each point on the map with a listing
       * in the sidebar.
       */
      stores.features.forEach(function(store, i) {
        store.properties.id = i;
      });

      /**
       * Wait until the map loads to make changes to the map.
       */
      map.on('load', function(e) {
        /**
         * This is where your '.addLayer()' used to be, instead
         * add only the source without styling a layer
         */
        map.addSource('places', {
          'type': 'geojson',
          'data': stores
        });

        // / **
        //   * Agregue todas las cosas a la página:
        //   * - Los listados de ubicación al costado de la página
        //   * - Los marcadores en el mapa
        //   * /
        buildLocationList(stores);
        addMarkers();
      });

      // / **
      //   * Agregue un marcador al mapa para cada listado de la tienda.
      //   ** /
      function addMarkers() {
        /* For each feature in the GeoJSON object above: */
        stores.features.forEach(function(marker) {
          /* Create a div element for the marker. */
          var el = document.createElement('div');
          /* Assign a unique `id` to the marker. */
          el.id = 'marker-' + marker.properties.id;
          /* Assign the `marker` class to each marker for styling. */
          el.className = 'marker';

          /**
           * Create a marker using the div element
           * defined above and add it to the map.
           **/
          new mapboxgl.Marker(el, { offset: [0, -23] })
            .setLngLat(marker.geometry.coordinates)
            .addTo(map);

          /**
           * Listen to the element and when it is clicked, do three things:
           * 1. Fly to the point
           * 2. Close all other popups and display popup for clicked store
           * 3. Highlight listing in sidebar (and remove highlight for all other listings)
           **/
          el.addEventListener('click', function(e) {
            /* Fly to the point */
            flyToStore(marker);
            /* Close all other popups and display popup for clicked store */
            createPopUp(marker);
            /* Highlight listing in sidebar */
            var activeItem = document.getElementsByClassName('active');
            e.stopPropagation();
            if (activeItem[0]) {
              activeItem[0].classList.remove('active');
            }
            var listing = document.getElementById(
              'listing-' + marker.properties.id
            );
            listing.classList.add('active');
          });
        });
      }

      /**
       * Add a listing for each store to the sidebar.
       **/
      function buildLocationList(data) {
        console.log(data.storeName);
        data.features.forEach(function(store, i) {
          /**
           * Create a shortcut for `store.properties`,
           * which will be used several times below.
           **/
          var prop = store.properties;

          /* Add a new listing section to the sidebar. */
          var listings = document.getElementById('listings');
          var listing = listings.appendChild(document.createElement('div'));
          /* Assign a unique `id` to the listing. */
          listing.id = 'listing-' + prop.id;
          /* Assign the `item` class to each listing for styling. */
          listing.className = 'item';

          /* Add the link to the individual listing created above. */
          var link = listing.appendChild(document.createElement('a'));
          link.href = '#';
          link.className = 'title';
          link.id = 'link-' + prop.id;
          link.innerHTML = prop.storeName;

          /* Add details to the individual listing. */
          var details = listing.appendChild(document.createElement('div'));
          details.innerHTML = prop.address+' '+prop.city;
         
            details.innerHTML += '<br> Teléfonos: ' + prop.phoneFormatted;
          

          /**
           * Listen to the element and when it is clicked, do four things:
           * 1. Update the `currentFeature` to the store associated with the clicked link
           * 2. Fly to the point
           * 3. Close all other popups and display popup for clicked store
           * 4. Highlight listing in sidebar (and remove highlight for all other listings)
           **/
          link.addEventListener('click', function(e) {
            for (var i = 0; i < data.features.length; i++) {
              if (this.id === 'link-' + data.features[i].properties.id) {
                var clickedListing = data.features[i];
                flyToStore(clickedListing);
                createPopUp(clickedListing);
              }
            }
            var activeItem = document.getElementsByClassName('active');
            if (activeItem[0]) {
              activeItem[0].classList.remove('active');
            }
            this.parentNode.classList.add('active');
          });
          
        });
      
      }

      /**
       * Use Mapbox GL JS's `flyTo` to move the camera smoothly
       * a given center point.
       **/
      function flyToStore(currentFeature) {
        map.flyTo({
          center: currentFeature.geometry.coordinates,
          zoom: 15
        });
      }

      /**
       * Create a Mapbox GL JS `Popup`.
       **/
      function createPopUp(currentFeature) {
        var popUps = document.getElementsByClassName('mapboxgl-popup');
        if (popUps[0]) popUps[0].remove();
        var popup = new mapboxgl.Popup({ closeOnClick: false })
          .setLngLat(currentFeature.geometry.coordinates)
          .setHTML(
            '<h3 class="text-center">'+currentFeature.properties.storeName+'</h3>' +
              '<h4>' +
              currentFeature.properties.address +'<br><b>Teléfono: </b>'+currentFeature.properties.phoneFormatted+
              '</h4>'
          )
          .addTo(map);
      }



