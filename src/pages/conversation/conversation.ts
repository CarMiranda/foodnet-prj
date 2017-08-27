import { Component } from '@angular/core';
import { IonicPage } from 'ionic-angular';
import { FakeCommentsProvider } from '../../providers/fake-comments/fake-comments';
import { Keyboard } from '@ionic-native/keyboard';
import { Events } from 'ionic-angular';


@IonicPage()
@Component({
  selector: 'page-conversation',
  templateUrl: 'conversation.html',
})

// dans fake-rest-api json-server --watch "src/assets/data.json"

export class ConversationPage {
  header_data:any;
  messages:any[];
  newMessage = {content:''}
  constructor(private event: Events,private keyboard: Keyboard, public fakeCommentsProvider : FakeCommentsProvider) {
    this.fakeCommentsProvider.getComments().then((data : any) => {
      this.messages = data;
    }, (err) => {
      console.log(err);
    });
    // header personnalisé
    this.header_data={isSearch:false,isCamera:true,isProfile:true,title:"KooDeFood"};
  }

  ionViewDidLoad() {
    this.keyboard.onKeyboardShow().subscribe(() => this.event.publish('hideTabs'));
    this.keyboard.onKeyboardHide().subscribe(() => this.event.publish('showTabs'));
  }

  focus(){
    console.log("keyboard on");
  }
  blur(){
    console.log("keyboard oFF");
  }

  submitMessage(){
    console.log('submitting message : '+this.newMessage.content);
  }

}
