import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, App, ToastController,Platform } from 'ionic-angular';
// import { DomSanitizer } from '@angular/platform-browser';

import { DbStorageProvider } from '../../providers/db-storage/db-storage';
import { ApiProvider } from '../../providers/api/api';
import { ProductDetailsPage } from '../product-details/product-details';
import { OtherProfilePage } from '../other-profile/other-profile';

@IonicPage()
@Component({
  selector: 'page-fildactualite',
  templateUrl: 'fildactualite.html',
})
export class FildactualitePage {

  public item:string="";
  isSearchbarOn: boolean =false;
  swipe: number = 0;
  comments:string[];
  header_data:any;
  private temp:any;
  private dataApi :any[];
  private nbAppel : number;

  constructor(private platform : Platform, private apiProvider : ApiProvider,public toastCtrl:ToastController, public app: App, public navCtrl: NavController, public navParams: NavParams) {
    this.comments = ["efzrefz","trololo","kerjg"];
    // header personnalisé
    this.header_data={isSearch:true,isCamera:true,isProfile:true,title:"KooDeFood"};
    platform.ready().then(() => {
      platform.registerBackButtonAction(() => {
      });
    });
    this.nbAppel = 0;
    this.loadData();

  }


  openCommentSection(id: string){
    console.log(id)
    var container = document.getElementById(id);
    for(var i=0;i<3;i++){
        container.insertAdjacentHTML('beforeend','<p>'+this.comments[i]+'</p>');
    }
  }

  doInfinite(infiniteScroll) {
    console.log('Begin async operation');
    setTimeout(() => {
      this.nbAppel=this.nbAppel+1;
      this.loadData();
      infiniteScroll.complete();
    }, 50);
  }

  private loadData(){
    this.apiProvider.GETData("posts?limit=5&offset="+this.nbAppel).then((res)=>{
      this.temp=res;
      console.log(this.nbAppel)
      if (this.nbAppel==0){

        this.dataApi = this.temp.data;
      }else{
        for (var i = 0; i < 3; ++i) {
          this.dataApi.push(this.temp.data[i]);
        }
      }
    },(err)=>{
      let messageERROR:string
      switch(err.status){
        // 0 quand on a pas de connection
        case 0:
          messageERROR='Connexion à l\'api impossible';
          break;
          // exception quand l'api renvoie une exeption: pr l'
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
  }

  showUserProfile(owner_id){
    this.navCtrl.push(OtherProfilePage, {
      'owner_id':owner_id
    });

  }

  showProduct(product){
    console.log(product)
    this.app.getRootNav().push(ProductDetailsPage, {
      'product': product
    });
  }

  doRefresh(refresher) {
    console.log('Begin async operation', refresher);

    setTimeout(() => {
      // clear data, then load a whole new feed
      this.dataApi =[];
      this.nbAppel = 0;
      this.loadData();
      console.log('Async operation has ended');
      refresher.complete();
    }, 3000);
  }



}
