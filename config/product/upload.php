<?php
return array(
    'image'=>array(
        'allowtype'=>array('image/jpg','image/jpeg', 'image/png', 'image/jpeg', 'image/gif','image/bmp','image/x-png'),
        'maxsize'=>'2000000',   //2M
        'upload_foder'=>IMA_UPLOAD_PATH       
    ),
    'video'=>array(
        'allowtype'=>array('video/avi','video/mp4','video/3gpp','application/octet-stream'),
        'maxsize'=>'7340032',  //7M
        'upload_foder'=>VIDEO_UPLOAD_PATH        
    ),
    'audio'=>array(
        'allowtype'=>array('audio/wav','audio/mpeg','audio/m4a','audio/x-m4a','application/octet-stream'),
        'maxsize'=>'7340032',//7M
        'upload_foder'=>AUDIO_UPLOAD_PATH        
    )
    
    
);

