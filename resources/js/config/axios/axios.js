import axios from "axios";
/*Globally*/

axios.defaults.baseURL = "http://localhost:8000/api";
axios.defaults.headers.common["Authorization"] = "token";
axios.defaults.headers.get["Accepts"] = "application/text";

/* const reqInt=axios.interceptors.request.use(
  config=>{
    console.log('Request interceptor',config)
    config.headers['SOMETHING']="another thing";
    return config;
  }
)
const respInt=axios.interceptors.response.use(
  res=>{
    console.log('Response interceptors',res)
    return res;
  }
) */
// axios.interceptors.request.eject(reqInt)
// axios.interceptors.response.eject(respInt)
export default axios;
