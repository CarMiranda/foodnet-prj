import { Component } from '@angular/core';
import { Platform, NavParams, NavController } from 'ionic-angular';
import { Camera, CameraOptions } from '@ionic-native/camera';
import { DomSanitizer, SafeResourceUrl, SafeUrl} from '@angular/platform-browser';
import { Events } from 'ionic-angular';

import { ConfirmationPage } from '../confirmation/confirmation';

import { ProfilePage } from '../profile/profile';
import { FildactualitePage } from '../fildactualite/fildactualite';
import { MessageriePage } from '../messagerie/messagerie';

@Component({
  selector: 'page-home',
  templateUrl: 'home.html'
})
export class HomePage {

  cameraData: SafeResourceUrl;
  photoTaken: boolean;
  cameraUrl: SafeUrl;
  photoSelected: boolean;
  swipe: number = 0;
  isSearchbarOn: boolean =false;
  tab1Root = FildactualitePage;
  tab2Root = MessageriePage;
  private isOn: boolean = false;
  items:string[];

  constructor(public navCtrl: NavController, public navParams: NavParams, private platform: Platform, private camera: Camera, private sanitizer: DomSanitizer, public events: Events) {
    this.photoTaken = false;
    events.subscribe('post:created', (post) => {
      console.log('Post actually created!');
      document.getElementById("title").innerHTML = "Title: " + post.title;
      document.getElementById("description").innerHTML = "Description: " + post.description;
      document.getElementById("image").innerHTML = post.imageData;
    });

  }

  getItems(ev) {
    var val = ev.target.value;  //val : valeur inserée dans la searchbar
    if (val && val.trim() != '') { //si vide on afficher rien du tout
      this.items  = ["Choucroute", "Pizza", "Nutella"];
    }else{
      this.items = [];
    }
  }

  //when searchbar isn't focused
  blur(){
    this.isOn=false;
  }
  //when searchbar is focused
  focus(){
    this.isOn=true;
  }
  searchbar(){
    this.isSearchbarOn=!this.isSearchbarOn;
  }


  selectFromGallery() {
    let options : CameraOptions = {
      sourceType: 0,      // Photo album
      destinationType: 1  // FILE_URI
    };
    this.camera.getPicture(options).then((imageData) => {
      this.cameraUrl = this.sanitizer.bypassSecurityTrustUrl(imageData);
      this.photoSelected = true;
      this.photoTaken = false;
      this.navCtrl.push( ConfirmationPage, {
        'imageSource': 0,
        'imageData': this.cameraUrl
      });
    }, (err) => {
      console.log(err);
    })
  }

  openCamera() {
    let options : CameraOptions = {
      quality: 99,
      destinationType: 0, // DATA_URL
      sourceType: 1, // CAMERA
      allowEdit: false,
      encodingType: 0, // JPEG
      targetWidth: innerWidth,
      targetHeight: innerHeight,
      saveToPhotoAlbum: false,
      correctOrientation: true
    };

    this.camera.getPicture(options).then((imageData) => {
      this.cameraData = this.sanitizer.bypassSecurityTrustResourceUrl('data:image/jpeg;base64,' + imageData);
      this.photoTaken = true;
      this.photoSelected = false;
      this.navCtrl.push( ConfirmationPage, {
        'imageSource': 1,
        'imageData': this.cameraData
      });
    }, (err) => {
      console.log(err);
    });
  }

  goprofile(){
    this.navCtrl.push(ProfilePage);
  }


}
