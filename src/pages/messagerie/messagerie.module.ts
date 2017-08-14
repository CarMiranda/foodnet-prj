import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { MessageriePage } from './messagerie';

@NgModule({
  declarations: [
    MessageriePage,
  ],
  imports: [
    IonicPageModule.forChild(MessageriePage),
  ],
  exports: [
    MessageriePage
  ]
})
export class MessageriePageModule {}
