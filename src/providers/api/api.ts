import { Injectable } from '@angular/core';
import { Http, Headers } from '@angular/http';
import 'rxjs/add/operator/map';

@Injectable()
export class ApiProvider {
  apiUrl = "https://ttttest.000webhostapp.com/";
  constructor(public http: Http) {
    console.log('Hello ApiProvider Provider');
  }

  postData(datas,type){
    return new Promise((resolve,reject)=>{
      let headers = new Headers();
      console.log(  JSON.stringify(datas));;
      this.http.post(this.apiUrl+type,JSON.stringify(datas),{headers:headers})
      .subscribe(res=>{
        console.log("resolve");
        resolve(res.json());
      },err =>{
        reject(err);
        console.log(err);
      });
    });
  }

  GETData(option,type){
    return new Promise(resolve=>{
      this.http.get(this.apiUrl+type).subscribe(res => {
        console.log(res.status);
        // data received by server
        console.log(res.headers);
        resolve(res.json());
      }, err => {
        console.log('ERRRR');
      });
    });
  }
}
