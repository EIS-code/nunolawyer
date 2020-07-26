<?php

use Illuminate\Database\Seeder;

class OriginalDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$this->mysql 		 = \DB::connection('mysql');
        $this->mysqlOriginal = \DB::connection('mysql_original');

        // Clients
        $this->runClients();
    }

    public function runClients()
    {
    	$oldDatas = $this->mysqlOriginal->table('nl_users')->get();

    	if (!empty($oldDatas) && !$oldDatas->isEmpty()) {
    		foreach ($oldDatas->chunk(500) as $oldData) {
    			foreach ($oldData as $data) {
    				
    			}
    		}
    	}
    }
}
