import { BrowserModule } from '@angular/platform-browser';
import { ErrorHandler, NgModule } from '@angular/core';
import { IonicApp, IonicErrorHandler, IonicModule } from 'ionic-angular';
import { SplashScreen } from '@ionic-native/splash-screen';
import { StatusBar } from '@ionic-native/status-bar';
import { Camera } from '@ionic-native/camera';
import { Keyboard } from '@ionic-native/keyboard';
import { HttpModule } from '@angular/http';
import { Geolocation } from '@ionic-native/geolocation';

import { CameraPreview } from '@ionic-native/camera-preview';
import { HttpModule } from '@angular/http';
import { HammerGestureConfig, HAMMER_GESTURE_CONFIG } from '@angular/platform-browser';

import { MyApp } from './app.component';
import { HomePage } from '../pages/home/home';
import { ConfirmationPage } from '../pages/confirmation/confirmation';
import { CreatePostPage } from '../pages/create-post/create-post';
import { LinefeedPage } from '../pages/linefeed/linefeed';
import { ProductDetailsPage } from '../pages/product-details/product-details';
import { DbStorageProvider } from '../providers/db-storage/db-storage';
import { TestGeolocaPage } from '../pages/test-geoloca/test-geoloca';
import { WelcomePage } from '../pages/welcome/welcome';
import { LoginPage } from '../pages/login/login';
import { SignupPage } from '../pages/signup/signup';
import { ProfilePage } from '../pages/profile/profile';
import { FildactualitePage } from '../pages/fildactualite/fildactualite';
import { MessageriePage } from '../pages/messagerie/messagerie';
import { ConversationPage } from '../pages/conversation/conversation';
import { CustomHeaderComponent } from '../components/custom-header/custom-header';
import { FakeCommentsProvider } from '../providers/fake-comments/fake-comments';
import { ApiProvider } from '../providers/api/api';
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
    TestGeolocaPage,
    WelcomePage,
    LoginPage,
    SignupPage,
    ProfilePage,
    FildactualitePage,
    MessageriePage,
    CustomHeaderComponent,
    ConversationPage
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
    TestGeolocaPage,
    WelcomePage,
    LoginPage,
    SignupPage,
    ProfilePage,
    FildactualitePage,
    MessageriePage,
    ConversationPage
  ],
  providers: [
    StatusBar,
    SplashScreen,
    Geolocation,
    Camera,
    Keyboard,
    FakeCommentsProvider,
    ApiProvider
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
