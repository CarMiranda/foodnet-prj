import { Component } from '@angular/core';
import { NavController, App, NavParams,ToastController } from 'ionic-angular';
import { DbStorageProvider } from '../../providers/db-storage/db-storage';
import { Platform } from 'ionic-angular';
import { ApiProvider } from '../../providers/api/api';

@Component({
  selector: 'page-profile',
  templateUrl: 'profile.html',
})
export class ProfilePage {
  public userDetails: any;
  header_data:any;
  public data:any;
  public dataApi :any;

  constructor(public app: App,public toastCtrl:ToastController, public apiProvider:ApiProvider, public platform: Platform, public navCtrl: NavController, public navParams: NavParams, public dbStorage: DbStorageProvider) {
    this.dbStorage.load(1).then((data : any) => {
      this.userDetails = data.results[0];
    }, (err) => {
      console.log(err);
    });
    this.data =JSON.parse(localStorage.getItem('userToken'));
    //recupDATA de L'api :
    this.apiProvider.GETData("users").then((res)=>{
      this.dataApi=res;
      console.log("ProfilePage : Get success");
      console.log(res);
    },(err)=>{
      console.log("ProfilePage : Get failed"+err);
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
    // header personnalisé
    this.header_data={isSearch:false,isCamera:true,isProfile:false,title:"Mon profil"};
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
