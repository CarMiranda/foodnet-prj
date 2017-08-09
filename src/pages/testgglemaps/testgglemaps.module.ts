import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { TestgglemapsPage } from './testgglemaps';

@NgModule({
  declarations: [
    TestgglemapsPage,
  ],
  imports: [
    IonicPageModule.forChild(TestgglemapsPage),
  ],
  exports: [
    TestgglemapsPage
  ]
})
export class TestgglemapsPageModule {}
