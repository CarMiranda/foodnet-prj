import { BrowserModule } from '@angular/platform-browser';
import { ErrorHandler, NgModule } from '@angular/core';
import { IonicApp, IonicErrorHandler, IonicModule } from 'ionic-angular';
import { SplashScreen } from '@ionic-native/splash-screen';
import { StatusBar } from '@ionic-native/status-bar';
import { Camera } from '@ionic-native/camera';
import { CameraPreview } from '@ionic-native/camera-preview';
import { HttpModule } from '@angular/http';

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
import { SwipeVerticalDirective } from '../directives/swipe-vertical/swipe-vertical';
import { AuthProvider } from '../providers/auth/auth';

@NgModule({
  declarations: [
    MyApp,
    HomePage,
    ConfirmationPage,
    CreatePostPage,
    LinefeedPage,
    ProductDetailsPage,
    LoginPage,
    SwipeVerticalDirective
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
    AuthProvider
  ]
})
export class AppModule {}
