import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { FildactualitePage } from './fildactualite';

@NgModule({
  declarations: [
    FildactualitePage,
  ],
  imports: [
    IonicPageModule.forChild(FildactualitePage),
  ],
  exports: [
    FildactualitePage
  ]
})
export class FildactualitePageModule {}
