import { Client } from "@/types/clients";
import AppLayout from "@/layouts/app-layout";
import { BreadcrumbItem } from "@/types";
import clients from "@/routes/clients";

export default function show({ client }: { client: Client }) {

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Clientes',
            href: clients.index().url,
        },
        {
            title: 'Detalle del cliente',
            href: clients.show(client).url,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="container mx-auto py-6">
                <p>{client.name}</p>
                <p>{client.identity_number}</p>
                <p>{client.phone_number}</p>
                <p>{client.email}</p>
            </div>
        </AppLayout>
    );
}
