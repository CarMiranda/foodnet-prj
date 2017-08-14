import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';
import { DbStorageProvider } from '../../providers/db-storage/db-storage';
import { Platform } from 'ionic-angular';

import { ProductDetailsPage } from '../product-details/product-details';
@IonicPage()
@Component({
  selector: 'page-messagerie',
  templateUrl: 'messagerie.html',
})
export class MessageriePage {

  data: any[];
  data2:any[];
  constructor(public platform: Platform, public navCtrl: NavController, public navParams: NavParams, public dbStorage: DbStorageProvider) {
    this.dbStorage.load(5).then((data : any) => {
      this.data = data.results;
    }, (err) => {
      console.log(err);
    });

    this.dbStorage.load(5).then((data : any) => {
      this.data2 = data.results;
    }, (err) => {
      console.log(err);
    });
  }
  viewProduct(id: string) {
    let idx : number = this.data.findIndex((el) => {
      return el.cell == id;
    });
    console.log(JSON.stringify(this.data[idx]));
    this.navCtrl.push(ProductDetailsPage, {
      'product': this.data[idx]
    });
  }


}
