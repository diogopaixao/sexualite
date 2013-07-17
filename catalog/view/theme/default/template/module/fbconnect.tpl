<style type="text/css">
.box.box-fbconnect .box-content .box-fbconnect-a {
	height: 40px;
	width: 182px;
	margin-bottom: 5px;
	background:url(facebuton1.fw.png) bottom;
	text-indent: -99999px;
	outline: none;
}
.box.box-fbconnect {
	float: left;
	height: auto;
	width: auto;
	display: block;
	line-height: 20px;
	font-weight: bolder;
	color: #FFF;
}
.box.box-fbconnect .box-content .box-fbconnect-a:hover {
	background-position: 0 0;
}
.box.box-fbconnect .box-content .box-fbconnect-a {
	float: left;
}
</style>
<div class="box box-fbconnect">
  <div class="box-heading"><?php echo $heading_title; ?></div>
  <div class="box-content">
	<a class="box-fbconnect-a" href="<?php echo $fbconnect_url; ?>"><?php echo $fbconnect_button; ?></a>
  </div>
</div>



