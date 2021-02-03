import axios from "axios";
const instance = axios.create({
  baseURL: "http://localhost/yii_micro/",
});
instance.defaults.headers.common["SOMETHING"] = "token";

export default instance;
