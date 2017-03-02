<?php
/**
 * Plume 自动生成
 */
 
namespace Bench\Entity;

use Plume\Entity;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class Name extends Entity
{
	public function up()
	{
		Capsule::Schema()->create('name', function (Blueprint $table) {
			
			/**
			 *  在此处修改Entity
			 */
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
		});
	}
	
	public function down()
	{
		/**
		 *  此处删除Entity
		 */
		Capsule::Schema()->drop('name');
	}
}

?>
