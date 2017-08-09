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
<title>全部成分</title>
<link rel="stylesheet"  href="/assets/css/common.css" type="text/css" />
<link rel="stylesheet"  href="/assets/css/style.css" type="text/css" />
</head>
<body>
    <!--头部标题-->
<!--<div class="skinTop">
<a class="leftarrow" href="../index/<?php echo $product['product_id']; ?>?token=<?php echo $token; ?>&f=1"><img src="/assets/images/leftarrow.png" width="9" height="17" alt=""/></a>
<h1>全部成分</h1>
<div class="right"><a href="#" id="collect" class="collect <?php if ($collect) { ?> cback_red <?php } else { ?>cback_plain<?php } ?>" onclick="collect('<?php echo $token; ?>','<?php echo $product['product_id']; ?>')"></a></div>
</div>-->
    <!--头部标题-->
<?php if ($product) { ?>
<div class="cfCon">
   <div class="cfbox">
       <table width="100%" border="0" cellpadding="0" cellspacing="0" class="cfTable">
         <tbody>  
    <tr>
      <th>成分名称</th>
      <th>功效成分</th>
      <th>易致痘成分</th>
      <th>易致敏成分</th>
      <th style="border:none;">孕哺期慎用</th>
    </tr>   
    <?php if ((isset($product['components'])) && ($product['components'])) { ?>
   <?php foreach ($product['components'] as $index => $v) { ?>
    <tr>
        <?php if (((($index) % 2) == 0)) { ?>
            <td> <a href="../element/<?php echo $v['component_id']; ?>?pid=<?php echo $product['product_id']; ?>&token=<?php echo $token; ?>"><?php echo $v['title']; ?></a></td>
           <td><?php if ($v['active']) { ?><i class="yse"><?php } ?></i></td>
           <td><?php if ($v['acne_risk']) { ?><i class="yse"><?php } ?></i></td>
           <td><?php if ($v['sensitization']) { ?><i class="yse"><?php } ?></td>
           <td><?php if ($v['safety']) { ?><i class="yse"><?php } ?></td>
      <?php } else { ?>
            <th> <a href="../element/<?php echo $v['component_id']; ?>?pid=<?php echo $product['product_id']; ?>&token=<?php echo $token; ?>"><?php echo $v['title']; ?></a></th>
            <th><?php if ($v['active']) { ?><i class="yse"><?php } ?></i></th>
            <th><?php if ($v['acne_risk']) { ?><i class="yse"><?php } ?></i></th>
            <th><?php if ($v['sensitization']) { ?><i class="yse"><?php } ?></i></th>
            <th style="border:none;"><?php if ($v['safety']) { ?><i class="yse"><?php } ?></i></th>
      <?php } ?>
    </tr> 
    <?php } ?>
    <?php } ?>
  </tbody>
</table>

</div>
 <?php } else { ?>
    no content
 <?php } ?>
 <script src="/assets/js/jquery.min.js"></script>
 <script src="/assets/js/collect.js?v=1.0.3"></script>
</body>
</html>