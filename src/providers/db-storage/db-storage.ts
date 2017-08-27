import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import 'rxjs/add/operator/map';

@Injectable()
export class DbStorageProvider {
  data: any;

  constructor(private http: Http) {
    this.data = null;
  }

  load(nb: number = 1) {
    return new Promise((resolve) => {
      this.http.get('https://randomuser.me/api/?results=' + nb)
        .map((res) => res.json())
        .subscribe((data) => {
          this.data = data;
          resolve(this.data);
        });
    });
  }
}
