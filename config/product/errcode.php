<?php
return array(
    0 => 'Request successful!',
    200 => 'OK',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    403 => 'No Permission',
    404 => 'Not Found',
    500 => 'Internal Server Error',
    504 => 'Gateway Timeout',
    // common error
    400001 => 'Paramter Error!',
    400002 => 'internal token validation fail!',
    // user error code from 410000
    413110 => 'Register failed!',
    413114 => 'User get failed!',
    413115 => 'Login failed!',
    413116 => 'Token disabled!',
    413117 => 'Login failed!Please smsCode login first!',
    413181 => 'Short message send failed',
    413191 => 'Code validate failed',
    413201 => 'User does exist!',
    413211 => 'User does not exist!',
    413221 => 'Password validate fail!',
    413141 => 'User update failed!',
    413151 => 'User password update failed!',
    413232 => 'Token validate fail!',
    414001 => 'You already have applied for',
    414002 => 'You have already in here',
    415001 => 'invalid inviteCode ',
    // test error code from 420000 (It's come from devicedata service)     
    427110 => 'Add failed!',
    427115 => 'Delete failed!',
    427113 => 'Update failed!',
    427111 => 'Get failed!',
    427112 => 'Set failed!',
    
    420001 => 'Update component failed, this component title already exists!',
    420002 => 'Add component failed, this component title already exists!',
  
  // result error from 430000 t's come from devicedata service)  
    437111 => "This user takes no test yet!",    
    //plan from 440000
    
     //article from 450000
    450001 => "This user praised this article already!",
    450002 => "This user hasn't praised this article yet!",    
    450003 => "This user should take all type test",
    450004 => "This user character content doesn't exist!",
    450013 => "Collect faill! This user has collected this article already!",
    450014 => "Cancel collection fail! The user hasn't collected this article yet!",
    450015 => "Get collection list fail! The user hasn't collected any article yet!",
    
    //message from 460000
    460001 => "Set push account fail,push_id parameter error!",
    460002 => "Get push account fail!",
    460003 => "Set push account fail!",
    460004 => "Delete push account fail!",
    460005 => "Get user message list fail!",
    460006 => "Push fail!",
    460007 => "Access denied,set message status read fail!",
    
       //comments from 465000
    465001 => "Get comments fail!",
    
    //feedback from 470000
    470001 => "Add feedback failed!",
    
    //upload from 480000
    480001 => "Type of file is not allowed!",
    480002 => "Size of file is too big!",
    480003 => "File is empty!",
    480004 => "More than one file is uploaded!",
    480005 => "The file is not the uploaded one!",
    480006 => "Analyse image fail!",
    
    //product from 490000
    490001 => "This user praised  or tore this product already!",
    490002 => "This user hasn't praised or torn this product yet!",
    490003 => "Collect faill! This user collected this product already!",
    490004 => "Cancel collection fail! This user hasn't collected this product yet!",
    490005 => "Get collection list fail! This user hasn't collected any product yet!",
    
    //device from 49100
    491001 => "No permission to operate this device as you are not the owner!",
    491002 => "P2puid recycling failed!",
);

	