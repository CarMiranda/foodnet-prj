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

  imageSource: number;
  imageData: any;

  constructor(public navCtrl: NavController, public navParams: NavParams, public events: Events) {
  this.imageSource = navParams.get('imageSource');
  this.imageData = navParams.get('imageData');
    this.post = {
      title: '',
      description: '',
      imageData: this.imageData
    }
  }

  logForm() {

    console.log('Post created!');

    this.events.publish('post:created', this.post);
    this.navCtrl.popAll();
  }
}
