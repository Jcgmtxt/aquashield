<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class ClientsService extends ServiceProvider
{
    public function getClients()
    {
        return Client::all();
    }

    public function createClient(Request $request){
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'identity_number' => 'required|string|max:255|unique:clients',
            'phone_number' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
        ]);

        $client = Client::create($validate);

        return $client;
    }

    public function verifyMail(string $mail): bool
    {
        if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

}

