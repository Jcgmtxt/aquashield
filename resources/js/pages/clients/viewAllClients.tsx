import { Link } from "@inertiajs/react";

export default function ViewAllClients({ clients }: { clients: any }) {
    return (
        <div>
            <h1>View All Clients</h1>
            <Link href="/clients/create">Create Client</Link>
            <ul>
                {clients.map((client: any) => (
                    <li key={client.id}>{client.name} {client.identity_number} {client.phone_number} {client.email}</li>
                ))}
            </ul>
        </div>
    );
}