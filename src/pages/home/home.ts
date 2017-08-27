import { Component, ElementRef, Renderer, ViewChild } from '@angular/core';
import { Events, Tabs } from 'ionic-angular';
import { Keyboard } from '@ionic-native/keyboard';
import { FildactualitePage } from '../fildactualite/fildactualite';
import { MessageriePage } from '../messagerie/messagerie';

@Component({
  selector: 'page-home',
  templateUrl: 'home.html'
})
export class HomePage {
  tab1Root = FildactualitePage;
  tab2Root = MessageriePage;
  keyboardOpen:boolean=false;
  hideTabs :boolean = true;

  constructor(private keyboard :Keyboard, private elementRef: ElementRef, private renderer: Renderer, private event: Events) {

  }

  ionViewDidEnter(){
    let tabs : HTMLElement = document.getElementById('tabs');
     this.keyboard.onKeyboardShow().subscribe((data)=>{
       this.keyboardOpen=true;
       console.log("le keyboard a été open");
       tabs.classList.add('tabs-item-hide');
     });
     this.keyboard.onKeyboardHide().subscribe((data)=>{
       this.keyboardOpen=false;
       console.log("le keyboard a été fermé");
      //  tabs.style.display='block';
     });
  }
}
