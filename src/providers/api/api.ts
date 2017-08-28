import { Injectable } from '@angular/core';
import { Http, Headers } from '@angular/http';
import 'rxjs/add/operator/map';

let apiUrl = "https://ttttest.000webhostapp.com/";
/*            https://ttttest.000webhostapp.com/
  Generated class for the ApiProvider provider.

  See https://angular.io/docs/ts/latest/guide/dependency-injection.html
  for more info on providers and Angular DI.
*/
@Injectable()
export class ApiProvider {

  constructor(public http: Http) {
    console.log('Hello ApiProvider Provider');
  }

  postData(datas,type){
    return new Promise((resolve, reject)=>{
      let headers = new Headers();
      this.http.post(apiUrl+type,JSON.stringify(datas),{headers: headers})
      .subscribe(res =>{
        resolve(res.json());
      }), (err) =>
      reject(err);
    });

  }
}
