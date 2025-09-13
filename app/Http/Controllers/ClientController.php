<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Client;
use App\Services\ClientsService;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;

class ClientController extends Controller
{
    public function __construct(private ClientsService $clientsService){}

    public function index() : Response
    {
        $clients = $this->clientsService->list();
        return Inertia::render('clients/index', [
            'clients' => $clients,
        ]);
    }

    public function create() : Response
    {
        return Inertia::render('clients/create');
    }

    public function store(StoreClientRequest $request)
    {
        $client = $this->clientsService->create($request->validated());

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Cliente creado correctamente');
    }

    public function show(Client $client) : Response
    {
        return Inertia::render('clients/show', [
            'client' => $client,
        ]);
    }

    public function edit(Client $client) : Response
    {
        return Inertia::render('clients/edit', [
            'client' => $client,
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $client = $this->clientsService->update($client, $request->validated());

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Cliente actualizado correctamente');
    }

    public function destroy(Client $client)
    {
        $this->clientsService->delete($client);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente eliminado correctamente');
    }
}
