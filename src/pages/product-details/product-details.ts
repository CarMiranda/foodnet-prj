import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';


@IonicPage()
@Component({
  selector: 'page-product-details',
  templateUrl: 'product-details.html',
})

export class ProductDetailsPage {
  product: any;
  header_data:any;
  constructor(public navCtrl: NavController, public navParams: NavParams) {
    this.product = this.navParams.get('product');
    this.header_data={isSearch:false,isCamera:true,isProfile:true,title:this.product.name.first};
  }

  showProduct() {
    return this.product.location.street + ' ' + this.product.location.city + ' ' + this.product.location.state;
  }

}
