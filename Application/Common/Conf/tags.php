<?php 
return array(     
// 要启用表单令牌功能，需要配置行为绑定，添加下面一行定义即可     
'view_filter' => array('Behavior\TokenBuildBehavior'),    
// 如果是3.2.1版本 需要改成    
// 'view_filter' => array('Behavior\TokenBuildBehavior'),
);