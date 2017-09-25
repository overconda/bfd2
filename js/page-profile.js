var subfolder = '';
var api_ws = "https://www.singhabeerfinder.com/webservice/api.php";
//var api_ws = "webservice/api.php";

$(document).ready(function () {
  var sbf_user = getLocalStorage('sbf_user');
  var params = {'method': 'get_user_score_level', 'oauth_user_id': sbf_user.oauth_user_id};
  $.post(api_ws, params, function (response) {

    var user = response;
    console.log(user);

    $('.profile-edit-btn').css('display','none');

    //$('.profile-pts .num-transition').remove();
    $('.profile-pts .num-transition').html('<span class="white-text">' + user.score + '</span>');
    $('.profile-pts .button').css('display','none');

    /*
    $('.route-stat-row .route-stat-col:nth-child(1) .num-transition').remove()
    $('.route-stat-row .route-stat-col:nth-child(1) .num-transition').html('<span  data-number="' + user.unlocked_num + '" class="numAnimate numSlideIn delay2">' + user.unlocked_num + '</span>');
    */
    $('.route-stat-row .route-stat-col:nth-child(1) .num-transition').html('<span class="white-text">' + user.unlocked_num + '</span>');
    $('.route-stat-row .route-stat-col:nth-child(2) .num-transition').html('<span class="white-text">0</span>');
    $('.route-stat-row .route-stat-col:nth-child(3) .num-transition').html('<span class="white-text">--</span>');
    //$('.route-stat-row .route-stat-col:nth-child(3)').css('display','none');


    $('.profile-section.profile-unlocked-bases .profile-subtitle:nth-child(1)').html('Unlocked ' + user.unlocked_num + ' bases');

    var params = {'method': 'get_user_unlocked_base_name', 'oauth_user_id': sbf_user.oauth_user_id};
    $('.unlocked-bases-list .unlocked-bases-row').html('');
    $.post(api_ws, params, function (response) {
      base =response;
      for(var i =0 ; i< base.length; i++){
        var base_title = base[i].base_title;
        console.log(base_title);
        $('.unlocked-bases-list .unlocked-bases-row').append(
          '\
          <div class="unlocked-bases-col">\
              <div class="base-box">\
                  <a href="#">\
                      <figure>\
                          <img src="images/svg/circle-dot-sm.svg" alt="border"/>\
                      </figure>\
                      <span>' + base_title + '</span>\
                  </a>\
              </div>\
          </div>\
          '
        );
      }

    });



    //animateMe($('.profile-pts .num-transition'));
    //numberAnimate();
  });



});
