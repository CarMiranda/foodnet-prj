import { BrowserModule } from '@angular/platform-browser';
import { ErrorHandler, NgModule } from '@angular/core';
import { IonicApp, IonicErrorHandler, IonicModule } from 'ionic-angular';
import { SplashScreen } from '@ionic-native/splash-screen';
import { StatusBar } from '@ionic-native/status-bar';
import { Camera } from '@ionic-native/camera';
import { CameraPreview } from '@ionic-native/camera-preview';
import { HttpModule } from '@angular/http';
import { HammerGestureConfig, HAMMER_GESTURE_CONFIG } from '@angular/platform-browser';

import { MyApp } from './app.component';
import { HomePage } from '../pages/home/home';
import { ConfirmationPage } from '../pages/confirmation/confirmation';
import { CreatePostPage } from '../pages/create-post/create-post';
import { LinefeedPage } from '../pages/linefeed/linefeed';
import { ProductDetailsPage } from '../pages/product-details/product-details'; 
import { LoginPage } from '../pages/login/login';
import { DbStorageProvider } from '../providers/db-storage/db-storage';
import { GlobalsProvider } from '../providers/globals/globals';
import { UserProvider } from '../providers/user/user';
import { AuthProvider } from '../providers/auth/auth';

export class MyHammerConfig extends HammerGestureConfig  {
  overrides = <any>{
      // override hammerjs default configuration
      'pan': {threshold: 5},
      'swipe': {
           velocity: 0.4,
           threshold: 20,
           direction: 31 // /!\ ugly hack to allow swipe in all direction
      }
  }
}

@NgModule({
  declarations: [
    MyApp,
    HomePage,
    ConfirmationPage,
    CreatePostPage,
    LinefeedPage,
    ProductDetailsPage,
    LoginPage
  ],
  imports: [
    BrowserModule,
    HttpModule,
    IonicModule.forRoot(MyApp)
  ],
  bootstrap: [IonicApp],
  entryComponents: [
    MyApp,
    HomePage,
    ConfirmationPage,
    CreatePostPage,
    LinefeedPage,
    ProductDetailsPage,
    LoginPage
  ],
  providers: [
    StatusBar,
    SplashScreen,
    Camera,
    CameraPreview,
    {provide: ErrorHandler, useClass: IonicErrorHandler},
    DbStorageProvider,
    GlobalsProvider,
    UserProvider,
    AuthProvider,
    {provide: HAMMER_GESTURE_CONFIG, useClass: MyHammerConfig}
  ]
})
export class AppModule {}
