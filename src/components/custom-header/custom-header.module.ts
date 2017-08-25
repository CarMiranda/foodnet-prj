import { NgModule } from '@angular/core';
import { IonicModule } from 'ionic-angular';
import { CustomHeaderComponent } from './custom-header';

@NgModule({
  declarations: [
    CustomHeaderComponent,
  ],
  imports: [
    IonicModule,
  ],
  exports: [
    CustomHeaderComponent
  ]
})
export class CustomHeaderComponentModule {}
