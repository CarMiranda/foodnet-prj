import { Injectable } from '@angular/core';
import { Http, Headers,URLSearchParams, RequestOptions } from '@angular/http';
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
      console.log(  JSON.stringify(datas));;
      this.http.post(this.apiUrl+type,JSON.stringify(datas),{headers:headers})
      .subscribe(res=>{
        console.log("resolve");
        let resjson = res.json();
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

  GETData(type,datas? :any,options?: RequestOptions){
    return new Promise((resolve,reject)=>{
      //userAgent
      // this.userAgent.set('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36')
      //   .then((res: any) => console.log(res))
      //   .catch((error: any) => console.error(error));
    //Authorization token
      this.token =JSON.parse(localStorage.getItem('userToken'));
  //     if (!options) {
  //       options = new RequestOptions({
  //         headers:headers
  //       });
  //      }
  // let p = new URLSearchParams();
  //      if (datas) {
  //
  //       for(let k in datas) {
  //          p.set(k, datas[k]);
  //       }
        // options.search = !options.search && p || options.search;
      // }
      console.log(  options);
      let url = this.apiUrl+type;

      var headers = new Headers({
        'Authorization': 'Bearer '+this.token
      });
      this.http.get(url ,{headers:headers})
      .subscribe(res=>{
        console.log(res);
        let resjson = res.json();
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
