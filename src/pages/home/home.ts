import { Component } from '@angular/core';
import { Platform, NavController, NavParams } from 'ionic-angular';
import { CameraPreview, CameraPreviewPictureOptions, CameraPreviewOptions, CameraPreviewDimensions } from '@ionic-native/camera-preview';
import { Camera, CameraOptions} from '@ionic-native/camera';
import { DomSanitizer, SafeResourceUrl, SafeUrl} from '@angular/platform-browser';
import { Events } from 'ionic-angular';
import { GlobalsProvider } from '../../providers/globals/globals';

import { LoginPage } from '../login/login';
import { ConfirmationPage } from '../confirmation/confirmation';
import { LinefeedPage } from '../linefeed/linefeed';
import { FriendsListPage } from '../friends-list/friends-list';
import { SettingsPage } from '../settings/settings';

@Component({
  selector: 'page-home',
  templateUrl: 'home.html'
})
export class HomePage {

  cameraData: SafeResourceUrl;
  cameraUrl: SafeUrl;
  photoTaken: boolean;
  photoSelected: boolean;
  cameraOpen: boolean;
  actions: string;

  constructor(public navCtrl: NavController, public navParams: NavParams, private platform: Platform, private camera: Camera, private cameraPreview: CameraPreview, private sanitizer: DomSanitizer, public events: Events, public globals: GlobalsProvider) {}

  // Function to start the camera
  openCameraPreview() {
    console.log('Opening camera preview...');
    return new Promise((resolve) => {
      // Set CameraPreview options
      let options : CameraPreviewOptions = {
        x: 0,
        y: 0,
        width: window.screen.availHeight,
        height: window.screen.availHeight,
        camera: 'rear',
        tapPhoto: true,
        previewDrag: false,
        toBack: true,
        alpha: 1
      };

      // Start CameraPreview asynchronously
      console.log('Starting camera...');
      this.cameraPreview.startCamera(options).then((res) => {
          console.log('Camera started correctly!');
          this.cameraOpen = true;
        }, (err) => {
          console.log(err);
        });

      // Show CameraPreview asynchronously
      console.log('Starting camera show...');
      this.cameraPreview.show().then((res) => {
          console.log('Camera shown correctly!');
          resolve(true);
        }, (err) => {
          console.log(err);
        });
    });
  }

  selectFromGallery() {
    console.log('Opening gallery...');

    // Set Camera options
    let options : CameraOptions = {
      sourceType: 0,      // Photo album
      destinationType: 1  // FILE_URI
    };

    // Get picture from gallery asynchronously then navigate to confirmation page
    this.camera.getPicture(options).then((imageData) => {
      console.log('Photo selected correctly!');
      this.cameraUrl = this.sanitizer.bypassSecurityTrustUrl(imageData);
      this.photoSelected = true;
      this.photoTaken = false;
      var callback = (params) => {
        return new Promise((resolve, reject) => {
          this.actions = params;
          resolve();
        });
      }
      console.log('Navigating to ConfirmationPage....');
      this.navCtrl.push( ConfirmationPage, { 
        'imageSource': 0,
        'imageData': this.cameraUrl,
        'callback': callback
      });
    }, (err) => {
      try {
        this.openCameraPreview();
      } catch (exception) {
        console.log(exception);
      }
      console.error(err);
    });
  }

  takePhoto() {
    console.log('Taking photo...');

    // Set CameraPreviewPicture options
    let pictureOptions: CameraPreviewPictureOptions = {
      width: window.screen.availWidth,
      height: window.screen.availHeight,
      quality: 50
    }

    // Take picture asynchronously, then navigate to confirmation page
    this.cameraPreview.takePicture(pictureOptions).then((imageData) => {
      console.log('Photo taken!');
      this.cameraData = 'data:image/jpeg;base64,' + imageData;
      console.log('Navigating to ConfirmationPage...');
      this.navCtrl.push( ConfirmationPage, { 
        'imageSource': 1,
        'imageData': this.cameraData 
      });
    }, (err) => {
      console.log(err);
    });
  }

  ionViewWillEnter() {
    console.log('Will enter HomePage!');
    if (this.actions) {
      console.log('Actions parameter passed from navigation.');
      switch (this.actions) {
        case 'chooseanother':
          this.selectFromGallery();
        break;
        case 'showtutorial':
        break;
      }
      this.actions = '';
    } else {
      console.log('Starting HomePage...');
      this.photoTaken = false;
      this.photoSelected = false;
      this.openCameraPreview();
    }
  }

  ionViewWillLeave() {
    console.log('Will leave HomePage...');
    this.cameraPreview.stopCamera();
  }

  goToLinefeed() {
    console.log('Navigating to LinefeedPage...');
    this.navCtrl.push(LinefeedPage);
  }

  goToFriendsList() {
    console.log('Navigating to FriendsListPage...');
    this.navCtrl.push(FriendsListPage);
  }

  goToSettings() {
    console.log('Navigating to SettingsPage...');
    this.navCtrl.push(SettingsPage);
  }

}
