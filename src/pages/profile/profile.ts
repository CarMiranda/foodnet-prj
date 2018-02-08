import { Component } from '@angular/core';
import { NavController, App, NavParams,ToastController, ActionSheetController } from 'ionic-angular';
import { DbStorageProvider } from '../../providers/db-storage/db-storage';
import { Platform } from 'ionic-angular';
import { Camera, CameraOptions } from '@ionic-native/camera';
import { DomSanitizer, SafeResourceUrl, SafeUrl} from '@angular/platform-browser';

import { ApiProvider } from '../../providers/api/api';
import { ListePostdeUserPage } from '../liste-postde-user/liste-postde-user';
import { ConfirmationPage } from '../../pages/confirmation/confirmation';
@Component({
  selector: 'page-profile',
  templateUrl: 'profile.html',
})
export class ProfilePage {
  public userDetails: any;
  header_data:any;
  public data:any;
  public dataApi :any;
  cameraData: SafeResourceUrl;
  photoTaken: boolean;
  cameraUrl: SafeUrl;
  photoSelected: boolean;

  constructor(private camera: Camera, private sanitizer: DomSanitizer, public actionSheetCtrl: ActionSheetController, public app: App,public toastCtrl:ToastController, public apiProvider:ApiProvider, public platform: Platform, public navCtrl: NavController, public navParams: NavParams, public dbStorage: DbStorageProvider) {
    this.dbStorage.load(1).then((data : any) => {
      this.userDetails = data.results[0];
    }, (err) => {
      console.log(err);
    });
    this.data =JSON.parse(localStorage.getItem('userToken'));
    //recupDATA de L'api :
    this.apiProvider.GETData("users").then((res)=>{
      this.dataApi=res;
      console.log(res);
    },(err)=>{
      console.log(err);
      let messageERROR:string
      switch(err.status){
        // 0 quand on a pas de connection
        case 0:
          messageERROR='Connexion à l\'api impossible';
          break;
          // exception quand l'api renvoie une exeption: pr l'
        case "exception" :
          messageERROR=err.data.message;
          break;
      };
      let toast = this.toastCtrl.create({
        message: messageERROR,
        duration: 3000,
        position: 'bottom'
      });
      toast.present();
    });
    // header personnalisé
    this.header_data={isSearch:false,isCamera:true,isProfile:false,title:"Mon profil"};
  }

  showMyPosts(){
    this.navCtrl.push(ListePostdeUserPage, {
      'user_id':this.dataApi.data.id
    });
  }

  backToWelcome(){
    const root = this.app.getRootNav();
    root.popToRoot();
  }

  logout(){
    localStorage.clear();
    setTimeout(() => this.backToWelcome(),1500);
  }

  presentActionSheet() {
    console.log("presentActionSheet")
    let actionSheet = this.actionSheetCtrl.create({
      title: 'Modifiez votre photo de profil !',
      buttons: [
        {
          text: 'Ouvrir la caméra',
          role: 'destructive',
          handler: () => {
            this.openCamera();
          }
        },{
          text: 'Choisir dans ma galerie',
          handler: () => {
            this.selectFromGallery();
          }
        },{
          text: 'Annuler',
          role: 'cancel',
          handler: () => {
            console.log('Cancel clicked');
          }
        }
      ]
    });
    actionSheet.present();
  }
  selectFromGallery() {
    let options : CameraOptions = {
      sourceType: 0,      // Photo album
      destinationType: 1  // FILE_URI
    };
    this.camera.getPicture(options).then((imageData) => {
      this.cameraUrl = this.sanitizer.bypassSecurityTrustUrl(imageData);
      this.dataApi.data.avatar = this.cameraData;
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
      this.dataApi.data.avatar = this.cameraData;
    }, (err) => {
      console.log(err);
    });
  }


}
