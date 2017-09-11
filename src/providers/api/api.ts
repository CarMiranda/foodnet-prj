import { Injectable } from '@angular/core';
import { Http, Headers, RequestOptions } from '@angular/http';
import 'rxjs/add/operator/map';
// import { UserAgent } from '@ionic-native/user-agent';

@Injectable()
export class ApiProvider {
  private apiUrl = "https://ttttest.000webhostapp.com/";
  private token:string;

  constructor(public http: Http) {
    console.log('Hello ApiProvider Provider');
  }

  postData(type,datas,withToken?: boolean){
    return new Promise((resolve,reject)=>{
      let headers = new Headers();
      if (withToken){
        headers.append('Authorization', 'Bearer '+this.token);
      }
      this.http.post(this.apiUrl+type,JSON.stringify(datas),{headers:headers})
      .subscribe(res=>{
        let resjson = res.json();
        switch(resjson.status){
          case "exception": reject(resjson);break;
          case "success" : resolve(resjson);break;
        };
      },(err) =>{
        reject(err);
      });
    });
  }

  GETData(type,datas? :any,options?: RequestOptions){

    return new Promise((resolve,reject)=>{
      this.token =JSON.parse(localStorage.getItem('userToken'));
      console.log(  options);
      let url = this.apiUrl+type;
      // Header contenant le token d'Authorization
      var headers = new Headers({
        'Authorization': 'Bearer '+this.token
      });
      console.log(headers);
      this.http.get(url ,{headers:headers})
      .subscribe(res=>{
        let resjson = res.json();
        console.log(resjson);
        console.log(res);
        switch(resjson.status){
          case "exception": reject(resjson);break;
          case "success" : resolve(resjson);break;
        };
      },(err) =>{
        reject(err);
        console.log(err);
      });



    });
  }
}
