<?php

class m121101_085453_UserPreference extends CDbMigration
{
	public function safeUp()
	{
		$this->createTable('{{userPreferenceType}}', array(
			'userPreferenceTypeID'=>'pk',
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
		$this->createTable('{{userPreference}}', array(
			'userPrefenceID'=>'pk',
			'UserID'=>'id',
			'GroupID'=>'id',
			'UserPreferenceTypeID'=>'id',
			'IntValue'=>'integer',
			'DateValue'=>'datetime',
			'FloatValue'=>'float',
			'StringValue'=>'string',
			'BooleanValue'=>'boolean',
			'CreatedDate'=>'datetime',
			'CreatedBy'=>'guid',
			'ModifiedDate'=>'datetime',
			'ModifiedBy'=>'guid',
			'RowVersion'=>'datetime',
			));

		$this->addForeignKey('FK_{{userPreference}}_UserID', '{{userPreference}}', 'UserID', 
							'{{User}}', 'UserID','NO ACTION', 'NO ACTION');
		$this->addForeignKey('FK_{{userPreference}}_UserPreferenceTypeID', '{{userPreference}}', 'UserPreferenceTypeID', 
							'{{userPreferenceType}}', 'userPreferenceTypeID','NO ACTION', 'NO ACTION');

	}

	public function safeDown()
	{

		$this->dropTable('{{userPreference}}');
		$this->dropTable('{{userPreferenceType}}');
	}

}