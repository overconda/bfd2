
jQuery(document).ready(function() {

	"use strict";

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
	console.log(param);
	if(param['page']=='challenge'){
		console.log('in challenge..');
		$('.button a').text('Challenge Again');
	}

	var api_countdown = "https://www.singhabeerfinder.com/webservice/countdown.php";
	var myInterval;

	var base_id = param['base_id'];
	var route_name, base_name, base_no;
	var params = {'method': 'get_base_info', 'base_id': base_id};
  $.post(api_countdown, params, function (response) {
		route_name = response.route_title;
		base_name = response.base_title;
		base_no = response.base_no;
		console.log(response);
		//console.log(route_name);
		//console.log(base_name);

		$('.base-num').text(base_no);
		$('.base-num + h1').text(base_name);
		$('.meta-route > span').text(route_name);
	});


	var startTime;
	var sbf_user = getLocalStorage('sbf_user');

	function parseDate(date) {
	  const parsed = Date.parse(date);
	  if (!isNaN(parsed)) {
	    return parsed;
	  }
	  return Date.parse(date.replace(/-/g, '/').replace(/[a-z]+/gi, ' '));
	}


	Date.prototype.addMinutes = function(minutes) {
      this.setMinutes(this.getMinutes() + minutes);
      return this;
  };

	var params = {'method': 'get_start_countdown_time', 'oauth_user_id': sbf_user.oauth_user_id};
  $.post(api_countdown, params, function (response) {
		startTime = response.time;
	});

	function Countdown(){
		var hour = 60*60*1000;
	  var gmt = 14;
	  var offset = gmt * hour;

		offset =0;

		var MaxMinute = 3;
		var minute = 60000;
		//var Now = new Date();
		//var Begin = new Date(startTime);
		//Begin.addMinutes(MaxMinute);	//// 3 minutes in actual

		var now = getYMDdate();
	  console.log(' now .. ' + now);
	  //var dateOld = parseDate(latest);
	  //var dateNew = parseDate(now);
	  var dateStart = new Date(startTime.replace(/-/g,'/'));
	  var dateStop = new Date(now);



		//var min = Diff/1000/60;
		console.log(dateStart + ' // Start');

		var ThreeMinutest = dateStart.setMinutes(dateStart.getMinutes() + MaxMinute);

		console.log(dateStart + ' // Start + 3 minutes');


		var Diff = Math.abs(ThreeMinutest - dateStop);
		//Diff -= offset;
		//Diff += (MaxMinute * minute);
		var min = Diff/1000/60;

		console.log(dateStop + ' ///////// Now');
		console.log(Diff);
		console.log(min);

		/*
		var ThreeMinutes = Begin;
		var Diff = ThreeMinutes-Now;
		var min = Diff/1000/60;
		*/
		var r = min % 1;
		var sec = Math.floor(r * 60);
		var s = sec;
		if (sec < 10) {
				sec = '0'+sec;
		}
		min = Math.floor(min);



		if((min<=0 && s <=0)||min>3){
			/// Finish countdown
			$('#countdown').text('00:00');
			clearInterval(myInterval);

			/// delete data
			var params= {'method': 'delete_countdown_time', 'oauth_user_id': sbf_user.oauth_user_id};
		  $.post(api_countdown, params, function (){});

			$('.button-sticky .button').removeClass('button-disable');
		}else{
			$('#countdown').text(min+':'+sec);
		}


	}

	myInterval = setInterval(Countdown, 1000);


	/* =============================================
	 Start - Utility Functions
	 ================================================ */

	/**
	 * Get localStorage with key and return object
	 * @param {type} key
	 * @returns {undefined|JSON.parse.j|Array|Object}
	 */
	function getLocalStorage(key) {
	  return JSON.parse(window.localStorage.getItem(key)) || undefined;
	}

	/**
	 * Set localStorage with key,value
	 * @param {type} key
	 * @param {type} value
	 * @returns {undefined}
	 */
	function setLocalStorage(key, value) {
	  window.localStorage.setItem(key, JSON.stringify(value));
	}

	/**
	 * Remove localStorage with key
	 * @param {type} key
	 * @returns {undefined}
	 */
	function removeLocalStorage(key) {
	  window.localStorage.removeItem(key);
	}

	function getYMDdate(){
	  var d = new Date()
	  var datestring = d.getFullYear() + "/" + ("0" +(d.getMonth()+1)).slice(-2) + "/" + d.getDate() + " " + ("0" +d.getHours()).slice(-2) + ":" + ("0" +  d.getMinutes()).slice(-2) + ":" + ("0" + d.getSeconds()).slice(-2);

	  return datestring;
	}


});
