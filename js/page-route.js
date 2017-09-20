var route_api = "https://singhabeerfinder.com/bfd2/webservice/route-api.php";

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
  var route_id =1;
  var routeHTML = "";
  var routeSvgHTML = "";
  var baseHTML = "";
  var completed_point = 0;

  routeHTML = $('#route-id-x').html();

  // get - sbf_user & bases status
  var params = {'method': 'get_route_svg', 'route_id': route_id};
  $.post(route_api, params, function (response) {

    var route = response.route;
    //var bases = response.bases;
    var basesHTML = decodeHtml(route.routes_html);
    completed_point = route.completed_point;

    console.log(route);
    //console.log(basesHTML);
    //console.log(completed_point);

    routeHTML = routeHTML.replace('[xxRouteBaseMarkerxx]', basesHTML);

    //console.log(routeHTML);

    routeSvgHTML = route['svg'];
    var imageBackground = route['image'];
    //routeHTML = routeHTML.replace('images/upload/route-bg-01.jpg', imageBackground);
    //console.log(routeHTML);

    var svg = decodeHtml(route.svg);
    //console.log(route.svg);
    //console.log(svg);
    //$('#route-id-' + route_id +' .route-path').html(svg);
    routeHTML = routeHTML.replace('[xxSVGxx]',svg);
    //$('.route-pts').html(completed_point + '<span>PTS.</span>');
    /*$('.route-item-details > .route-pts').html(function(){
      var ret = completed_point + '<span>PTS.</span>';
      console.log('ret: ' + ret);
      return ret;
    });
    */
    routeHTML = routeHTML.replace('[xxPTSxx]', completed_point);

    $('#route-id-x').html('<div id="route-id-' + route_id + '">' + routeHTML + "</div>");
    $('#route-id-' + route_id + ' .bg-image').css('background-image', function(){
      var bg = ('url(' + imageBackground + ')');
      return bg;
    });

    //console.log(routeHTML);

  });
});
