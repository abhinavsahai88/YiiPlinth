<?php

class m121101_094701_userContentRating extends CDbMigration
{
	public function safeUp()
	{
		$this->createTable('{{userContentRating}}', array(
			'userContentRatingID'=>'pk',
			'defaultContentRatingID'=>'id',
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
	}

	public function safeDown()
	{
		$this->dropTable('{{userContentRating}}');
	}

}