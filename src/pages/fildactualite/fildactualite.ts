import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, App } from 'ionic-angular';
// import { DomSanitizer } from '@angular/platform-browser';

import { DbStorageProvider } from '../../providers/db-storage/db-storage';

import { ProductDetailsPage } from '../product-details/product-details';

@IonicPage()
@Component({
  selector: 'page-fildactualite',
  templateUrl: 'fildactualite.html',
})
export class FildactualitePage {

  private data: any[];
  public item:string="";
  isSearchbarOn: boolean =false;
  swipe: number = 0;
  comments:string[];
  header_data:any;

  constructor(public app: App, public navCtrl: NavController, public navParams: NavParams, public dbStorage: DbStorageProvider) {
    this.comments = ["efzrefz","trololo","kerjg"];
    this.dbStorage.load(3).then((data : any) => {
      this.data = data.results;
    }, (err) => {
      console.log(err);
    });
    // header personnalisé
    this.header_data={isSearch:true,isCamera:true,isProfile:true,title:"KooDeFood"};
  }

  openCommentSection(id: string){
    let idx : number = this.data.findIndex((el) => {
      return el.cell == id;
    });

    console.log(this.data[idx].cell);

    var container = document.getElementById(this.data[idx].cell);
    for(var i=0;i<3;i++){
        container.insertAdjacentHTML('beforeend','<p>'+this.comments[i]+'</p>');
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

  showProduct(product){
    this.app.getRootNav().push(ProductDetailsPage, {
      'product': product
    });
  }

}
