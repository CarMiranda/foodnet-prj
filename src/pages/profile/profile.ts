import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';
import { DbStorageProvider } from '../../providers/db-storage/db-storage';
import { Platform } from 'ionic-angular';
/**
 * Generated class for the ProfilePage page.
 *
 * See http://ionicframework.com/docs/components/#navigation for more info
 * on Ionic pages and navigation.
 */
@IonicPage()
@Component({
  selector: 'page-profile',
  templateUrl: 'profile.html',
})
export class ProfilePage {

  data: any[];

  constructor(public platform: Platform, public navCtrl: NavController, public navParams: NavParams, public dbStorage: DbStorageProvider) {
    this.dbStorage.load(1).then((data : any) => {
      this.data = data.results;
    }, (err) => {
      console.log(err);
    });
  }

}
