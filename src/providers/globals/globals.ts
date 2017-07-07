import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import 'rxjs/add/operator/map';

class Globals {

  private lang : string;
  private logged : boolean;

  constructor() {
    this.lang = 'en';
    this.logged = false;
  }

  get(id : string) : any {
    if (this.hasOwnProperty(id)) {
      return this[id];
    } else {
      return false;
    }
  }

}

@Injectable()
export class GlobalsProvider {

  private globals : Globals;

  constructor(public http: Http) {
    // this.globals.load();
    this.globals = new Globals();
  }

  get(id : string) : any {
    return this.globals.get(id);
  }

}
