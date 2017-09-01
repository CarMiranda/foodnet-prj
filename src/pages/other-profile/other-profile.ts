import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, ToastController, App } from 'ionic-angular';
import { ApiProvider } from '../../providers/api/api';

import { ConversationPage } from '../conversation/conversation';
/**
 * Generated class for the OtherProfilePage page.
 *
 * See http://ionicframework.com/docs/components/#navigation for more info
 * on Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-other-profile',
  templateUrl: 'other-profile.html',
})
export class OtherProfilePage {
  private owner_id : number;
  dataApi:any;
  header_data:any;

  constructor(public app: App, public toastCtrl:ToastController, public apiProvider:ApiProvider,
    public navCtrl: NavController, public navParams: NavParams) {
    this.owner_id = navParams.get('owner_id');
    this.apiProvider.GETData("users?id="+this.owner_id).then((res)=>{
      this.dataApi=res;
      this.header_data.title = "Profil de "+ this.dataApi.data.fname;
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
    // header personnalisé
    this.header_data={isSearch:false,isCamera:true,isProfile:true,title:""};
  }

  startConversation(){
    this.app.getRootNav().push(ConversationPage, {
      'other_user_id':this.owner_id
    });
  }

}
