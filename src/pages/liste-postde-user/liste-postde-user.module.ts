import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { ListePostdeUserPage } from './liste-postde-user';

@NgModule({
  declarations: [
    ListePostdeUserPage,
  ],
  imports: [
    IonicPageModule.forChild(ListePostdeUserPage),
  ],
})
export class ListePostdeUserPageModule {}
