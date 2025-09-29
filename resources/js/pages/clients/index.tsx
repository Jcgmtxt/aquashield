import AppLayout from "@/layouts/app-layout";
import { Head } from "@inertiajs/react";
import { BreadcrumbItem } from "@/types";
import clients from "@/routes/clients";
import { ClientsTable } from "@/features/clients/components";
import { IndexProps, Client } from "@/types/clients";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Clientes',
        href: clients.index().url,
    },
]

export default function index({ clients }: IndexProps) {
    const handleEdit = (client: Client) => {
        // Navegar a la página de edición
        window.location.href = `/clients/${client.id}/edit`;
    };

    const handleDelete = (client: Client) => {
        if (confirm(`¿Estás seguro de que quieres eliminar a ${client.name}?`)) {
            // Aquí implementarías la lógica de eliminación
            console.log('Eliminar cliente:', client);
        }
    };

    const handleView = (client: Client) => {
        // Navegar a la página de detalles
        window.location.href = `/clients/${client.id}`;
    };

    const handleCreate = () => {
        // Navegar a la página de creación
        window.location.href = '/clients/create';
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Clientes" />
            <div className="container mx-auto py-6">
                <ClientsTable
                    clients={clients}
                    onEdit={handleEdit}
                    onDelete={handleDelete}
                    onView={handleView}
                    onCreate={handleCreate}
                />
            </div>
        </AppLayout>
    );
}
