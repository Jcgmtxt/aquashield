import { Client } from "@/pages/clients/index";

export default function show({ client }: { client: Client }) {
    return (
        <div>
            <h1>Show</h1>
            <p>{client.name}</p>
            <p>{client.identity_number}</p>
            <p>{client.phone_number}</p>
            <p>{client.email}</p>
        </div>
    );
}
