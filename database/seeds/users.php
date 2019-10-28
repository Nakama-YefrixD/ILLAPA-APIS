<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\User;

class users extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'persona_id' => 1,
            'email' => 'raulpozogonzalez@gmail.com',
            'estado' => 1,
            'email_verified_at' => '2019-09-11',
            'password' => Hash::make('raul123'),
            'api_token' => Str::random(60),

        ]);

        // User::create([
        //     'persona_id' => 2,
        //     'email' => 'heber@hotmail.com',
        //     'estado' => 1,
        //     'email_verified_at' => NUll,
        //     'password' => Hash::make('heber'),
        //     'api_token' => Str::random(60),

        // ]);

        // User::create([
        //     'persona_id' => 3,
        //     'email' => 'peronaC@hotmail.com',
        //     'estado' => 1,
        //     'email_verified_at' => NUll,
        //     'password' => Hash::make('persona'),
        //     'api_token' => Str::random(60),

        // ]);
        // User::create([
        //     'persona_id' => 4,
        //     'email' => 'peronaD@hotmail.com',
        //     'estado' => 1,
        //     'email_verified_at' => NUll,
        //     'password' => Hash::make('persona'),
        //     'api_token' => Str::random(60),

        // ]);

        // User::create([
        //     'persona_id' => 5,
        //     'email' => 'peronaE@hotmail.com',
        //     'estado' => 1,
        //     'email_verified_at' => NUll,
        //     'password' => Hash::make('persona'),
        //     'api_token' => Str::random(60),

        // ]);
        // User::create([
        //     'persona_id' => 6,
        //     'email' => 'peronaF@hotmail.com',
        //     'estado' => 1,
        //     'email_verified_at' => NUll,
        //     'password' => Hash::make('persona'),
        //     'api_token' => Str::random(60),

        // ]);
        // User::create([
        //     'persona_id' => 7,
        //     'email' => 'peronaG@hotmail.com',
        //     'estado' => 1,
        //     'email_verified_at' => NUll,
        //     'password' => Hash::make('persona'),
        //     'api_token' => Str::random(60),

        // ]);
        // User::create([
        //     'persona_id' => 8,
        //     'email' => 'Cliente@hotmail.com',
        //     'estado' => 1,
        //     'email_verified_at' => NUll,
        //     'password' => Hash::make('persona'),
        //     'api_token' => Str::random(60),

        // ]);

    }
}
