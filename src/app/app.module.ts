import { NgModule, ErrorHandler } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { IonicApp, IonicModule, IonicErrorHandler } from 'ionic-angular';
import { MyApp } from './app.component';
import { HttpModule } from '@angular/http';
import { Camera } from '@ionic-native/camera';


import { HomePage } from '../pages/home/home';
import { LoginPage } from '../pages/login/login';
import { WelcomePage} from '../pages/welcome/welcome';
import { SignupPage} from '../pages/signup/signup';
import { ConfirmationPage} from '../pages/confirmation/confirmation';
import { CreatePostPage } from '../pages/create-post/create-post';
import { LinefeedPage } from '../pages/linefeed/linefeed';
import { DbStorageProvider } from '../providers/db-storage/db-storage';
import { ProductDetailsPage } from '../pages/product-details/product-details';
import { ConversationPage } from '../pages/conversation/conversation';
import { FildactualitePage } from '../pages/fildactualite/fildactualite';
import { ProfilePage } from '../pages/profile/profile';
import { MessageriePage } from '../pages/messagerie/messagerie';
import { OtherProfilePage } from '../pages/other-profile/other-profile';

import { StatusBar } from '@ionic-native/status-bar';
import { SplashScreen } from '@ionic-native/splash-screen';
import { ApiProvider } from '../providers/api/api';
import { CustomHeaderComponent } from '../components/custom-header/custom-header';

@NgModule({
  declarations: [
    MyApp,
    HomePage,
    LoginPage,
    WelcomePage,
    SignupPage,
    ConfirmationPage,
    CreatePostPage,
    LinefeedPage,
    ProductDetailsPage,
    ConversationPage,
    CustomHeaderComponent,
    FildactualitePage,
    ProfilePage,
    MessageriePage,
    OtherProfilePage
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
    LoginPage,
    WelcomePage,
    SignupPage,
    ConfirmationPage,
    CreatePostPage,
    LinefeedPage,
    ProductDetailsPage,
    ConversationPage,
    FildactualitePage,
    ProfilePage,
    MessageriePage,
    OtherProfilePage
  ],
  providers: [
    StatusBar,
    SplashScreen,
    Camera,
    {provide: ErrorHandler, useClass: IonicErrorHandler},
    ApiProvider,
    DbStorageProvider
  ]
})
export class AppModule {}
