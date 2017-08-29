import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';

import { MessageriePage } from '../messagerie/messagerie';

@IonicPage()
@Component({
  selector: 'page-product-details',
  templateUrl: 'product-details.html',
})

export class ProductDetailsPage {
  product:any;
  header_data:any;
  description:any;
  constructor(public navCtrl: NavController, public navParams:NavParams) {
    this.product = navParams.get('product');
    this.description=JSON.stringify(this.product.picture);
    // header personnalisé
    this.header_data={isSearch:false,isCamera:true,isProfile:true,title:this.product.name.first+"\'s product"};

  }

  goMessagerie(){
    this.navCtrl.push(MessageriePage);
  }

}
