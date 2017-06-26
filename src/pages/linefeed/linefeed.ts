import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';
import { Platform } from 'ionic-angular';
import { DbStorageProvider } from '../../providers/db-storage/db-storage';
import { ProductDetailsPage } from '../product-details/product-details';

@IonicPage()
@Component({
  selector: 'page-linefeed',
  templateUrl: 'linefeed.html',
})
export class LinefeedPage {
  data: any[];

  constructor(public platform: Platform, public navCtrl: NavController, public navParams: NavParams, public dbStorage: DbStorageProvider) {
    this.dbStorage.load(10).then((data : any) => {
      this.data = data.results;
    }, (err) => {
      console.log(err);
    });
  }

  doInfinite(): Promise<any> {
    console.log('Heading to infinite scroll...');
    return new Promise((resolve) => {
      setTimeout(() => {
        console.log('Into promise...');
        this.dbStorage.load(10).then((res : any) => {
          var i;
          for (i = 0; i < 10; ++i) {
            this.data.push(res.results[i]);
          }
          console.log('RESOLVED!');
          resolve();
        }, (err) => {
          console.log(err);
        });
      }, 50);
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
