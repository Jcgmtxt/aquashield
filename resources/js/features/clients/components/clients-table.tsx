import { Edit, Trash2, Eye } from 'lucide-react'
import { Table, TableColumn, TableRowButton } from '@/components/ui/table'
import { useTable } from '@/hooks/useTable'
import { Client } from '@/types/clients'

interface ClientsTableProps {
    clients: Client[]
    onEdit?: (client: Client) => void
    onDelete?: (client: Client) => void
    onView?: (client: Client) => void
}

export function ClientsTable({
    clients,
    onEdit,
    onDelete,
    onView
}: ClientsTableProps) {
    const [tableState, tableActions] = useTable<Client>({
        data: clients,
        initialPageSize: 10,
        searchFields: ['name', 'email', 'phone_number', 'identity_number'],
    })

    // Definir columnas básicas
    const columns: TableColumn<Client>[] = [
        {
            key: 'name',
            title: 'Nombre',
            dataIndex: 'name',
            sortable: true,
        },
        {
            key: 'email',
            title: 'Email',
            dataIndex: 'email',
            sortable: true,
        },
    ]

    const actions: TableRowButton<Client>[] = [
        {
            key: 'view',
            label: 'Ver',
            icon: <Eye className="h-4 w-4" />,
            onClick: (client) => onView?.(client),
            variant: 'ghost',
        },
        {
            key: 'edit',
            label: 'Editar',
            icon: <Edit className="h-4 w-4" />,
            onClick: (client) => onEdit?.(client),
            variant: 'ghost',
        },
        {
            key: 'delete',
            label: 'Eliminar',
            icon: <Trash2 className="h-4 w-4" />,
            onClick: (client) => onDelete?.(client),
            variant: 'ghost',
            className: 'text-red-600 hover:text-red-800',
        },
    ]

    return (
        <Table
            data={tableState.paginatedData}
            columns={columns}
            actions={actions}
            searchable={true}
            searchPlaceholder="Buscar clientes..."
            onSearch={tableActions.setSearchQuery}
            sortable={true}
            onSort={tableActions.setSort}
            selectable={false}
            pagination={{
                current: tableState.currentPage,
                pageSize: tableState.pageSize,
                total: tableState.filteredData.length,
                onChange: (page, pageSize) => {
                    tableActions.setCurrentPage(page)
                    if (pageSize !== tableState.pageSize) {
                        tableActions.setPageSize(pageSize)
                    }
                },
            }}
            emptyText="No se encontraron clientes"
            className="w-full"
            onRowClick={(record) => onView?.(record)}
        />
    )
}
