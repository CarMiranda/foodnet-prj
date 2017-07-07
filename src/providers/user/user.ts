import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import { Events } from 'ionic-angular'; 
import 'rxjs/add/operator/map';

class User {
  private name : string;

  constructor() {
    this.name = 'Carlos';
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
    return new Promise((resolve) => {
      resolve(this.user);
    });
  }

}
