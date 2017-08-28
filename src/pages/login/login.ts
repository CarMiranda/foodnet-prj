import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';

import {HomePage} from '../home/home';
import { ApiProvider } from '../../providers/api/api';
/**
 * Generated class for the LoginPage page.
 *
 * See http://ionicframework.com/docs/components/#navigation for more info
 * on Ionic pages and navigation.
 */
@IonicPage()
@Component({
  selector: 'page-login',
  templateUrl: 'login.html',
})
export class LoginPage {
  userData = { "action":"PUT","login":true,"id":"Uname1","password":"Uname1"};
  responseData: any;
  constructor(public navCtrl: NavController, public navParams: NavParams, public apiprovider:ApiProvider) {
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
    });
    //this.navCtrl.push(HomePage);
  }

  loginfcb(){
    //login avec compte fcb
  }
  loginLinkedin(){
    //login avec compte Linkedin

  }
}
