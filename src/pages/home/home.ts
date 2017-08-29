import { Component, ElementRef, Renderer } from '@angular/core';
import { Events } from 'ionic-angular';
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

  userData : any;
  constructor(private keyboard :Keyboard, private elementRef: ElementRef, private renderer: Renderer, private event: Events) {

    const data = JSON.parse(localStorage.getItem('userData'));
    this.userData = data;
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
