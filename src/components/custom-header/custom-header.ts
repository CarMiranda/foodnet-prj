import { Component, Input } from '@angular/core';
import { Platform, NavController, NavParams } from 'ionic-angular';
import { Camera, CameraOptions } from '@ionic-native/camera';
import { DomSanitizer, SafeResourceUrl, SafeUrl} from '@angular/platform-browser';

import { ProfilePage } from '../../pages/profile/profile';
import { ConfirmationPage } from '../../pages/confirmation/confirmation';
import { LinefeedPage } from '../../pages/linefeed/linefeed';
/**
 * Generated class for the CustomHeaderComponent component.
 *
 * See https://angular.io/docs/ts/latest/api/core/index/ComponentMetadata-class.html
 * for more info on Angular Components.
 */
@Component({
  selector: 'custom-header',
  templateUrl: 'custom-header.html'
})
export class CustomHeaderComponent {

  cameraData: SafeResourceUrl;
  photoTaken: boolean;
  cameraUrl: SafeUrl;
  photoSelected: boolean;
  private isOn: boolean = false;
  isSearchbarOn: boolean =false;
  swipe: number = 0;
  items:string[];

  header_data : any;

  constructor(public navCtrl: NavController, public navParams: NavParams, private platform: Platform, private camera: Camera, private sanitizer: DomSanitizer) {
  }
  @Input()
  set header(header_data: any) {
    this.header_data=header_data;
  }
  get header() {
    return this.header_data;
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

    getItems(ev) {
      var val = ev.target.value;  //val : valeur inserée dans la searchbar
      if (val && val.trim() != '') { //si vide on afficher rien du tout
        this.items  = ["Choucroute", "Pizza", "Nutella"];
      }else{
        this.items = [];
      }
    }

    go(toPage: string) {
      console.log("Swiped! Going to " + toPage);
      if (toPage === 'Linefeed') {
        this.navCtrl.push(LinefeedPage);
      }
      if (toPage === 'ProfilePage') {
        this.navCtrl.push(ProfilePage);
      }
    }
}
