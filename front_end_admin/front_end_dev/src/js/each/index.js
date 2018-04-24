$(function () {
    function isLogin() {
       var isToken = localStorage.getItem("BUSINESS_TOKEN") || localStorage.getItem("BUSINESS_USERNAME");
       if(!isToken){
           location.href = "./login.html";
       }
    }
    isLogin()
})
