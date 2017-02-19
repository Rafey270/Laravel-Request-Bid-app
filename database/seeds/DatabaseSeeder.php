<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(BidStatusTableSeeder::class);
        $this->call(BidTemplateTableSeeder::class);
        $this->call(FileTypeSeeder::class);
        $this->call(BidTypeTableSeeder::class);
        $this->call(ContactTypeTableSeeder::class);
        $this->call(MailToTypeTableSeeder::class);
        $this->call(PhoneTypeTableSeeder::class);
        $this->call(PropertyTypeTableSeeder::class);
        $this->call(SourceTableSeeder::class);
        $this->call(RequestBidJobSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(RoleUserTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);

        Model::reguard();
    }
}
