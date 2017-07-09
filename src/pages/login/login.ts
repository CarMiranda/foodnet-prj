import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';
import { Validators, FormBuilder, FormGroup } from '@angular/forms';
import { UserProvider } from '../../providers/user/user';
import { HomePage } from '../home/home';

@IonicPage()
@Component({
  selector: 'page-login',
  templateUrl: 'login.html',
})
export class LoginPage {

  loginForm : FormGroup;

  constructor(public navCtrl: NavController, public navParams: NavParams, public user: UserProvider, private formBuider: FormBuilder) {
    this.loginForm = this.formBuider.group({
      user: ['', Validators.required],
      password: ['', Validators.required]
    });
  }

  login() {
    let self = this;
    this.user.login().then((user) => {
      self.navCtrl.setRoot(HomePage);
    });
  }
}
