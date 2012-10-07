<?php

class m121004_101804_Country extends CDbMigration
{
	public function safeUp()
	{

		$this->createTable('{{Country}}', array(
			'CountryID'=>'pk',
			'ISOCode'=>'code',
			'Name'=>'title',
			'CreatedDate'=>'datetime',
			'CreatedBy'=>'guid',
			'ModifiedDate'=>'datetime',
			'ModifiedBy'=>'guid',
			'Rowversion'=>'datetime',
			));
		$this->createIndex('UQ_{{Country}}_ISOCode', "{{Country}}", "ISOCode", true);

		$laCountries = $this->getCountries();
		$loCountry = new Country;
		foreach ($laCountries as $lcCode => $lcCountry)
		{
			$loCountry->unsetAttributes();
			$loCountry->setIsNewRecord(true);
			$loCountry->setAttributes(array(
				'ISOCode'=>$taCountry[0],
				'Name'=>$taCountry[1],
			));
			$loCountry->save();
		}
	}

	public function safeDown()
	{
		$this->dropTable('{{Country}}');
	}

	private function getCountries()
	{
		return array(
			'test'=>'test country',
			);
	}
}