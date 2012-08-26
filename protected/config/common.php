<?php
/**
* This is the default configuration for a YiiPlinth application.
*/
return array(
    'charset'=>'utf-8',
    'sourceLanguage'=>'00',     // Force translation lookups
    'language'=>'en',           // Default language is English

    // autoloading model and component classes
    'import'=>array(
        'YIIPlinth.models.*',
        'YIIPlinth.components.*',
        'YIIPlinth.controllers.*',
        'YIIPlinth.extensions.Session.*',
    ),

    'modules'=>array(
        // Note gii should not be available in the runtime environment
        'UserManagement'=>array(
            'class'=>'YIIPlinth.modules.UserManagement.UserManagementModule',
            'modules'=>array(
                'OAuth'=>array(
                    'modules'=>array(
                        'Twitter',
                        'Facebook',
                        ),
                    ),
                ),
            ),
        'LessCSS'=>array(
            'class'=>'YIIPlinth.modules.LessCSS.LessCSSModule',
            ),
        'Messaging',
        'MailChimp',
    ),

    // application components
    'components'=>array(
        'db'=>array(
            'connectionString'=>'mysql:host=127.0.0.1;dbname=YiiPlinth',
            'emulatePrepare'=>true,
            'charset'=>'utf8',
            'driverMap' => array(
                'mysql'=> 'YIIPlinth.components.db.mysql.PlinthMySQLSchema',
                ),
            'tablePrefix'=> '',
            ),
        'urlManager'=>array(
            'urlFormat'=>'path',
            'showScriptName'=>false,
            'rules'=>array(
                // TODO: Make 'WS' configurable
                // Web Service Interface
                array('WS/list', 'pattern'=>'WS/<model:\w+>', 'verb'=>'GET'),
                array('WS/view', 'pattern'=>'WS/<model:\w+>/<id:\d+>', 'verb'=>'GET'),
                array('WS/view', 'pattern'=>'WS/<model:\w+>/<guid:\w+>', 'verb'=>'GET'),
                array('WS/update', 'pattern'=>'WS/<model:\w+>/<id:\d+>', 'verb'=>'PUT'),
                array('WS/update', 'pattern'=>'WS/<model:\w+>/<guid:\w+>', 'verb'=>'PUT'),
                array('WS/create', 'pattern'=>'WS/<model:\w+>/', 'verb'=>'POST'),
                array('WS/delete', 'pattern'=>'WS/<model:\w+>/<id:\d+>', 'verb'=>'DELETE'),
                array('WS/delete', 'pattern'=>'WS/<model:\w+>/<guid:\w+>', 'verb'=>'DELETE'),

                // Default action
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                'caseSensitive'=>false,
                ),
            ),
    ),
);