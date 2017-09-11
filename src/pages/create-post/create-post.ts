import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';
import { Events } from 'ionic-angular';

@IonicPage()
@Component({
  selector: 'page-create-post',
  templateUrl: 'create-post.html',
})
export class CreatePostPage {

  post: any;
  today:string;
  imageSource: number;
  imageData: any;

  constructor(public navCtrl: NavController, public navParams: NavParams, public events: Events) {
  this.imageSource = navParams.get('imageSource');
  this.imageData = navParams.get('imageData');
  this.today = new Date().toISOString();
    this.post = {
      title: '',
      description: '',
      exp_date: this.today,
      imageData: this.imageData
    }
  }

  logForm() {
    // Api connection !
    console.log('Post created!');

    this.events.publish('post:created', this.post);
    this.navCtrl.popAll();
  }
}
