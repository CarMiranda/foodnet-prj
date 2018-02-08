import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, ToastController } from 'ionic-angular';

import { MessageriePage } from '../messagerie/messagerie';
import { ApiProvider } from '../../providers/api/api';

@IonicPage()
@Component({
  selector: 'page-product-details',
  templateUrl: 'product-details.html',
})

export class ProductDetailsPage {
  product:any;
  header_data:any;
  description:any;
  owner_data:any;
  constructor(public navCtrl: NavController, public navParams:NavParams, public apiProvider:ApiProvider,public toastCtrl:ToastController) {
    this.product = navParams.get('product')

    //ttttest.000webhostapp.com/usersid=1&showFavorites=1&showGroups=1&showFriends=0
    this.apiProvider.GETData("users?id="+this.product.owner_id).then((result)=>{
      this.owner_data = result;
      console.log(this.owner_data)
      this.header_data.title=this.owner_data.data.fname+"\'s product";
    }, (err) =>{
      let messageERROR:string
      switch(err.status){
        // 0 quand on a pas de connection
        case 0:
          messageERROR='Connexion à l\'api impossible';
          break;
          // exception quand l'api renvoie une exeption: pr l'instant yen a qu'une possible : mdp/login oncorrect
        case "exception" :
          messageERROR=err.data.message;
          break;
      };
      let toast = this.toastCtrl.create({
        message: messageERROR,
        duration: 3000,
        position: 'bottom'
      });
      toast.present();
    });
    // header personnalisé
    this.header_data={isSearch:false,isCamera:true,isProfile:true,title:"\'s product"};
  }

  goMessagerie(){
    this.navCtrl.push(MessageriePage);
  }

}
