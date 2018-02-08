import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';
import { CreatePostPage } from '../create-post/create-post';
import { Events } from 'ionic-angular';

@IonicPage()
@Component({
  selector: 'page-confirmation',
  templateUrl: 'confirmation.html',
})
export class ConfirmationPage {

  imageSource: number;
  imageData: any;
  constructor(public navCtrl: NavController, public navParams: NavParams) {
    this.imageSource = navParams.get('imageSource');
    this.imageData = navParams.get('imageData');
  }

  chooseAnother() {
    console.log('Navigating back to HomePage to choose another picture...');
    var callback = this.navParams.get('callback');
    callback('chooseanother').then(() => {
      this.navCtrl.pop();
    })
  }

  retakePicture() {
    console.log('Navigating back to HomePage to retake the picture...');
    this.navCtrl.pop();
  }

  createPost() {
    this.navCtrl.push(CreatePostPage, {
      'imageData' : this.imageData
    });
  }

}
