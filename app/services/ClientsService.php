<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Requests\Client\StoreClientRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ClientsService
{
    public function list()
    {
        return Client::query()->latest('id')->get();
    }

    public function find(int $id)
    {
        return Client::findOrFail($id);
    }

    public function create(array $data){
        return Client::create($data);
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
        return $client->delete();
    }

    public function verifyMail(string $mail): bool
    {
        if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

    public function store(StoreClientRequest $request): Client
    {
        return Client::create($request->validated());
    }

}

