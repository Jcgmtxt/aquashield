import { Plus, Edit } from 'lucide-react'
import { Table, TableColumn, TableRowButton } from '@/components/ui/table'
import { useTable } from '@/hooks/useTable'
import { Client } from '@/types/clients'

interface ClientsTableProps {
	clients: Client[]
	onEdit?: (client: Client) => void
	onView?: (client: Client) => void
	onCreate?: () => void
}

export function ClientsTable({
	clients,
	onEdit,
	onView,
	onCreate
}: ClientsTableProps) {
	const [tableState, tableActions] = useTable<Client>({
		data: clients,
		initialPageSize: 10,
		searchFields: ['name', 'email', 'phone_number', 'identity_number'],
	})

	const handleCreate = () => {
		onCreate?.()
	}

	// Columnas
	const columns: TableColumn<Client>[] = [
		{ key: 'name', title: 'Nombre', dataIndex: 'name', sortable: true },
		{ key: 'email', title: 'Email', dataIndex: 'email', sortable: true },
		{ key: 'phone_number', title: 'Teléfono', dataIndex: 'phone_number', sortable: true },
		{ key: 'identity_number', title: 'Número de Identificación', dataIndex: 'identity_number', sortable: true },
	]

	// Acciones por fila
	const actions: TableRowButton<Client>[] = [
		{
			key: 'edit',
			label: 'Editar',
			icon: <Edit className="h-4 w-4" />,
			onClick: (client) => onEdit?.(client),
			variant: 'ghost',
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
			buttons={[
				{
					label: 'Crear Cliente',
					onClick: handleCreate,
					variant: 'default',
					icon: <Plus className="h-4 w-4" />,
				},
			]}
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