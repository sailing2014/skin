<?php

return array(
    '/v1/user' => array(
        array('', 'user::add', 'POST'),
        array('/{uid:\d{8,32}}', 'user::get', 'GET'),
        array('/{uid:\d{8,32}}', 'user::update', 'PUT'),
        array('/token', 'token::add', 'POST'),    
        array('/pwd/token', 'token::addByPwd', 'POST'), 
        array('/token/oauth', 'token::oauth', 'POST'),
        array('/token/oauth', 'token::checkIdentifier', 'GET'),
        array('/token/{uid:\d{8,32}}', 'token::delete', 'DELETE'), 
        array('/validation', 'user::requestSmsValidation', 'POST'),
        array('/mobile/{mobile:\S{4,32}}', 'user::checkResiterOrNot', 'GET'),
        array('/password', 'pwd::add', 'POST'),
        array('/weather','complex::weather','GET'),
        
        array('/intl/{uid:\d{8,32}}', 'userintl::get', 'GET'),
        array('/intl', 'userintl::getList', 'GET'),
        array('/analytics/intl', 'userintl::getAnalytics', 'GET'),
        array('/clocks/intl', 'userintl::getClocks', 'GET'),
        //push intl
        array('/todo/push/intl','userintl::pushTodo','POST'),
        
        //testers get user photo list
        array('/analytics/intl/{uid:\d{8,32}}', 'userintl::getAnalyticsByUid', 'GET'),
    ),
    '/v1/feedback' => array(
        array('', 'feedback::add', 'POST'),
    ),
    '/v1/test' => array(
        array('/types/intl','test::add',"POST"),
        array('/types','test::get',"GET"),
        array('/types/intl','test::intlGet',"GET"),
        array('/types/intl/{test_type:\d{1,6}}','test::update',"PUT"),
        array('/types/intl/{test_type:\d{1,6}}','test::getByType',"GET"),
        array('/types/intl/{test_type:\d{1,6}}','test::delete',"DELETE"),
        array('/intl/{doc_id:\S{6,40}}','problem::deleteBydocid',"DELETE"),
        array('/problems/intl','problem::add',"POST"),
        array('/problems/intl','problem::getAll',"GET"),
        array('/problems/{test_type:\d{1,6}}','problem::get',"GET"),        
        array('/problems/intl/{test_type:\d{1,3}}','problem::getIntl',"GET"),
        array('/problems/intl/{question_id:\S{32,40}}','problem::getIntlProblem',"GET"),
        array('/problems/intl/{question_id:\S{32,40}}','problem::update',"PUT"),
        array('/problems/intl/{question_id:\S{32,40}}','problem::deleteProblem',"DELETE"),
        
        array('/type/results/intl/{test_type:\d{1,6}}','test::setResult','POST'),
        array('/type/results/intl/{test_type:\d{1,6}}','test::getResult','GET'),
        array('/type/results/intl/{test_type:\d{1,6}}','test::deleteResult','DELETE'),
        array('/type/results/{test_type:\d{1,6}}','test::getResult','GET'),
        
        array('/type/config/intl/{test_type:\d{1,6}}','test::intlAddTestTypeConfig','POST'),
        array('/type/config/intl/{test_type:\d{1,6}}','test::intlGetTestTypeConfig','GET'),
        array('/type/config/intl/{doc_id:\S{29,40}}','test::intlUpdateTestTypeConfig','PUT'),
        array('/type/config/intl/{doc_id:\S{29,40}}','test::intlDeleteTestTypeConfig','DELETE'),
        array('/type/config/intl/{doc_id:\S{29,40}}','test::getTestTypeConfig','GET'),
        array('/type/config/intl','test::getAllTestTypeConfig','GET')
        
    ),
    '/v1/oss' => array(
        array('/token','storage::add',"POST"),
        array('/upload','storage::upload','POST')
    ),
    '/v1/detect' => array(
        array('/result','detect::add','POST'),
        array('/answers','detect::submit','POST')
    ),
    '/v1/storage' => array(
        array('/user_skin_list/{uid:\d{8,32}}','result::get','GET'),
        array('/total/{type:\d{1,6}}','result::getTotal','GET')
    ),
    '/v1/product' => array(
        array('/tags/{uid:\d{8,32}}','product::get','GET'),       
        array('/list','product::getList','GET'),
        array('/search','product::search','GET'),
        array('/filters/{filter_type:\d{1}}','product::filterType','GET'),
        array('/filter_list','product::filter','GET'),
        array('/detail/{product_id:\S{13,26}}','product::getDetail','GET'),
        array('/components/{product_id:\S{13,26}}','product::components',"GET"),
        array('/component_detail/{component_id:\S{24,36}}','product::componentDetail',"GET"),
        array('/favnum','product::favnum',"PUT"),
        array('/favnum','product::cancelFavnum',"DELETE"),
        array('/favnum/{product_id:\S{13,26}}','product::favnumCheck',"GET"),
        
        array('/html/collect/{product_id:\S{13,26}}','product::htmlCollect',"POST"),
        
        array('/collect/{product_id:\S{13,26}}','product::collect',"POST"),
        array('/collect/{product_id:\S{13,26}}','product::collectCancel',"DELETE"),
        array('/collect/list','product::getCollectList',"GET"),
        
        array('/component_list/intl','productintl::componentList',"GET"),
        array('/component_list/search/intl','productintl::searchComponent',"GET"),
        
        array('/filters/intl','productintl::addFilterType',"POST"),
        array('/filters/intl/{filter_type:\d{1}}','productintl::deleteFilter',"DELETE"),
        array('/filters/intl','productintl::updateFilterType',"PUT"),
        array('/filters/intl/{filter_id:\S{31,32}}','productintl::getFilterType',"GET"),
        array('/filters/intl/{filter_id:\S{31,32}}','productintl::deleteFilterType',"DELETE"),
        array('/filters/intl/{filter_type:\d{1}}','productintl::filterType','GET'),
        
        array('/component/intl','productintl::addComponent','POST'),
        array('/components/intl','productintl::addMultipleComponent','POST'),
        array('/component/intl/{component_id:\S{24,36}}','productintl::updateComponent','PUT'),
        array('/component/intl/{component_id:\S{24,36}}','productintl::deleteComponent','DELETE'),
        array('/component/intl/{component_ids:(\S{24,36}(,\S{24,36})*)}','productintl::deleteComponent','DELETE'),
        array('/component/intl/{component_id:(\S{24,36}(,\S{24,36})*)}','productintl::componentDetail','GET'),
        array('/components/intl/{product_id:\S{13,26}}','productintl::getProductComp','GET'),        
        array('/component/usage/intl','productintl::getCompUsage','GET'),
        array('/component/usage/intl/{usage_id:\S{34}}','productintl::getCompUsageById','GET'),
        array('/component/usage/intl','productintl::addCompUsage','POST'),
        array('/component/usage/intl/{usage_id:\S{34}}','productintl::updateCompUsage','PUT'),
        array('/component/usage/intl/{usage_id:\S{34}}','productintl::deleteCompUsage','DELETE'),
        
        array('/intl','productintl::addProduct','POST'),
        array('/search/intl','productintl::searchProduct','GET'),
        array('/intl/{product_id:\S{13,26}}','productintl::updateProduct','PUT'),
        array('/top/intl/{product_id:\S{13,26}}','productintl::setTop','PUT'),
        array('/intl','productintl::productList','GET'),
        array('/usage/intl','productintl::getUsageIdList','GET'),
        array('/intl/{product_id:\S{13,26}}','productintl::getProduct','GET'),
        array('/intl/{product_ids:(\S{13,26}(,\S{13,26})*)}','productintl::deleteProduct','DELETE'),
        array('/tags/intl','productintl::getTags',"GET"),
        array('/tags/intl','productintl::addTags',"POST"),
        array('/tags/intl/{tag_id:\S{30}}','productintl::updateTags',"PUT"),
        
        array('/usages/intl','productintl::usageList',"GET"),
    ),
   
    '/v1/article' => array(
        array('/tags','article::getTags','GET'),
        array('/characteristics','article::getCharacter','GET'),
        array('/tips','article::getTips','GET'),
        array('/daily','article::getDaily','GET'),
        array('/solutions','article::getSolutions','GET'),
        array('/search','article::search','GET'),
        array('/tags/list/{tag_id:(\S{30}(,\S{30})*)}','article::getListByTag','GET'),
        array('/encyclopedia_list/{list_type:\d{1}}','article::getEncyclopediaList','GET'),
        array('/encyclopedia/{encyclopedia_id:\S{31}}','article::getEncyclopedia','GET'),
        array('/encyclopedia/favnum','article::addFav','PUT'),
        array('/encyclopedia/favnum/cancel','article::deleteFav','DELETE'),
        array('/encyclopedia/favnum/check','article::queryFav','GET'),
        
        array('/encyclopedia/collect/{encyclopedia_id:\S{31}}','article::collect',"POST"),
        array('/encyclopedia/collect/{encyclopedia_id:\S{31}}','article::collectCancel',"DELETE"),
        array('/encyclopedia/collect/list','article::getCollectList',"GET"),
        
         array('/encyclopedia/html/collect/{encyclopedia_id:\S{31}}','article::htmlCollect',"POST"),
        
        array('/tags/intl','articleintl::getTags','GET'),
        array('/tags/intl/{tag_id:\S{30}}','articleintl::getTag','GET'),
        array('/tags/intl','articleintl::addTag','POST'),
        array('/tags/intl/{tag_id:\S{30}}','articleintl::updateTag','PUT'),
        array('/tags/intl/{tag_id:\S{30}}','articleintl::deleteTag','DELETE'),
        
        array('/comprehensive/intl','articleintl::addComprenhensive','POST'),
        array('/comprehensive/intl','articleintl::getComprehensive','GET'),
        array('/character/intl/{type:\S{4}}','articleintl::addCharacter','POST'),
        array('/character/intl/{type:\S{4}}','articleintl::getCharacter','GET'),
        array('/character/intl/{type:\S{4}}','articleintl::addCharacter','POST'),
        array('/character/code/intl/{code:\S{4}}','articleintl::getCharacterbyCode','GET'),
        array('/character/code/intl/{code:\S{4}}','articleintl::delCharacterbyCode','DELETE'),
        array('/encyclopedia/intl/{encyclopedia_id:\S{31}}','articleintl::getEncyclopedia','GET'),
        array('/encyclopedia/intl','articleintl::addEncyclopedia','POST'),
        array('/encyclopedia/intl','articleintl::getEncyclopediaList','GET'),
        array('/encyclopedia/intl/{encyclopedia_id:\S{31}}','articleintl::setEncyclopedia','PUT'),
        array('/encyclopedia/intl/{encyclopedia_id:\S{31}}','articleintl::deleteEncyclopedia','DELETE'),
        array('/encyclopedia/top/intl/{encyclopedia_id:\S{31}}','articleintl::setTop','PUT'),
        
        array('/todo/intl/{doc_id:\S{23}}','articleintl::getTodo','GET'),
        array('/todo/intl','articleintl::addTodo','POST'),
        array('/todo/intl','articleintl::getTodoList','GET'),
        array('/todo/intl/{doc_id:\S{23}}','articleintl::updateTodo','PUT'),        
        array('/todo/intl/{doc_id:(\S{23}(,\S{23})*)}','articleintl::deleteTodo','DELETE'),
        
        
        array('/todolist','complex::todoList','GET'),
        array('/todo/{doc_id:\S{23}}','complex::todo','GET'),
        array('/clock','complex::clock','POST'),
        array('/cplan/clock/{doc_id:\S{23,24}}','complex::clockCplan','POST'),
        array('/cplan/{doc_id:\S{24}}','complex::cplan','GET'),
        
        array('/push/intl/{doc_id:\S{13}}','articleintl::getPush','GET'),
        array('/push/intl','articleintl::addPush','POST'),
        array('/push/intl','articleintl::getPushList','GET'),
        array('/push/intl/{doc_id:\S{13}}','articleintl::updatePush','PUT'),        
        array('/push/intl/{doc_id:(\S{13}(,\S{13})*)}','articleintl::deletePush','DELETE'),        
        
        /**temporarily **/
        array('/bbc_raw/intl','articleintl::deleteRawList','DELETE'),
//        array('/bbc_data/intl','articleintl::deleteDevicedataList','DELETE'), 
    ),
    '/v1/plan' => array(
        array('/tags','plan::getTags','GET'),  
        array('/list','plan::getList','GET'),
        array('/search','plan::search','GET'),
        array('/{plan_id:\S{23}}','plan::add','POST'),
        array('/{plan_id:\S{23}}','plan::cancel','DELETE'),
        array('/take_list','plan::takeList','GET'),
        array('/{plan_id:\S{23}}','plan::getDetail','GET'),
        array('/tips/{plan_id:\S{23}}','plan::getDetailTips','GET'),
        array('/day/{doc_id:\S{22}}','plan::getDay','GET'),
        array('/process/{plan_id:\S{23}}','plan::getProcess','GET'),
        array('/process/{plan_id:\S{23}}','plan::updateProcess','PUT'),
        array('/record/{plan_id:\S{23}}','plan::getRecord','GET'),
        array('/reason/{plan_id:\S{23}}','plan::getReason','GET'),
        
        array('/tags/intl','planintl::addTags','POST'),
        array('/tags/intl/{tag_id:\S{27}}','planintl::updateTags','PUT'),
        array('/tags/intl/{tag_id:\S{27}}','planintl::delete','DELETE'),
        array('/tags/intl/{tag_id:\S{27}}','planintl::getById','GET'),
        array('/tags/intl','planintl::getTags','GET'),
        array('/intl','planintl::addPlan','POST'),
        array('/intl/{plan_id:\S{23}}','planintl::updatePlan','PUT'),   
        array('/top/intl/{plan_id:\S{23}}','planintl::setTop','PUT'),   
        array('/intl/{plan_id:(\S{23}(,\S{23})*)}','planintl::deletePlan','DELETE'),
        array('/intl/{plan_id:\S{23}}','planintl::getPlan','GET'),
        array('/intl','planintl::getPlanList','GET'),
        
        array('/step/intl','planintl::addStep','POST'),
        array('/step/intl/{doc_id:\S{23}}','planintl::getStepById','GET'),
        array('/step/intl/{doc_id:\S{23}}','planintl::updateStep','PUT'),
        array('/step/intl/{doc_id:\S{23}}','planintl::deleteStep','DELETE'),
        
        array('/day/intl','planintl::addDay','POST'),
        array('/day/intl/{doc_id:\S{22}}','planintl::getDayById','GET'),
        array('/day/intl/{doc_id:\S{22}}','planintl::updateDay','PUT'),
        array('/day/intl/{doc_id:\S{22}}','planintl::deleteDay','DELETE'),
        
    ),
    '/v1/message' => array(       
        array('/list','message::getList','GET'),
        array('/{message_id:\S{32,66}}','message::getDetail','GET'),
        array('/{message_id:\S{32,66}}','message::delete','DELETE'),
        array('/list','message::removeList','DELETE'),
        array('/{message_id:\S{32,66}}','message::update','PUT'),
        array('/push_account','message::addPushAccount','POST'),
        array('/push_account/{uid:\d{8,32}}','message::getPushAccount','GET'),
        array('/push_account/{uid:\d{8,32}}','message::deletePushAccount','DELETE'),
        
         //message intl
        array('/list/intl/{uid:\d{8,32}}','messageintl::getMessageList','GET'),
        array('/intl','messageintl::addMessage','POST'),
        array('/push/intl','messageintl::addPush','POST'),
        array('/sender/intl','messageintl::sender','POST'),
        
            //comments
            array('/comments','comment::addComment','POST'),
            array('/comments/{doc_id:\S{13,26}|\S{31}}','comment::getList','GET'),
            array('/html/comments/{doc_id:\S{13,26}|\S{31}}','comment::getHtmlList','GET'),
            array('/html/comments/fav/{doc_id:\S{26}}','comment::htmlFav','POST') 
       
    ),
    
    '/v1/gateway'=>array(
        array('','gateway::addGateway','POST'),
        array('/{bta:\S{12}}','gateway::getOne','GET'),
        array('/{bta:\S{12}}','gateway::reset','DELETE'),
        array('/upload/image','gateway::uploadimage','POST')        
     ),
    
    '/v1/device'=>array(
        array('','device::addDevice','POST'),
        array('','device::getDeviceList','GET'),
        array('/{did:\S{12}}','device::updateDevice','PUT'),
        array('/{did:\S{12}}','device::removeDevice','DELETE'),
        array('/check','device::checkBinding','GET'),
        array('/record/latest/{did:\S{12}}','device::getLatestResult','GET'),
        array('/average/list/{did:\S{12}}','device::getList','GET'),
        array('/record/{did:\S{12}}','device::getRecord','GET'),
        array('/daily/list/{did:\S{12}}','device::getDaily','GET'),
        array('/record/{did:\S{12}}','device::deleteRecord','DELETE')
     ),
    
    '/v1/version'=>array(
        array('','version::getVersion','GET'),
        )
);

