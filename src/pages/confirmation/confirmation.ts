import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';
import { CreatePostPage } from '../create-post/create-post';

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

  retakePic() {
    console.log('Implement this sHieeT.');
  }

  chooseAnother() {
    console.log('Implement this sHieeT.');
  }

  createPost() {
    this.navCtrl.push(CreatePostPage, {
      'imageData' : this.imageData
    })
  }

}
