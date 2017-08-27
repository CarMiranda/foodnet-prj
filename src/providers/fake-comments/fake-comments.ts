import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import 'rxjs/add/operator/map';
const COMMENTS_API: string = "http://localhost:3000/comments";

@Injectable()
export class FakeCommentsProvider {
  data : any;
  constructor(public http: Http) {
    console.log('Hello FakeCommentsProvider Provider');
    this.data = null;
  }

  getComments(){
    return new Promise((resolve) => {
      this.http
        .get(COMMENTS_API)
        .map(resp => resp.json())
        .subscribe((data) => {
          this.data = data;
          resolve(this.data);
        });
    });
  }

}
