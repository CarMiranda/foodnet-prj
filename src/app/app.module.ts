import { BrowserModule } from '@angular/platform-browser';
import { ErrorHandler, NgModule } from '@angular/core';
import { IonicApp, IonicErrorHandler, IonicModule } from 'ionic-angular';
import { SplashScreen } from '@ionic-native/splash-screen';
import { StatusBar } from '@ionic-native/status-bar';
import { Camera } from '@ionic-native/camera';
import { HttpModule } from '@angular/http';
import { Geolocation } from '@ionic-native/geolocation';

import { MyApp } from './app.component';
import { HomePage } from '../pages/home/home';
import { ConfirmationPage } from '../pages/confirmation/confirmation';
import { CreatePostPage } from '../pages/create-post/create-post';
import { LinefeedPage } from '../pages/linefeed/linefeed';
import { ProductDetailsPage } from '../pages/product-details/product-details';
import { DbStorageProvider } from '../providers/db-storage/db-storage';
import { TestGeolocaPage } from '../pages/test-geoloca/test-geoloca'

@NgModule({
  declarations: [
    MyApp,
    HomePage,
    ConfirmationPage,
    CreatePostPage,
    LinefeedPage,
    ProductDetailsPage,
    TestGeolocaPage
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
    TestGeolocaPage
  ],
  providers: [
    StatusBar,
    SplashScreen,
    Geolocation,
    Camera,
    {provide: ErrorHandler, useClass: IonicErrorHandler},
    DbStorageProvider
  ]
})
export class AppModule {}
