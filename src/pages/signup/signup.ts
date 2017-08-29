import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';

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
    "action":"PUT",
    "login":false,
    "id":"",
    "username":"",
    "password":"",
    "email":""
  };

  // userData = { "username":"Uname1","password":"Uname1","id":"Uname1",}
  constructor(public navCtrl: NavController, public navParams: NavParams, public apiprovider: ApiProvider) {
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad SignupPage');
  }

  signup(){
    // Api Connection
    //this.apiprovider.postData(this.userData)

    this.navCtrl.push(HomePage);
  }

  signUpFcb(){
    //sign up with fcb account
  }

  signUpLinkedin(){
    //sign up with Linkedin account
  }
}
