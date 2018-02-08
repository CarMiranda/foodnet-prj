import { Component } from '@angular/core';
import { Platform } from 'ionic-angular';
import { StatusBar } from '@ionic-native/status-bar';
import { SplashScreen } from '@ionic-native/splash-screen';
import { GlobalsProvider } from '../providers/globals/globals';

import { WelcomePage } from '../pages/welcome/welcome';

@Component({
  templateUrl: 'app.html'
})
export class MyApp {
  rootPage:any = WelcomePage;

  constructor(platform: Platform, statusBar: StatusBar, splashScreen: SplashScreen, globals: GlobalsProvider) {
    let self = this;
    platform.ready().then(() => {
      statusBar.styleDefault();
      splashScreen.hide();
      if (globals.get('logged')) {
        self.rootPage = HomePage;
      } else {
        self.rootPage = LoginPage;
      }
    });
  }
}
