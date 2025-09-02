<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Client;
use App\Services\ClientsService;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientsService = new ClientsService();
        $clients = $clientsService->getClients();
        return Inertia::render('clients/index', [
            'clients' => $clients,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('clients/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $clientsService = new ClientsService();
        $client = $clientsService->createClient($request);
        return to_route('clients.index', [
            'client' => $client,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $clientsService = new ClientsService();
        $client = $clientsService->getClientById($id);
        return Inertia::render('clients/show', [
            'client' => $client,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
