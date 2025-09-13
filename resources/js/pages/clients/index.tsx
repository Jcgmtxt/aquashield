import { Head, Link } from "@inertiajs/react";

export interface Client {
    id: number;
    name: string;
    identity_type: string;
    identity_number: string;
    phone_number: string;
    email: string;
}

interface IndexProps {
    clients: Client[];
}

export default function index({ clients }: IndexProps) {
    return (
        <>
            <Head title="Todos los Clientes" />
            <div className="container mx-auto py-6">
                <div className="flex justify-between items-center mb-6">
                    <h1 className="text-2xl font-bold">Todos los Clientes</h1>
                    <Link 
                        href="/clients/create" 
                        className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Crear Cliente
                    </Link>
                </div>
                <div className="grid gap-4">
                    {clients && clients.length > 0 ? (
                        clients.map((client: Client) => (
                            <div key={client.id} className="border rounded-lg p-4 shadow-sm">
                                <h3 className="font-semibold">{client.name}</h3>
                                <p>Tipo documento: {client.identity_type}</p>
                                <p>Número documento: {client.identity_number}</p>
                                <p>Teléfono: {client.phone_number}</p>
                                <p>Email: {client.email}</p>
                                <div className="mt-2">
                                    <Link 
                                        href={`/clients/${client.id}`}
                                        className="text-blue-600 hover:text-blue-800 mr-4"
                                    >
                                        Ver
                                    </Link>
                                    <Link 
                                        href={`/clients/${client.id}/edit`}
                                        className="text-green-600 hover:text-green-800"
                                    >
                                        Editar
                                    </Link>
                                </div>
                            </div>
                        ))
                    ) : (
                        <p className="text-gray-500">No hay clientes registrados.</p>
                    )}
                </div>
            </div>
        </>
    );
}