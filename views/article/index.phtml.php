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
        <title>文章详情</title>

        <link rel="stylesheet"  href="/assets/css/common.css" type="text/css" />
        <link rel="stylesheet"  href="/assets/css/style.css?v=1.2" type="text/css" />
    </head>
    <body style="background:#f4f4f4">
        <!--头部标题-->
<!--        <div class="skinTop">
            <a class="leftarrow" href="#back"><img src="/assets/images/leftarrow.png" width="9" height="17" alt=""/></a>
            <h1>文章详情</h1>
            <div class="right"><a href="#" id="collect" class="collect <?php if ($article['collect']) { ?> cback_red <?php } else { ?>cback_plain<?php } ?>" onclick="collectArticle('<?php echo $token; ?>','<?php echo $article['encyclopedia_id']; ?>')"></a><a href="#share"><img src="/assets/images/share.png" width="17" height="17" alt=""/></a></div>
        </div>-->
        <!--头部标题-->
        
        <?php if ($article) { ?>
        <div class="articleCon">
            <p><?php echo $article['title']; ?></p>
            <div class="pericondiv"><i class="pericon"><img src="<?php echo $article['from_image']; ?>" width="45" height="45" alt="<?php echo $article['from_nickname']; ?>"/></i>
            <?php if ($article['from_nickname']) { ?><?php echo $article['from_nickname']; ?> <?php } else { ?>匿名 <?php } ?>    
           <span>(<?php echo $article['pageView']; ?>人浏览)</span>                       
            </div>    
        <p><img src="<?php echo $article['img']; ?>" width="100%" height="100%" alt="<?php echo $article['title']; ?>"/></p>
        <div class="content"
        <p class="aticletxt">
            <?php if ($article['content']) { ?><?php echo $article['content']; ?><?php } else { ?> 小编很懒，啥都没留下 。。。<?php } ?>
        </p>
        </div>
        </div>
        <?php } else { ?>
        <p class="aticletxt">no content </p>
        <?php } ?>



        <script src="/assets/js/jquery.min.js"></script>
        <script src="/assets/js/collect.js?v=1.0.4"></script>
    </body>
</html>
