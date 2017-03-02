<?php
/**
 * Plume 自动生成
 */
 
namespace Ace\Entity;

use Plume\Entity;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class User extends Entity
{
	public function up()
	{
		Capsule::Schema()->create('user', function (Blueprint $table) {
			
			/**
			 *  在此处修改Entity
			 */
        	$table->increments('id');
			$table->string('username');
			$table->string('password');
			$table->unique("username");
        	$table->timestamps();
		});
	}
	
	public function down()
	{
		/**
		 *  此处删除Entity
		 */
		Capsule::Schema()->drop('user');
	}
}

?>
