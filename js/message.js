var api_ws = "https://www.singhabeerfinder.com/webservice/message.php";
//var api_ws = "webservice/api.php";
var subfolder="";

$(document).ready(function () {

  var sbf_user = getLocalStorage('sbf_user');

  $('form').submit(function(e){
      e.preventDefault();
      //console.log(form_data);
      var subject = $('#subject').val();
      var message = $('#message').val();

      var form = $('form');
      console.log(form);

      var params = {'method': 'send_message', 'subject': subject, 'message' : message,  'oauth_user_id': sbf_user.oauth_user_id};
      console.log('Before post message');
      console.log(subject);
      console.log(message);
      console.log(sbf_user.oauth_user_id);
      $.post(api_ws, params, function (response) {
        //console.log(response);
        if(response=='success'){
          alert('Sent already');
          window.location = "msg-inbox.html";
        }
      });
    });
  });

  function initRouteMsg(){
    var pathname = window.location.pathname;
    if (pathname === subfolder + '/msg-inbox.html') {
      renderInbox();
    }
    if (pathname === subfolder + '/msg-message.html') {
      renderMessage();
    }

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



  function renderMessage(){
    var pair = window.location.search.substring(1).split("=");
    var unique_id = pair[1];
    var params = {'method': 'get_message', 'unique_id': unique_id};
    $.post(api_ws, params, function (response) {
      var msg = response;
      console.log(msg);

      var options = { year: 'numeric', month: 'short', day: 'numeric' };
      var option_t = {hour: '2-digit', minute:'2-digit'};
      var d = new Date(msg.date_create);
      var dateDisplay = d.toLocaleDateString("en-US",options);
      var timeDisplay = d.toLocaleTimeString([], options ).match(/\d{2}:\d{2}|[AMP]+/g).join(' ');

      $('.msg-header').text(msg.msg_title);
      $('.msg-text').html('<p>' + msg.msg_html + '</p>');
      $('.image-approved, .image-approved + .text-center').html('');
      $('.msg-sender').html(msg.oauth_user_id + '<span>'  + dateDisplay + ' ' + timeDisplay + '</span>');
    });
  }

  

  function renderInbox(){
    $('ul.msg-list').empty();

    var sbf_user = getLocalStorage('sbf_user');

    var params = {'method': 'get_inbox', 'oauth_user_id': sbf_user.oauth_user_id};
    $.post(api_ws, params, function (response) {
      //console.log(response);
      var msgs = response;

      msgs.forEach(function (msg) {
        var is_read = msg.is_read;
        var unique_id = msg.unique_id;
        var msg_title = msg.msg_title;
        var sender = msg.oauth_user_id;
        var senttime = msg.date_create;

        var class_read = '';
        var icon = 'msg-unread.svg';
        var alt = 'unread';

        if(is_read == '1'){
          class_read = ' class="msg-read" ';
          icon = 'msg-read.svg';
          alt = 'read';
        }

        var options = { year: 'numeric', month: 'short', day: 'numeric' };
        var pd = parseDate(senttime);
        var d = new Date(senttime);
        var dateDisplay = d.toLocaleDateString("en-US",options);



        var todaysDate = new Date();
        if(d.setHours(0,0,0,0) == todaysDate.setHours(0,0,0,0)) {
            // Date equals today's date
            console.log(senttime);
            var dd = new Date(senttime);
            dateDisplay = dd.toLocaleTimeString("en-US", {hour: '2-digit', minute:'2-digit'});
        }

        /*
        console.log(msg);
        console.log(senttime);
        console.log(pd);
        console.log(d);
        console.log(dateDisplay);
        */

        var msg_html = '\
        <!--Message-->\
        <li ' + class_read + '>\
          <div class="msg-icon">\
              <img src="images/svg/' + icon + '" alt="' + alt + '"/>\
            </div>\
          <div class="msg-details">\
                <div class="msg-title">\
                    <a href="msg-message.html?msg=' + unique_id + '">\
                        <h4>\
                            ' + msg_title + '\
                        </h4>\
                    </a>\
                </div>\
                <div class="msg-sender">\
                    <span>' + sender + '</span>\
                    <span>' + dateDisplay + '</span>\
                </div>\
            </div>\
        </li>\
  \
        ';

        $('ul.msg-list').append(msg_html);

      }); /// end msgs



    });
  }


$(document).ready(function(){
  initRouteMsg();
});
