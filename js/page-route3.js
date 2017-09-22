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

$(document).ready(function () {
  var arrRoutes = [1,5];
  var baseHTML = "";
  var allHTML = "";
  var completed_point = 0;

  routeHTML = $('#route-id-x').html();
$('.route-item-wrapper').html(''); /// clear

  $.get("route-part.html", function( my_var ) {
    // my_var contains whatever that request returned
    var partHtml = my_var;
    for( var i=0 ; i<arrRoutes.length; i++){
      var k = i+1;
      var thisHtml = partHtml;
      var route_id = arrRoutes[i];

      thisHtml = thisHtml.replace('route-id-x', 'route-id-' + route_id);


      var routeSvgHTML;
      var baseHTML;
      var completed_point;
      var background;

      var params = {'method': 'get_route_svg', 'route_id': route_id};
      $.post(route_api, params, function (response) {
        var route = response.route;
         routeSvgHTML = decodeHtml(route.svg);
         baseHTML = decodeHtml(route.routes_html);
         completed_point = route.completed_point;
         background = route.image;

        console.log(completed_point);
        thisHtml = thisHtml.replace('[xxPTSxx]', completed_point);
        thisHtml = thisHtml.replace('[xxSVGxx]', routeSvgHTML);
        thisHtml = thisHtml.replace('[xxRouteBaseMarkerxx]', baseHTML);
        /*
        console.log(route_id);
        console.log(completed_point);
        console.log(thisHtml);
        */


        //allHTML += thisHtml;
        //console.log(allHTML);
        console.log(thisHtml);
        $('.route-item-wrapper').append(thisHtml);
      });

    }

    //$('.route-item-wrapper').html(allHTML);
}, 'html');

  /*
  // get - sbf_user & bases status
  var params = {'method': 'get_route_svg', 'route_id': route_id};
  $.post(route_api, params, function (response) {

    var route = response.route;
    var basesHTML = decodeHtml(route.routes_html);
    completed_point = route.completed_point;

    console.log(completed_point);

    routeHTML = routeHTML.replace('[xxRouteBaseMarkerxx]', basesHTML);


    routeSvgHTML = route['svg'];
    var imageBackground = route['image'];

    var svg = decodeHtml(route.svg);
    routeHTML = routeHTML.replace('[xxSVGxx]',svg);

    routeHTML = routeHTML.replace('[xxPTSxx]', completed_point);

    $('#route-id-x').html('<div id="route-id-' + route_id + '">' + routeHTML + "</div>");
    $('#route-id-' + route_id + ' .bg-image').css('background-image', function(){
      var bg = ('url(' + imageBackground + ')');
      return bg;
    });

    //console.log(routeHTML);

  }); // post
  */
});
