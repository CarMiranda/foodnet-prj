import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, ToastController } from 'ionic-angular';
import { ApiProvider } from '../../providers/api/api';

@IonicPage()
@Component({
  selector: 'page-liste-postde-user',
  templateUrl: 'liste-postde-user.html',
})
export class ListePostdeUserPage {

  header_data:any;
  user_id:number;
  dataApi:any;
  temp:any;
  constructor(public navCtrl: NavController, public toastCtrl:ToastController, public apiProvider:ApiProvider, public navParams: NavParams) {
    this.user_id = navParams.get('user_id');
    this.header_data={isSearch:true,isCamera:true,isProfile:false,title:"Post"};
    this.loadData();
  }

  loadData(){
    this.apiProvider.GETData("posts/user?id="+this.user_id).then((res)=>{
      this.temp=res;
      this.dataApi= this.temp.data;
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
}
