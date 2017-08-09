import {
  GoogleMaps,

  GoogleMap,
  CameraPosition,
  GoogleMapsEvent
} from '@ionic-native/google-maps';
import { Component } from '@angular/core';
@Component({
  selector: 'testgglemaps-page',
  templateUrl: 'testgglemaps.html'
})
export class TestgglemapsPage {

constructor(private googleMaps: GoogleMaps) {}

 // Load map only after view is initialized
 ngAfterViewInit() {
 console.log("loading map");
    this.loadMap();
  }

  loadMap() {
  let position: CameraPosition = {
    target: {
      lat: 43.0741904,
      lng: -89.3809802
    },
    zoom: 18,
    tilt: 30
  };
    let element: HTMLElement = document.getElementById('map');
    let map: GoogleMap = this.googleMaps.create(element);
    console.log(" map created");
    map.one(GoogleMapsEvent.MAP_READY).then(
        () => {
          console.log('Map is ready!');
          // Now you can add elements to the map like the marker
      }
    );


      console.log("camera will move");
      map.moveCamera(position);

  }

}
