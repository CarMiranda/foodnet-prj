import { Component } from '@angular/core';
import { NavController,ToastController } from 'ionic-angular';
import { HomePage } from '../home/home'
import { ApiProvider } from '../../providers/api/api';
import { Validators, FormBuilder, FormGroup } from '@angular/forms';
import { UserProvider } from '../../providers/user/user';
import { HomePage } from '../home/home';

@IonicPage()
@Component({
  selector: 'page-login',
  templateUrl: 'login.html'
})
export class LoginPage {

  userData = {"action":"POST","login":true,
  "data":{"id":"R","password":"r"}};
  responseData: any;

  constructor(public navCtrl: NavController, public apiprovider:ApiProvider,public toastCtrl:ToastController) {

  }

  login(){
    this.apiprovider.postData("users",this.userData).then((result)=>{
      this.responseData = result;
      console.log(result);
     localStorage.setItem('userToken',JSON.stringify(this.responseData.data));
      this.navCtrl.push(HomePage);
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
  }

  loginfcb(){
    this.navCtrl.push(HomePage);
  }

  loginLinkedin(){
    //login avec compte Linkedin

  }

}
