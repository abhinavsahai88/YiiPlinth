<?php

class m121101_094703_userInfo extends CDbMigration
{
	public function safeUp()
	{
		$this->addColumn('{{UserInfo}}','defaultContentRatingID','id');
		$this->addForeignKey('FK_{{UserInfo}}_defaultContentRatingID', '{{UserInfo}}', 'defaultContentRatingID', 
							'{{UserContentRating}}', 'userContentRatingID','NO ACTION', 'NO ACTION');
	}

	public function safeDown()
	{
		$this->dropColumn('{{userInfo}}','defaultContentRating');
	}

}