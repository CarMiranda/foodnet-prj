import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { TestGeolocaPage } from './test-geoloca';

@NgModule({
  declarations: [
    TestGeolocaPage,
  ],
  imports: [
    IonicPageModule.forChild(TestGeolocaPage),
  ],
  exports: [
    TestGeolocaPage
  ]
})
export class TestGeolocaPageModule {}
