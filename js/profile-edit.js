var profile_edit_ws = "https://www.singhabeerfinder.com/webservice/profile-edit.php";
//var api_ws = "webservice/api.php";
var subfolder="";

$(document).ready(function () {

  var sbf_user = getLocalStorage('sbf_user');

  var params = {'method': 'get_profile', 'oauth_user_id': sbf_user.oauth_user_id};
  $.post(profile_edit_ws, params, function (response) {

  });

  function initProfileEdit(){
    var pathname = window.location.pathname;
    /*
    if (pathname === subfolder + '/msg-inbox.html') {
      renderInbox();
    }
    if (pathname === subfolder + '/msg-message.html') {
      renderMessage();
    }
    */

  }

  function getLocalStorage(key) {
    return JSON.parse(window.localStorage.getItem(key)) || undefined;
  }

  function parseDate(date) {
    const parsed = Date.parse(date);
    if (!isNaN(parsed)) {
      return parsed;
    }
    return Date.parse(date.replace(/-/g, '/').replace(/[a-z]+/gi, ' '));
  }

}); // ready
