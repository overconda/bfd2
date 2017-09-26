var route_api = "https://www.singhabeerfinder.com/webservice/route-api.php";

function decodeHtml(str)
{
    var map =
    {
        '&amp;': '&',
        '&lt;': '<',
        '&gt;': '>',
        '&quot;': '"',
        '&#039;': "'"
    };
    return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function(m) {return map[m];});
}

function parseQuery()
{
  var currentURL = document.URL;
  var params = currentURL.split("?");
  var str = params[1];
  if(typeof str != "string" || str.length == 0) return {};
  var s = str.split("&");
  var s_length = s.length;
  var bit, query = {}, first, second;
  for(var i = 0; i < s_length; i++)
    {
    bit = s[i].split("=");
    first = decodeURIComponent(bit[0]);
    if(first.length == 0) continue;
    second = decodeURIComponent(bit[1]);
    if(typeof query[first] == "undefined") query[first] = second;
    else if(query[first] instanceof Array) query[first].push(second);
    else query[first] = [query[first], second];
    }
  return query;
//console.log(query)
}

var param = parseQuery();
var route_id = param['id'];

$(document).ready(function () {
  var params = {'method': 'get_route_svg', 'route_id': route_id};
  $.post(route_api, params, function (response) {
    console.log(response);
    var route = response.route;
    var completed_point = route.completed_point;
    var unlocked_base_point = route.unlocked_base_point;
    var extra_point = route.extra_point;
    var svg = decodeHtml(route.svg);
    var bases_html = decodeHtml(route.bases_html);
    var background = route.image;
    var route_title = route.route_title;

    $('.route-detail-pts-wrap.large-image').attr('data-large-image', background);
    //$('.route-detail-pts-wrap.large-image').css('background-image', 'url(\'' + background + '\')');
    $('#gradient-bg').attr('style', "background-image:url('" + background + "')");
    $('.route-detail-pts-wrap.large-image').attr('style', "background-image:url('" + background + "')");
    $('.route-svg-content.svg-ms').html(svg + bases_html);

    $('.point-col:nth-child(1) .num-transition').html('<span data-number="'+ completed_point + '" class="numAnimate numSlideIn delay4">' + completed_point + '</span>');
    $('.point-col:nth-child(2) .num-transition').html('<span data-number="'+ unlocked_base_point + '" class="numAnimate numSlideIn delay2">' + unlocked_base_point + '</span>');
    $('.point-col:nth-child(3) .num-transition').html('<span data-number="'+ extra_point + '" class="numAnimate numSlideIn delay6">' + extra_point + '</span><span>x</span>');
    console.log(completed_point);
    console.log(unlocked_base_point);
    console.log(extra_point);

    /*
    $('.base-info-1').addClass('base-active');
    $('.base-info-1 g').removeClass('group-unactive');
    $('.base-info-1 g').addClass('group-active');
    */








  });

  var sbf_user = getLocalStorage('sbf_user');
  var params = {'method': 'get_route_bases_user_unlocked', 'route_id': route_id, 'oauth_user_id': sbf_user.oauth_user_id};
  $.post(route_api, params, function (response) {
    console.log('get_route_bases_user_unlocked');
    //console.log(response);
    //$('.tabs_container').html(''); // clear
    $.each(response, function(base_id, data){
      //console.log(data);
      var base_no = data.base_no;
      var unlocked = data.unlocked;
      var base_title = data.base_title;
      var base_excerpt = data.base_excerpt;
      var unlockdClass ;
      var unlockIcon;

      if(unlocked==1){
        $('.base-info-' + base_no +'.base-marker').addClass('base-unlocked');
        unlockdClass = 'base-info-unlocked';
        unlockIcon = 'unlocked';
      }else{
        unlockdClass = 'base-info-lock';
        unlockIcon = 'lock';
      }

      var base_text = '<!--Base info ' + base_no + '-->\
      <div id="base-info-' + base_no + '" class="route-info-wrapper ' + unlockdClass + ' ">\
\
          <div class="lock-icon">\
              <img src="images/svg/' + unlockIcon + '.svg" alt="lock"/>\
          </div>\
\
          <div class="route-info">\
\
              <div class="route-num-box">' + base_no + '</div>\
\
              <div class="route-info-box">\
\
                  <h3>' + base_title + '</h3>\
\
                  <div class="route-info-desc">\
                      ' + base_excerpt + '\
                  </div>\
\
                  <div class="button button-small">\
                      <a href="unlocked.html">More</a>\
                  </div>\
\
              </div>\
\
          </div>\
\
      </div>\
      <!--End-->';


      //$('.tabs_container').append(base_text);

    });

    $('.route-details-container').tabulous({
      effect: 'custom'
      });

    $('.route-info-wrapper:first-child').addClass("showcustom");





    /* =============================================
    Route transition settings
    ================================================ */
    $('.route-transition .base-marker').on("click", function() {
      $('.route-transition .base-marker').each(function() {
        $(this).removeClass("base-active")
      });
      $(this).addClass("base-active baseClick");

      var groupActive = $(".group-active").clone();
      var groupUnActive = $(".group-unactive:first").clone();

      $('.group-active').each(function() {
        $(this).replaceWith(groupUnActive);
      });
      $(this).find(".group-unactive").replaceWith(groupActive);
      });

  });

  /*
  $('.route-svg-content .base-marker:eq(1)').addClass('base-active');
  $('.route-svg-content .base-marker:eq(2) g').removeClass('group-unactive');
  $('.route-svg-content .base-marker:eq(2) g').addClass('group-active');
  */

  /*
  $('.route-svg-content g:eq(1)').addClass('base-active');
  $('.route-svg-content g:eq(2)').removeClass('group-unactive');
  $('.route-svg-content g:eq(2)').addClass('group-active');
  */

}); /// document ready
