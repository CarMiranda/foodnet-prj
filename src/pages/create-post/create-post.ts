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

  constructor(public navCtrl: NavController, public navParams: NavParams, public events: Events) {
    this.post = {
      title: '',
      description: ''
    }
  }

  logForm() {
    document.getElementById("data").innerHTML = "\
      Title: " + this.post.title + " \n\
      Description: " + this.post.description;
    console.log('Post created!');
    this.events.publish('post:created', this.post);
    this.navCtrl.popAll();
  }
}
