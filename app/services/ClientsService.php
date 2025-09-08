<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClientsService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Client::query()->latest('id')->paginate($perPage);
    }

    public function find(int $id)
    {
        return Client::findOrFail($id);
    }

    public function create(array $data){
        return DB::transaction(function () use ($data) {
            return Client::create($data);
        });
    }

    public function update(Client $client, array $data): Client
    {
        return DB::transaction(function () use ($client, $data) {
            $client->update($data);
            return $client->refresh();
        });
    }

    public function delete(Client $client)
    {
        return DB::transaction(function () use ($client) {
            $client->delete();
        });
    }

    public function verifyMail(string $mail): bool
    {
        if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

}

