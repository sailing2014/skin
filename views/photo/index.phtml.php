<html lang="cn">
<head>
<meta charset="utf-8">
<title>测试专用</title>
<link rel="stylesheet"  href="/assets/css/common.css" type="text/css" />
<link rel="stylesheet"  href="/assets/css/style.css" type="text/css" />
</head>
<body>

 
<div id="intro">		
			 
                     <hr>			 
			  
                    <div>uid: <input id='btn_uid'  placeholder="用户id"></div>
        	 
        	<div>开始时间戳: <input id='time_start' placeholder="开始时间戳"></div>
	<div>结束时间戳: <input id='time_end' placeholder="结束时间戳"></div>
        
        	<hr>        	 
                <div><button id='btn_submit'>获取照片列表信息</button></div> 
                    <hr>                    
        <div class="cfCon">
            <div class="cfbox">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="cfTable">
                  <tbody id="photo_list">  
                     <tr>
                       <th>id</th>
                       <th>用户id</th>
                       <th>蓝牙地址</th>
                       <th>光源</th>
                       <th>拍摄部位</th>
                       <th>照片路径</th>
                       <th>分析结果</th>
                       <th>拍摄时间</th>
                       <th>上传时间</th>
                     </tr>   
                </tbody>
             </table>      
            </div>
         </div>

<script src="/assets/js/jquery.min.js"></script>
<script type="text/javascript">
var url = "/v1/user/analytics/intl";

$('#btn_submit').click(function(){ 
                var uid = $('#btn_uid').val();
                var start = $('#time_start').val();
                var end = $('#time_end').val();
                if( !uid){
                        alert("uid不填是查不到滴！");                   
                }else{
                        var get_url = url+"/" + uid + "?page=1&size=300&start=" + start + "&end=" + end;
                        $.ajax({
                            type: "GET",
                            url: get_url, 
                            success: function (json) {
                                    // 模拟result 有数据和无数据的结果
                                    if (json.data.length) {           // 判断是否为空                                
                                        var showcontent = "<tr><th>id</th><th>用户id</th>"+
                                                            "<th>蓝牙地址</th><th>光源</th><th>拍摄部位</th>" +
                                                            "<th>照片路径</th><th>分析结果</th><th>拍摄时间</th>"+
                                                            "<th>上传时间</th></tr>"; 

                                        var list = json.data;
                                        //console.log(list);                               
                                        for (var i = 0; i < list.length; i++) {                                    
                                                if( i % 2 == 0){
                                                    showcontent +=  '<tr>'+
                                                                      '<td>'+ i   + '</td>'  + 
                                                                      '<td>'+ list[i]["uid"]   + '</td>' +
                                                                      '<td>'+ list[i]["bta"]   + '</td>' +
                                                                      '<td>'+ list[i]["type"]   + '</td>' +
                                                                      '<td>'+ list[i]["body_part"]   + '</td>' +
                                                                      '<td>'+ list[i]["url"]    + '</td>' + 
                                                                      '<td>'+ JSON.stringify(list[i]["results"])  + '</td>' +
                                                                      '<td>'+ list[i]["time"]    + '</td>' +
                                                                      '<td>'+ list[i]["create_at"]    + '</td>' +
                                                                    '</tr>' ;
                                                }else{
                                                        showcontent +=  '<tr>'+
                                                                      '<th>'+ i   + '</th>'  + 
                                                                      '<th>'+ list[i]["uid"]   + '</th>' +
                                                                      '<th>'+ list[i]["bta"]   + '</th>' +
                                                                      '<th>'+ list[i]["type"]   + '</th>' +
                                                                      '<th>'+ list[i]["body_part"]   + '</th>' +
                                                                      '<th>'+ list[i]["url"]    + '</th>' + 
                                                                      '<th>'+ JSON.stringify(list[i]["results"]) + '</th>' +
                                                                      '<th>'+ list[i]["time"]    + '</th>' +
                                                                      '<th>'+ list[i]["create_at"]    + '</th>' +
                                                                    '</tr>' ;    
                                            }                                                              
                                        }
                                    }else{
                                        showcontent = "没数据,不要拉了..";
                                    }

                                    $("#photo_list").html(showcontent);
                                   }
                        }); 
                }
            });
</script>
</body>
</html>
