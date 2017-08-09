import { Component } from '@angular/core';
import { IonicPage, NavController} from 'ionic-angular';

import { Geolocation } from '@ionic-native/geolocation';


import { TestgglemapsPage } from '../testgglemaps/testgglemaps'

@IonicPage()
@Component({
  selector: 'page-test-geoloca',
  templateUrl: 'test-geoloca.html',
})
export class TestGeolocaPage {

  constructor(public navCtrl: NavController, private geolocation:Geolocation) {



  var watchOptions = {timeout : 1000, enableHighAccuracy: false};
  let watch = this.geolocation.watchPosition(watchOptions);
  watch.subscribe((data) => {
      var lat = data.coords.latitude
      var long = data.coords.longitude
      document.getElementById("latitude").innerHTML = "latitude: " + lat
      document.getElementById("longitude").innerHTML = "longitude: " + long
    }

    );

  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad TestGeolocaPage');
  }

  go(toPage: string) {
    console.log("Swiped! Going to " + toPage);
    if (toPage === 'Testgglemaps') {
      this.navCtrl.push(TestgglemapsPage);

    }

  }
}
