function fav(token,cid,i){
    fbackChange(i);
    
    if(token){
        var url = "/v1/message/html/comments/fav/"+cid;
        
        $.ajax({
                    type: "POST",
                    url: url,      
                    headers:{token: token},              
                    success: function (ret) {                        
                    }
               });
    }    
}

function fbackChange(i){  
    var id = "comment_fav_" + i;
    var favnum_id = "favnum_" + i;
    
    var fback = document.getElementById(id).getAttribute("class");
    var favnum = document.getElementById(favnum_id).innerHTML;
    
    if(fback.indexOf("good_red") >=0){        
        document.getElementById( id ).className = "good good_plain"; 
        document.getElementById(favnum_id).innerHTML = --favnum;
    }else{        
        document.getElementById( id ).className = "good good_red"; 
        document.getElementById(favnum_id).innerHTML = ++favnum;
    }
}