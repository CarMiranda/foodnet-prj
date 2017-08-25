import { Component } from '@angular/core';
import { NavParams, NavController } from 'ionic-angular';

import { FildactualitePage } from '../fildactualite/fildactualite';
import { MessageriePage } from '../messagerie/messagerie';

@Component({
  selector: 'page-home',
  templateUrl: 'home.html'
})
export class HomePage {
  tab1Root = FildactualitePage;
  tab2Root = MessageriePage;

  constructor(public navCtrl: NavController, public navParams: NavParams) {

  }

}
