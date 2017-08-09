<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Cache-Control" content="max-age=0" />
<meta name="MobileOptimized" content="240" />
<meta name="apple-touch-fullscreen" content="yes" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name = "format-detection" content="telephone = no" />
<meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" id="viewport" name="viewport">
<title>成分详情</title>
<link rel="stylesheet"  href="/assets/css/common.css" type="text/css" />
<link rel="stylesheet"  href="/assets/css/style.css" type="text/css" />
</head>
<body>
    <!--头部标题-->
<!--<div class="skinTop">
<a class="leftarrow" href="javascript:goUrl();"><img src="/assets/images/leftarrow.png" width="9" height="17" alt=""/></a>
<h1>成分详情</h1>
<div class="right"><a href="#" id="collect" class="collect <?php if ($collect) { ?> cback_red <?php } else { ?>cback_plain<?php } ?>" onclick="collect('<?php echo $token; ?>','<?php echo $pid; ?>')"></a><a href="#share"><img src="/assets/images/share.png" width="17" height="17" alt=""/></a></div>
</div>-->
    <!--头部标题-->
    <?php if ($component) { ?>
<div class="prodIntro">
  <div class="prodList">
  <ul>
  <li class="clearfix"><span class="floatleft"><?php echo $component['title']; ?></span><span class="floatright"><?php echo $component['EN_title']; ?></span></li>
  <li class="clearfix"><span class="floatleft">作用</span><span class="floatright"><?php echo $component['usage']; ?></span></li>
  <li style="border:none;"><p>详细说明:</p>
  <div class="skinbox">
  <span class="f12 f_grey"><?php echo $component['description']; ?></span>
  </div>
  </li>
  </ul>
  </div>
  <!--表格以上的内容-->
</div>
    <?php } else { ?>
    no content
    <?php } ?>
<script src="/assets/js/jquery.min.js"></script>
 <script src="/assets/js/collect.js?v=1.0.3"></script>
</body>
</html>