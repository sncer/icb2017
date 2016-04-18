<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>仿rrb</title>
		<!-- Bootstrap core CSS -->
		<link href="/icb/Application/Home/View//Public/static/css/bootstrap.min.css" rel="stylesheet">
			
		<link href="/icb/Application/Home/View//Public/static/css/jquery-accordion-menu.css" rel="stylesheet" type="text/css" />
		<link href="/icb/Application/Home/View//Public/static/css/font-awesome.css" rel="stylesheet" type="text/css" />
		
		<!-- Custom styles for this template -->
		<link href="/icb/Application/Home/View//Public/static/css/main.css" rel="stylesheet">

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
	      <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	      <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
	    <![endif]-->
	</head>

	<body>
		<div id="wrapper">
			<div id="header">
				
			</div>
			<div id="main">
				<div id="left">
					<div id="menu">
						<div id="jquery-accordion-menu" class="jquery-accordion-menu red">
							<ul id="demo-list">
							 
							    <li><a href="#"><i class="fa fa-long-arrow-right"></i>WELCOME</a></li>
								<li class="active"><a href="#"><i class="fa fa-long-arrow-right"></i>VENUE, GHENT, THE CITY</a></li>
								<li><a href="#"><i class="fa fa-long-arrow-right"></i>RRB-12 ORGANIZING COMMITTEE</a></li>
								<li><a href="#"><i class="fa fa-long-arrow-right"></i>RRB-12 SCIENTIFIC ADVISORY BOARD</a></li>
								<li><a href="#"><i class="fa fa-long-arrow-right"></i>CONFIRMED FACULTY AND CHAIRS</a></li>
								<li><a href="#"><i class="fa fa-long-arrow-right"></i>CALL FOR ABSTRACTS</a></li>
								<li><a href="#"><i class="fa fa-long-arrow-right"></i>PROGRAM</a>
									<ul class="submenu">
										<li><a href="#"><i class="fa fa-long-arrow-right"></i>MONDAY, MAY 30</a></li>
										<li><a href="#"><i class="fa fa-long-arrow-right"></i>TUESDAY, MAY 31</a></li>
										<li><a href="#"><i class="fa fa-long-arrow-right"></i>WEDNESDAY, JUNE 1</a></li>
									</ul>
								</li>
								<li><a href="#"><i class="fa fa-long-arrow-right"></i>FRINGE EVENTS</a>
									<ul class="submenu">
										<li><a href="#"><i class="fa fa-long-arrow-right"></i>VISIT OF THE STORA ENSO PLANT</a></li>
									</ul>
								</li>
								<li><a href="#"><i class="fa fa-long-arrow-right"></i>REGISTRATION</a></li>
								<li><a href="#"><i class="fa fa-long-arrow-right"></i>HOTEL ACCOMMODATION</a></li>
							    <li><a href="#"><i class="fa fa-long-arrow-right"></i>SPONSORS</a></li>
							    <li><a href="#"><i class="fa fa-long-arrow-right"></i>CONTACT</a></li>
							    <li><a href="#"><i class="fa fa-long-arrow-right"></i>PHOTO GALLERY</a></li>
							    <li><a href="#"><i class="fa fa-long-arrow-right"></i>PREVIOUS EDITIONS</a>
							    	<ul class="submenu">
										<li><a href="#"><i class="fa fa-long-arrow-right"></i>RRB1 : 2005</a></li>
										<li><a href="#"><i class="fa fa-long-arrow-right"></i>RRB2 : 2006</a></li>
										<li><a href="#"><i class="fa fa-long-arrow-right"></i>RRB3 : 2007</a></li>
										<li><a href="#"><i class="fa fa-long-arrow-right"></i>RRB4 : 2008</a></li>
										<li><a href="#"><i class="fa fa-long-arrow-right"></i>RRB5 : 2009</a></li>
										<li><a href="#"><i class="fa fa-long-arrow-right"></i>RRB6 : 2010</a></li>
										<li><a href="#"><i class="fa fa-long-arrow-right"></i>RRB7 : 2011</a></li>
										<li><a href="#"><i class="fa fa-long-arrow-right"></i>RRB7 : 2011 AWARDS</a></li>
										<li><a href="#"><i class="fa fa-long-arrow-right"></i>RRB8 : 2012</a></li>
										<li><a href="#"><i class="fa fa-long-arrow-right"></i>RRB9 : 2013</a></li>
										<li><a href="#"><i class="fa fa-long-arrow-right"></i>RRB10: 2014</a></li>
										<li><a href="#"><i class="fa fa-long-arrow-right"></i>RRB11: 2015</a></li>
									</ul>
							    </li>
							    <li><a href="#"><i class="fa fa-long-arrow-right"></i>LINKS</a></li>
							</ul>
							
						</div>
					</div>
				</div>
				<div id="right">
					<div class="content">
						<h3>Venue</h3>
					</div>
				</div>
			</div>
			<div id="footer">
				
			</div>
		</div>
		<!-- Bootstrap core JavaScript
    ================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="/icb/Application/Home/View//Public/static/js/jquery.min.js"></script>
		<script src="/icb/Application/Home/View//Public/static/js/bootstrap.min.js"></script>
		<script src="/icb/Application/Home/View//Public/static/js/jquery-accordion-menu.js" type="text/javascript"></script>
		<script src="/icb/Application/Home/View//Public/static/js/main.js"></script>
		<script type="text/javascript">
			$(function(){	
				//顶部导航切换
				$("#demo-list li").click(function(){
					$("#demo-list li.active").removeClass("active")
					$(this).addClass("active");
				})	
			})	
		</script>
		<script type="text/javascript">
			(function($) {
			$.expr[":"].Contains = function(a, i, m) {
				return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
			};
			function filterList(header, list) {
				//@header 头部元素
				//@list 无需列表
				//创建一个搜素表单
				var form = $("<form>").attr({
					"class":"filterform",
					action:"#"
				}), input = $("<input>").attr({
					"class":"filterinput",
					type:"text"
				});
				$(form).append(input).appendTo(header);
				$(input).change(function() {
					var filter = $(this).val();
					if (filter) {
						$matches = $(list).find("a:Contains(" + filter + ")").parent();
						$("li", list).not($matches).slideUp();
						$matches.slideDown();
					} else {
						$(list).find("li").slideDown();
					}
					return false;
				}).keyup(function() {
					$(this).change();
				});
			}
			$(function() {
				filterList($("#form"), $("#demo-list"));
			});
			})(jQuery);	
			</script>
			
			<script type="text/javascript">
			
				jQuery("#jquery-accordion-menu").jqueryAccordionMenu();
				
			</script>
	</body>

</html>