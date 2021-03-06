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
<title>产品详情</title>
<link rel="stylesheet"  href="/assets/css/common.css?v=1.0.1" type="text/css" />
<link rel="stylesheet"  href="/assets/css/style.css?v=1.0.3" type="text/css" />
</head>
<body>
<!--头部标题-->
<!--<div class="skinTop">
    <a class="leftarrow" href="#back"><img src="/assets/images/leftarrow.png" width="9" height="17" alt=""/></a>
<h1>产品详情</h1>
<div class="right"><a href="#" id="collect" class="collect <?php if ($product && $product['collect']) { ?> cback_red <?php } else { ?>cback_plain<?php } ?>" onclick="collect('<?php echo $token; ?>','<?php echo $product['product_id']; ?>')"></a><a href="#share"><img src="/assets/images/share.png" width="17" height="17" alt=""/></a></div>
</div>-->
<!--头部标题-->
<!--头部产品-->
<?php if ($product) { ?>
<?php if ($product['recommend_type'] == 1) { ?><div class="toptips">美肤营推荐</div> <?php } ?>
<div class="prodtop">
	<div><img src="<?php echo $product['image']; ?>" width="125" height="125" alt="<?php echo $product['title']; ?>"/></div>
<p class="prodtopName"><?php echo $product['brand_title']; ?>  <?php echo $product['title']; ?></p>
<div>
<span><span class="see"></span><span><?php echo $product['pageView']; ?></span></span>
<span class="pl10 pr10"><span class="zan"></span><span><?php echo $product['favnum']; ?></span></span>
<span><span class="hate"></span><span><?php echo $product['tearnum']; ?></span></span>
</div>
<?php if ($product['recommend_type'] == 1) { ?><i class="advicetips"></i><?php } ?>
</div> 
<!--头部产品-->
<!--产品介绍-->
<div class="prodIntro">
  <div class="prodList">
  <ul>
  <li class="clearfix"><span class="floatleft">主要功效:</span><span class="floatright"><?php if ($product['usage']) { ?><?php foreach ($product['usage'] as $v) { ?><i class="effect"><?php echo $v; ?></i><?php } ?><?php } ?></span></li>
  <li class="clearfix"><span class="floatleft">参考售价:</span><span class="floatright"><span class="f12">￥</span> <?php echo $product['price']; ?>  <?php if ($product['scale']) { ?>/  <?php echo $product['scale']; ?> <?php } ?></span></li>
  <li style="border:none;"><p>肤质建议:</p>
  <div class="skinbox">
  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="skinTable">
  <tbody>
    <tr>
      <th>重干</th>
      <th>轻干</th>
      <th>轻油</th>
      <th style="border:none;">重油</th>
    </tr>
    <tr>
      <td><i class=<?php if ($product['unfitlist']['重干']) { ?>"no"<?php } else { ?>"yse"<?php } ?>></i></td>
      <td><i class=<?php if ($product['unfitlist']['轻干']) { ?>"no"<?php } else { ?>"yse"<?php } ?>></i></td>
      <td><i class=<?php if ($product['unfitlist']['轻油']) { ?>"no"<?php } else { ?>"yse"<?php } ?>></i></td>
      <td><i class=<?php if ($product['unfitlist']['重油']) { ?>"no"<?php } else { ?>"yse"<?php } ?>></i></td>
    </tr>
     <tr>
      <th>重敏</th>
      <th>轻敏</th>
      <th>轻耐</th>
      <th style="border:none;">重耐</th>
    </tr>
     <tr>
      <td><i class=<?php if ($product['unfitlist']['重敏']) { ?>"no"<?php } else { ?>"yse"<?php } ?>></i></td>
      <td><i class=<?php if ($product['unfitlist']['轻敏']) { ?>"no"<?php } else { ?>"yse"<?php } ?>></i></td>
      <td><i class=<?php if ($product['unfitlist']['轻耐']) { ?>"no"<?php } else { ?>"yse"<?php } ?>></i></td>
      <td><i class=<?php if ($product['unfitlist']['重耐']) { ?>"no"<?php } else { ?>"yse"<?php } ?>></i></td>
    </tr>
  </tbody>
</table>

  </div>
  </li>
  </ul>
  </div>
  <!--表格以上的内容-->
  <div class="prodCF">
  <p>肤质建议:</p>
  <div class="prodCFList">
  <ul>
      
<?php if ($product['acne_risk']) { ?><a href="../riskList?id=<?php echo $product['product_id']; ?>&type=2&token=<?php echo $token; ?>"><?php } ?>
 <li class=" bggrey clearfix">
 <span class="floatleft">易致痘成分</span>
 <span class="floatright"><?php if ($product['acne_risk']) { ?><span class="f_red"><?php echo $product['acne_risk']; ?></span>种<?php } else { ?> 无<?php } ?></span>
 </li>
   <?php if ($product['acne_risk']) { ?></a><?php } ?>
   
  <?php if ($product['sensitization']) { ?><a href="../riskList?id=<?php echo $product['product_id']; ?>&type=3&token=<?php echo $token; ?>"><?php } ?>
 <li class="clearfix">
 <span class="floatleft">易致敏成分</span>
 <span class="floatright"><?php if ($product['sensitization']) { ?><span class="f_red"><?php echo $product['sensitization']; ?></span>种<?php } else { ?> 无<?php } ?></span>
 </li>
<?php if ($product['sensitization']) { ?></a><?php } ?>
   
 <?php if ($product['safety']) { ?><a href="../riskList?id=<?php echo $product['product_id']; ?>&type=4&token=<?php echo $token; ?>"><?php } ?>
  <li class="bggrey clearfix">
  <span class="floatleft">孕妇哺乳期慎用成分</span>
  <span class="floatright"><?php if ($product['safety']) { ?><span class="f_red"><?php echo $product['safety']; ?></span>种<?php } else { ?> 无<?php } ?></span>
  </li>
  <?php if ($product['safety']) { ?></a><?php } ?>
  
 <a href="../riskList?id=<?php echo $product['product_id']; ?>&type=1&token=<?php echo $token; ?>"> 
     <li class="clearfix" style="border-bottom:1px solid #d4d4d4;">
    <span class="floatleft">功效成分</span>
    <span class="floatright"><img src="/assets/images/rightarrow.png" width="9" height="17" alt="" class="mid"/></span>
    </li>
 </a>
  <a href="../elementList/<?php echo $product['product_id']; ?>?token=<?php echo $token; ?>"> 
  <li class="clearfix">
  <span class="floatleft"> <span class="f_red">全部成分</span></span>
  <span class="floatright"><img src="/assets/images/rightarrow.png" width="9" height="17" alt="" class="mid"/></span>
  </li></a> 
  </ul>
  </div>
  </div>
</div>
<input type="hidden" name="pid" id="pid" value="<?php echo $product['product_id']; ?>" />
<input type="hidden" name="token" id="token" value="<?php echo $token; ?>" />

  <!--新增评论-->
  <div class="commentTop">用户点评：</div> 
  <div class="commentCon">
       <input type="hidden" name="comment-list-pages" id="comment-list-pages" value="0" />
        <ul></ul>
  </div>
</div>
<!--新增评论-->

<!--产品介绍-->
<script src="/assets/js/jquery.min.js"></script>
<script src="/assets/js/collect.js?v=1.0.7"></script>
<script src="/assets/js/fav.js?v=1.0.2"></script>
<script type="text/javascript">
    (function (win) {
    var topTips = document.getElementsByClassName('toptips')[0];
    var adviceTips = document.getElementsByClassName('advicetips')[0];
    
    if(topTips && adviceTips){
        win.onscroll = function () {
            var t = document.documentElement.scrollTop || document.body.scrollTop;
            if (t >= 102) {
                topTips.style.display = 'block';
                adviceTips.style.display = 'none';
            } else {
                topTips.style.display = 'none';
                adviceTips.style.display = 'block';
            }
        };
    }
})(window);


var url = "/v1/message/html/comments/"+$('#pid').val();
var token =  $('#token').val();       
var pages = Number($(nowpagebox).val()) + 1;                
var nowpagebox = '#comment-list-pages';
var returnbox = '.commentCon ul';
var showcontent = '';
var flag = 0;
$(window).scroll(function(){        
        if($(window).scrollTop() == $(document).height() - $(window).height()){
            if (flag == 0) {    
                flag = 1;
                loading();                
                //获得当前频道页数
                var pages = Number($(nowpagebox).val()) + 1;
                $.ajax({
                    type: "GET",
                    url: url+"?page="+pages,      
                    headers:{token: token},
                    success: function (json) {
                            // 模拟result 有数据和无数据的结果
                            if (json.data.length) {           // 判断是否为空                                
                                var showcontent = "";                                
                                var list = json.data;
                                //console.log(list);
                                var curr = "";
                                var onclick = "";
                                for (var i = 0; i < list.length; i++) {
                                    curr = "good_plain";
                                    onclick  = "fav('"+ token +"','"+list[i]["comment_id"]+"','"+ i +"')";

                                    if(list[i]["fav"]){
                                        curr = "good_red";
                                    }
                                    showcontent +=  '<li><div class="commentTit clearfix"><div class="leftpericon">' +
                                    '<img src="' + list[i]["image"] + '" width="34" height="34" alt="" /></div>' +
                                    '<div class="righttxt"><p class="p1">' + list[i]["nickname"] + '</p><p class="p2">'+ list[i]["skin"] + '</p></div></div>' +

                                    '<div class="commentTxt clearfix"><div class="f14">' + list[i]["content"] + '</div>' +
                                    '<div class="gooddiv"><span id="comment_fav_'+ i + '" class="good '+ curr + '" onclick="' + onclick +
                                    '"></span><span id="favnum_'+ i +'">'+ list[i]["favnum"] +'</span></div></div></li>';
                                }
                                $("" + returnbox).append(showcontent);
                                $(nowpagebox).val(pages);
                                flag = 0;
                            } else {
                                flag = 1;                                      
                                $(".commentCon" ).append("<p style='text-align: center;color: #ff4d4d'>到头了</p>");
                                $('.loading').remove(); 
                            }
                            $('.loading').remove();
                           }
               }); 
            }
        }
    });    

</script>
<?php } else { ?>
no content
<?php } ?>
</body>
</html>