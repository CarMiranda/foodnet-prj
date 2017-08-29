import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, ToastController } from 'ionic-angular';

import {HomePage} from '../home/home';
import { ApiProvider } from '../../providers/api/api';

@IonicPage()
@Component({
  selector: 'page-login',
  templateUrl: 'login.html',
})
export class LoginPage {
  userData = {"action":"PUT","login":true,"id":"Uname1","password":"Uname1"};
  responseData: any;
  loginFailedMessage : "Identifiant ou mot de passe incorrect."
  toast:any;
  constructor(public navCtrl: NavController,public toastCtrl:ToastController, public navParams: NavParams, public apiprovider:ApiProvider) {
    this.toast = this.toastCtrl.create({
     message: this.loginFailedMessage,
     duration: 3000
   });

  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad LoginPage');
  }
  login(){
    //login avec identifiant et password (form?)
    console.log(JSON.stringify(this.userData));
    this.apiprovider.postData(this.userData,"users").then((result)=>{
      this.responseData = result;
      localStorage.setItem('authorizationToken',JSON.stringify(this.responseData.data));
      console.log("responseDATA = "+this.responseData);
      this.navCtrl.push(HomePage);
    }, (err)=>{
      //Connection failed
      console.log("connection failed");
      this.toast.present();
    });
    //this.navCtrl.push(HomePage);
  }

  loginfcb(){
    this.navCtrl.push(HomePage);
  }
  loginLinkedin(){
    //login avec compte Linkedin

  }
}
