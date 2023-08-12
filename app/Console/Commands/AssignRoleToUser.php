<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignRoleToUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:assign {username} {role_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign role to user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $username = $this->argument('username');
        $role_name = $this->argument('role_name');

        $user = User::where('username', $username)->first();
        if (!$user) {
            $this->error("User with username {$username} not found.");
            return 1; // error code
        }

        $role = Role::findByName($role_name);
        if (!$role) {
            $this->error("Role with name {$role_name} not found.");
            return 1; // error code
        }

        $user->assignRole($role_name);
        $this->info("Role {$role_name} has been assigned to user {$username}.");

        return 0; // success code
    }
}
