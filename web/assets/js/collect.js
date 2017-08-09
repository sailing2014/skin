function collect(token,pid){
    
    cbackChange();
    
    if(token){
        var url = "/v1/product/html/collect/"+pid;
        
        $.ajax({
                    type: "POST",
                    url: url,      
                    headers:{token: token},
                    data: {product_id: pid},
                    success: function (ret) {
                    }
               });
    }    
}

function cbackChange(){
    var cback = document.getElementById("collect").getAttribute("class");
    if(cback.indexOf("cback_red") >=0){
        document.getElementById( "collect" ).className = "collect cback_plain"; 
    }else{
        document.getElementById( "collect" ).className = "collect cback_red"; 
    }
}
function goUrl(){
    url = document.referrer;       
    location.href = url;    
}

function collectArticle(token,id){
    
   cbackChange();
    
    if(token){
        var url = "/v1/article/encyclopedia/html/collect/"+id;
        
        $.ajax({
                    type: "POST",
                    url: url,      
                    headers:{token: token},
                    success: function (ret) { 
                    }
               });
    }    
}

// loading 效果
function loading () {    
	var loadingHTML = '<div class="loading" style="position: relative;text-align:center"><div id="loading"><img src="/assets/images/loading.gif" width="18px" heigth="18px"></div></div>';
	$(document.body).append(loadingHTML);
}