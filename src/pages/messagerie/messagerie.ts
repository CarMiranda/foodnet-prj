import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, App } from 'ionic-angular';
import { DbStorageProvider } from '../../providers/db-storage/db-storage';
import { Platform } from 'ionic-angular';

import { ConversationPage } from '../conversation/conversation';
@IonicPage()
@Component({
  selector: 'page-messagerie',
  templateUrl: 'messagerie.html',
})
export class MessageriePage {

  data: any[];
  data2:any[];
  header_data:any;
  constructor(public app: App,public platform: Platform, public nav: NavController, public navParams: NavParams, public dbStorage: DbStorageProvider) {
    this.dbStorage.load(5).then((data : any) => {
      this.data = data.results;
    }, (err) => {
      console.log(err);
    });
    this.dbStorage.load(5).then((data : any) => {
      this.data2 = data.results;
    }, (err) => {
      console.log(err);
    });
    this.header_data={isSearch:true,isCamera:true,isProfile:true,title:"KooDeFood"};
  }

  viewProduct(id: string) {
    let idx : number = this.data.findIndex((el) => {
      return el.cell == id;
    });
    console.log(JSON.stringify(this.data[idx]));
    this.app.getRootNav().push(ConversationPage, {
      'product': this.data[idx]
    });
  }


}
