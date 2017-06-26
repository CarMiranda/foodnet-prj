import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { LinefeedPage } from './linefeed';
import { DbStorageProvider } from '../../providers/db-storage/db-storage';

@NgModule({
  declarations: [
    LinefeedPage,
  ],
  imports: [
    IonicPageModule.forChild(LinefeedPage),
  ],
  exports: [
    LinefeedPage
  ],
  providers: [
    DbStorageProvider
  ]
})
export class LinefeedPageModule {}
