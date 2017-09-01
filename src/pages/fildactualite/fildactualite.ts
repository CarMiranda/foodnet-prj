import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, App, Platform, ToastController } from 'ionic-angular';
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

  private data: any[];
  public item:string="";
  isSearchbarOn: boolean =false;
  swipe: number = 0;
  comments:string[];
  header_data:any;
  private temp:any;
  private dataApi :any[];
  private nbAppel : number;

  constructor(private apiProvider : ApiProvider,public toastCtrl:ToastController, private platform : Platform, public app: App, public navCtrl: NavController, public navParams: NavParams, public dbStorage: DbStorageProvider) {
    this.comments = ["efzrefz","trololo","kerjg"];
    this.dbStorage.load(3).then((data : any) => {
      this.data = data.results;
    }, (err) => {
      console.log(err);
    });
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
        console.log("1ere fois")
      }else{
        for (var i = 0; i < 3; ++i) {
          this.dataApi.push(this.temp.data[i]);
        }
      }
      console.log(this.dataApi)
    },(err)=>{
      let messageERROR:string
      switch(err.status){
        // 0 quand on a pas de connection
        case 0:
          messageERROR='Connexion à l\'api impossible';
          break;
          // exception quand l'api renvoie une exeption: pr l'
        case "exception" :
          messageERROR='exception';
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
    this.app.getRootNav().push(ProductDetailsPage, {
      'product': product
    });
  }

}
