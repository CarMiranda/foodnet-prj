import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';
import { DbStorageProvider } from '../../providers/db-storage/db-storage';
import { Platform } from 'ionic-angular';

@Component({
  selector: 'page-profile',
  templateUrl: 'profile.html',
})
export class ProfilePage {
  public userDetails: any;

  constructor(public platform: Platform, public navCtrl: NavController, public navParams: NavParams, public dbStorage: DbStorageProvider) {
    this.dbStorage.load(1).then((data : any) => {
      this.userDetails = data.results[0];
    }, (err) => {
      console.log(err);
    });
  }

  logout(){
    
  }

}
