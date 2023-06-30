<?php

use App\Models\Master\Employee;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $total_user = $this->command->ask(
            'How many employees you want to create?',
            10
        );
        $user_password = $this->command->ask('Please enter user password', '123456');

        for ($i = 1; $i <= $total_user; $i++) {
            $this->command->info('Creating user ' . $i . ' of ' . $total_user);

            $username = 'user' . sprintf('%02d', $i);
            $display_name = 'user' . sprintf('%02d', $i);

            if (User::where('username', $username)->exists()) {
                $this->command->warn('User \'' . $username . '\' already exists');
                continue;
            }

            $user = User::create([
                'name' => $display_name,
                'username' => $username,
                'password' => Hash::make($user_password),
                'email' => $username . '@user.local'
            ]);

            $this->command->info('User \'' . $display_name . '\' created');

            Employee::create([
                'user_id' => $user->id,
                'company_code' => '1000',
                'code' => '900' + sprintf('%02d', $i),
                'email' => $user->email,
                'name' => $display_name,
                'phone_number' => '190000' . sprintf('%02d', $i),
                'address' => 'Address of ' . $display_name,
                'is_active' => true,
            ]);
        }
    }
}
