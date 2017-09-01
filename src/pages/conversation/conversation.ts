import { Component } from '@angular/core';
import { IonicPage } from 'ionic-angular';
import { NavController, NavParams, ToastController } from 'ionic-angular';

import { ApiProvider } from '../../providers/api/api';
// import { FakeCommentsProvider } from '../../providers/fake-comments/fake-comments';
// import { Keyboard } from '@ionic-native/keyboard';
// import { Events } from 'ionic-angular';


@IonicPage()
@Component({
  selector: 'page-conversation',
  templateUrl: 'conversation.html',
})

// dans fake-rest-api json-server --watch "src/assets/data.json"

export class ConversationPage {
  header_data:any;
  messages:any[];
  newMessage = {content:''}
  other_user_id:number;
  dataApi:any;

  constructor(public toastCtrl:ToastController, public apiProvider:ApiProvider,
    public navCtrl: NavController, public navParams: NavParams) {
      this.other_user_id = navParams.get('other_user_id');
      this.apiProvider.GETData("users?id="+this.other_user_id).then((res)=>{
        this.dataApi=res;
        this.header_data.title = this.dataApi.data.fname+' '+this.dataApi.data.lname;
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

  ionViewDidLoad() {
    // this.keyboard.onKeyboardShow().subscribe(() => this.event.publish('hideTabs'));
    // this.keyboard.onKeyboardHide().subscribe(() => this.event.publish('showTabs'));
  }

  focus(){
    console.log("keyboard on");
  }
  blur(){
    console.log("keyboard oFF");
  }

  submitMessage(){
    console.log('submitting message : '+this.newMessage.content);
  }

}
