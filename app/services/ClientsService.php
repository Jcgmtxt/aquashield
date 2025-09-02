<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientsService
{
    public function getClients()
    {
        return Client::all();
    }

    public function getClientById(int $id)
    {
        if(!Client::find($id)){
            throw new \Exception('Client not found');
        }
    
        return Client::find($id);
    }

    public function createClient(Request $request){
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'identity_type' => 'required|string|in:CC,CE,NIT,Passport',
            'identity_number' => 'required|string|max:255|unique:clients',
            'phone_number' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
        ]);

        $client = Client::create($validate);

        return $client;
    }

    public function updateClient(Request $request, int $id)
    {
        if(!Client::find($id)){
            throw new \Exception('Client not found');
        }

        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'identity_number' => 'required|string|max:255|unique:clients',
            'phone_number' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
        ]);

        Client::find($id)->update($validate);

        return Client::find($id);
    }

    public function verifyMail(string $mail): bool
    {
        if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

}

