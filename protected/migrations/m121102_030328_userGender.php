<?php

class m121102_030328_userGender extends CDbMigration
{
	public function safeUp()
	{
		$this->createTable('{{userGender}}', array(
			'GenderID'=>'code_null',
			'Description'=>'short_description_null',
			'GUID'=>'guid',
			'Sequence'=>'short_description_null',
			'ImageURI'=>'uri_null',
			'CreatedDate'=>'datetime',
			'CreatedBy'=>'guid',
			'ModifiedDate'=>'datetime',
			'ModifiedBy'=>'guid',
			'RowVersion'=>'datetime',
			));

		$this->addForeignKey('FK_{{userInfo}}_GenderID', '{{userInfo}}', 'GenderID', 
							'{{userGender}}', 'GenderID','NO ACTION', 'NO ACTION');
	
	}

	public function safeDown()
	{
		$this->dropTable('{{userGender}}');
	}
}