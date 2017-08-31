import { Component } from '@angular/core';
import { NavController,ToastController } from 'ionic-angular';
import { HomePage } from '../home/home'
import { ApiProvider } from '../../providers/api/api';
@Component({
  selector: 'page-login',
  templateUrl: 'login.html'
})
export class LoginPage {

  userData = {"action":"POST","login":true,
  "data":{"id":"rj01","password":"rj01"}};
  responseData: any;

  constructor(public navCtrl: NavController, public apiprovider:ApiProvider,public toastCtrl:ToastController) {

  }

  login(){
    this.apiprovider.postData(this.userData,"users").then((result)=>{
      this.responseData = result;
      console.log(this.responseData);
      console.log("connection established");
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
}
