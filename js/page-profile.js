var subfolder = '';
var api_ws = "https://www.singhabeerfinder.com/webservice/api.php";
var api_rt = "https://www.singhabeerfinder.com/webservice/route-api.php";
//var api_ws = "webservice/api.php";

function separateNumber(number){
		//var output = [],
    var sNumber = number.toString();
    var s = '';
    console.log(sNumber);

    for (var i = 0, len = sNumber.length; i < len; i += 1) {
       var rand = Math.floor((Math.random() * 8) + 1);
        s += '<span data-number="' + sNumber.charAt(i) + '" class="numAnimate numSlideIn delay' + rand + '">' + sNumber.charAt(i) + '</span>';
    }
    console.log(s);
    return(s);
}

$(document).ready(function () {
  var sbf_user = getLocalStorage('sbf_user');
  var score=0;
  var params = {'method': 'get_user_score_level', 'oauth_user_id': sbf_user.oauth_user_id};
  $.post(api_ws, params, function (response) {

    var user = response;
    score = user.score;
    //console.log(user);

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

    //// Animate Score
    var sScore = separateNumber(score);
    $('.your-pts-wrapper .num-transition').html(sScore);

    $('ul.points-act').empty();
    var params = {'method': 'get_latest_ten_score_history', 'oauth_user_id': sbf_user.oauth_user_id};
    $.post(api_ws, params, function (response) {
      $.each(response, function(num, history){
        $('ul.points-act').append(
          '<li>\
              <div class="point-act-item">\
                  <div class="point-act-num">+' + history.score+ '</div>\
                  <div class="point-act-desc">\
                      <p>+' + history.score + ' ' + history.text + '<br>' + history.date + '</p>\
                  </div>\
              </div>\
          </li>'
        );
      });
    });
  });


  $('.profile-section.profile-routes-completed .routes-list:eq(0)').hide(); // Complete Routes
  $('.profile-section.profile-routes-completed .routes-list:eq(1)').hide(); // On Going Route

  var params = {'method': 'check_user_route_status', 'oauth_user_id': sbf_user.oauth_user_id};
  $.post(api_rt, params, function (response) {
    var routeComplete = response.routeComplete;
    var routeToGo = response.routeToGo;

    /*
    console.log('check route=====');
    console.log(routeComplete);
    console.log(routeToGo);
    console.log(routeToGo.length);
    */

    var numRouteComplete = Object.keys(routeComplete).length;
    var numRouteToGo = Object.keys(routeToGo).length;

    if(numRouteComplete > 0){
      $('.profile-section.profile-routes-completed .routes-list:eq(0) .profile-subtitle').html('Completed ' + numRouteComplete + ' routes');
      $('.profile-section.profile-routes-completed .routes-list:eq(0) .routes-list-row').html('');//clear
      //for(var i = 0; i < numRouteToGo; i++){
      $.each(routeComplete, function(route_id, route){
        $('.profile-section.profile-routes-completed .routes-list:eq(0) .routes-list-row').append('\
          <div class="routes-list-col">\
              <div class="route-box">\
                  <a class="modal-open" href="#">\
                      <figure>\
                          <img src="images/svg/route-profile.svg" alt="route"/>\
                      </figure>\
                      <h4>' + route.route_title + '</h4>\
                      <div class="route-date">\
                          ' + route.unlocked_date + '\
                      </div>\
                  </a>\
              </div>\
          </div>');
      });


      $('.profile-section.profile-routes-completed .routes-list:eq(0)').show();
    }

    if(numRouteToGo > 0){

      $('.profile-section.profile-routes-completed .routes-list:eq(1) .profile-subtitle').html('On going ' + numRouteToGo + ' routes');
      $('.profile-section.profile-routes-completed .routes-list:eq(1) .routes-list-row').html('');//clear
      //for(var i = 0; i < numRouteToGo; i++){
      $.each(routeToGo, function(route_id, route){
        $('.profile-section.profile-routes-completed .routes-list:eq(1) .routes-list-row').append('\
          <div class="routes-list-col">\
              <div class="route-box">\
                  <a class="modal-open" href="#">\
                      <figure>\
                          <img src="images/svg/route-profile.svg" alt="route"/>\
                      </figure>\
                      <h4>' + route.route_title + '</h4>\
                      <div class="route-date">\
                          ' + route.unlocked_date + '\
                      </div>\
                  </a>\
              </div>\
          </div>');
      });


      $('.profile-section.profile-routes-completed .routes-list:eq(1)').show();

    }
  });

  ///// Hide for Guardian Minutes
  $('.profile-section.profile-guardian-mins .profile-subtitle:eq(0)').hide();
  $('.guardian-mins-wrapper .mins-stat').hide();

  /// hide buton
  $('.your-pts-wrapper .button').hide();



  /////// Message New checking
  $('.profile-msg span').css('display', 'none');

  var params = {'method': 'get_new_message_num', 'oauth_user_id': sbf_user.oauth_user_id};
  $.post(api_ws, params, function (response) {
    //console.log(response);
    var c = response;
    var num = c.new_message;
    if(num>0){
      $('.profile-msg span').text(num);
      $('.profile-msg span').css('display', 'block');
    }
  });


});
