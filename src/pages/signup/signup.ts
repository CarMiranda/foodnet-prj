import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, ToastController } from 'ionic-angular';

import {HomePage} from '../home/home';
import { ApiProvider } from '../../providers/api/api';

@IonicPage()
@Component({
  selector: 'page-signup',
  templateUrl: 'signup.html',
})
export class SignupPage {

  //data to signup
  userData = {
       "uname":"Uname",
       "mail":"user.name@test.com",
       "password":"Uname",
       "fname":"User",
       "lname":"Name"
      };

      responseData: any;
  // userData = { "username":"Uname1","password":"Uname1","id":"Uname1",}
  constructor(public navCtrl: NavController,public toastCtrl:ToastController, public navParams: NavParams, public apiprovider: ApiProvider) {
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad SignupPage');
  }

  signup(){
    let params =
      {
      "action":"POST",
      "login":false,
      "data":this.userData
      }
      this.apiprovider.postData(params,"users").then((result)=>{
        this.responseData = result;
        console.log(this.responseData);
        this.navCtrl.push(HomePage);
      }, (err) =>{
        console.log("connection failed");
    let toast2 = this.toastCtrl.create({
  message: 'CF: '+err,
  duration: 3000,
  position: 'bottom'
  });
  toast2.present();
      });

  }

  signUpFcb(){
    //sign up with fcb account
  }

  signUpLinkedin(){
    //sign up with Linkedin account
  }
}
