import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';

import { LinefeedPage } from '../linefeed/linefeed';
import { TestgglemapsPage } from '../testgglemaps/testgglemaps';
/**
 * Generated class for the FildactualitePage page.
 *
 * See http://ionicframework.com/docs/components/#navigation for more info
 * on Ionic pages and navigation.
 */
@IonicPage()
@Component({
  selector: 'page-fildactualite',
  templateUrl: 'fildactualite.html',
})
export class FildactualitePage {

  constructor(public navCtrl: NavController, public navParams: NavParams) {
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad FildactualitePage');
  }

  go(toPage: string) {
    console.log("Swiped! Going to " + toPage);
    if (toPage === 'Linefeed') {
      this.navCtrl.push(LinefeedPage);
    }
    if (toPage === 'TestGeoloca') {
      this.navCtrl.push(TestgglemapsPage);
    }

  }
}
