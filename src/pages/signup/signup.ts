import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, ToastController } from 'ionic-angular';

// import {HomePage} from '../home/home';
import { ApiProvider } from '../../providers/api/api';

@IonicPage()
@Component({
  selector: 'page-signup',
  templateUrl: 'signup.html',
})
export class SignupPage {

  //data to signup
  userData = {
  "action":"POST",
  "login":false,
  "data":{
       "uname":"",
       "mail":"",
       "password":"",
       "fname":"",
       "lname":""
      }
  };
  responseData: any;

  constructor(public navCtrl: NavController,public toastCtrl:ToastController, public navParams: NavParams, public apiprovider: ApiProvider) {
  }

  signup(){
      this.apiprovider.postData("users",this.userData).then((result)=>{
        this.responseData = result;
        console.log(result);
        // localstorage.setItem('userToken',)
        let toast = this.toastCtrl.create({
          message: "Le compte a bien été créé.",
          duration: 3000,
          position: 'bottom'
        });
        toast.present();
      }, (err) =>{
        let messageERROR:string;
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
  }

  signUpFcb(){
    //sign up with fcb account
  }

  signUpLinkedin(){
    //sign up with Linkedin account
  }
}
