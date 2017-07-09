import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import { Events } from 'ionic-angular'; 
import 'rxjs/add/operator/map';

class User {
  private name: string;
  public isLogged: boolean;

  constructor() {
    this.name = 'Carlos';
    this.isLogged = true;
  }

  getName() {
    return this.name;
  }

}

@Injectable()
export class UserProvider {

  private user : User;

  constructor(public http: Http, public events: Events) {
    this.user = new User();
  }

  login() {
    let self = this;
    return new Promise((resolve) => {
      resolve(self.user);
    });
  }

}
