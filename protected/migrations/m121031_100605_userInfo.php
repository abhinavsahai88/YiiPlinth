<?php

class m121031_100605_userInfo extends CDbMigration
{
	public function safeUp()
	{
		$this->createTable('{{userInfo}}', array(
			'UserInfoID'=>'pk',
			'UserID'=>'id',
			'NotifyUpdates'=>'boolean',
			'Country'=>'string',
			'ProfileImageURI'=>'uri',
			'FirstName'=>'string',
			'LastName'=>'string',
			'Description'=>'short_description',
			'CreatedDate'=>'datetime',
			'CreatedBy'=>'guid',
			'ModifiedDate'=>'datetime',
			'ModifiedBy'=>'guid',
			'RowVersion'=>'datetime',
			'UserURL'=>'code',
			));

		$this->addForeignKey('FK_{{userInfo}}_UserID', '{{userInfo}}', 'UserID',
					'{{User}}', 'UserID', 'NO ACTION', 'NO ACTION');

		//$this->createIndex('UQ_{{UserID}}_UserInfoID', "{{UserInfo}}", 'UserID', true);
	}

	public function down()
	{
		//$this->dropTable('{{userInfo}}');
		$this->dropColumn('{{UserInfo}}','CountryID');
	}

}