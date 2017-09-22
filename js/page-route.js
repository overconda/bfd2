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
    var partHtml = my_var;


      var routeSvgHTML;
      var baseHTML;
      var completed_point;
      var background;

      var params = {'method': 'get_all_routes', 'orderby': 'ID', 'order': 'desc'};
      $.post(route_api, params, function (response) {
        $.each(response.route,function(index, route){
          var route_id = route.ID;
          var completed_point = route.completed_point;
          var svg = decodeHtml(route.svg);
          var bases_html = decodeHtml(route.bases_html);
          var background = route.image;

          var thisHtml = partHtml;
          thisHtml = thisHtml.replace('route-id-x','route-id-' + route_id);
          thisHtml = thisHtml.replace('[xxSVGxx]',svg);
          thisHtml = thisHtml.replace('[xxPTSxx]', completed_point);
          thisHtml = thisHtml.replace('[xxRouteBaseMarkerxx]', bases_html);
          thisHtml = thisHtml.replace('data-bg-image="images/upload/route-bg-01.jpg"', 'style="background:url(\'' + background + '\'"');

          $('.route-item-wrapper').append(thisHtml);
          console.log(thisHtml);
        });
      });

}, 'html');
});
