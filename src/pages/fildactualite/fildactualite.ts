import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';

import { LinefeedPage } from '../linefeed/linefeed';
import { DbStorageProvider } from '../../providers/db-storage/db-storage';
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
  private data: any[];
  private comments:string[];
  

  constructor(public navCtrl: NavController, public navParams: NavParams, public dbStorage: DbStorageProvider) {
    this.comments = ["YOPOLO","trucbidule","wlalala","gnagnagna"];
    this.dbStorage.load(3).then((data : any) => {
      this.data = data.results;
    }, (err) => {
      console.log(err);
    });
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad FildactualitePage');
  }

  openCommentSection(){
    var container = document.getElementById("commentSection");
    for(var i=0;i<3;i++){
      container.appendChild(document.createTextNode(this.comments[i]));
    }
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

  doInfinite(infiniteScroll) {
    console.log('Begin async operation');

    setTimeout(() => {
      this.dbStorage.load(3).then((res : any) => {
        var i;
        for (i = 0; i < 3; ++i) {
          this.data.push(res.results[i]);
        }
     }, (err) => {
        console.log(err);
      });
      infiniteScroll.complete();
    }, 50);
  }

}
