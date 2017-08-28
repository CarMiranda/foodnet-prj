import { Component } from '@angular/core';
import { NavController, App, NavParams } from 'ionic-angular';
import { DbStorageProvider } from '../../providers/db-storage/db-storage';
import { Platform } from 'ionic-angular';

@Component({
  selector: 'page-profile',
  templateUrl: 'profile.html',
})
export class ProfilePage {
  public userDetails: any;
  public data:any;
  constructor(public app: App, public platform: Platform, public navCtrl: NavController, public navParams: NavParams, public dbStorage: DbStorageProvider) {
    this.dbStorage.load(1).then((data : any) => {
      this.userDetails = data.results[0];
    }, (err) => {
      console.log(err);
    });
    this.data =JSON.parse(localStorage.getItem('authorizationToken'));
  }

  backToWelcome(){
    const root = this.app.getRootNav();
    root.popToRoot();
  }

  logout(){
    localStorage.clear();
    setTimeout(() => this.backToWelcome(),1500);
  }

}
