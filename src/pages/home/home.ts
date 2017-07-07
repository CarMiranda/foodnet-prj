import { Component } from '@angular/core';
import { Platform, NavController } from 'ionic-angular';
import { CameraPreview, CameraPreviewPictureOptions, CameraPreviewOptions, CameraPreviewDimensions } from '@ionic-native/camera-preview';
import { Camera, CameraOptions} from '@ionic-native/camera';
import { DomSanitizer, SafeResourceUrl, SafeUrl} from '@angular/platform-browser';
import { Events } from 'ionic-angular';
import { GlobalsProvider } from '../../providers/globals/globals';

import { LoginPage } from '../login/login';
import { ConfirmationPage } from '../confirmation/confirmation';
import { LinefeedPage } from '../linefeed/linefeed';
import { ChatPage } from '../chat/chat';
import { SettingsPage } from '../settings/settings';

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
  cameraOpen: boolean;
  const 

  constructor(public navCtrl: NavController, private platform: Platform, private camera: Camera, private cameraPreview: CameraPreview, private sanitizer: DomSanitizer, public events: Events, public globals: GlobalsProvider) {
    platform.ready().then(() => {
      // When native functions are ready, start the camera
      let self = this;
      this.startCamera().then((res) => {
        // Subscribe to an navigation event triggering reopening of the camera
        self.events.subscribe('nav:backHome', (user) => {
          console.log('Navigated back Home!');
          self.startCamera();
        });
      }, (err) => {
        console.error(err);
      });
    });
  }

  // Function to start the camera
  startCamera() {
    this.photoTaken = false;
    return new Promise((res) => {

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
      let self = this;
      this.cameraPreview.startCamera(options)
        .then((res) => {
          console.log(res);
          self.cameraOpen = true;
        }, (err) => {
          console.error(err);
        });

      // Show CameraPreview asynchronously
      this.cameraPreview.show()
        .then((res) => {
          console.log(res);
          res(true);
        }, (err) => {
          console.error(err);
        });
    });
  }

  selectFromGallery() {

    // Set Camera options
    let options : CameraOptions = {
      sourceType: 0,      // Photo album
      destinationType: 1  // FILE_URI
    };

    // Get picture from gallery asynchronously then navigate to confirmation page
    this.camera.getPicture(options).then((imageData) => {
      this.cameraUrl = this.sanitizer.bypassSecurityTrustUrl(imageData);
      this.photoSelected = true;
      this.photoTaken = false;
      this.navCtrl.push( ConfirmationPage, { 
        'imageSource': 0,
        'imageData': this.cameraUrl 
      });
    }, (err) => {
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
      this.cameraData = 'data:image/jpeg;base64,' + imageData;
      this.navCtrl.push( ConfirmationPage, { 
        'imageSource': 1,
        'imageData': this.cameraData 
      });
    }, (err) => {
      console.log(err);
    });

    console.log('Photo taken!');
  }

  goToLinefeed() {
    this.cameraPreview.stopCamera();
    this.navCtrl.push(LinefeedPage);
  }

  goToChat() {
    this.cameraPreview.stopCamera();
    this.navCtrl.push(ChatPage);
  }

  goToSettings() {
    this.cameraPreview.stopCamera();
    this.navCtrl.push(SettingsPage);
  }

}
