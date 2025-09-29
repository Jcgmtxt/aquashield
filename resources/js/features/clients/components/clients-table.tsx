import React from 'react'
import { Edit, Trash2, Eye, Plus } from 'lucide-react'
import { Table, TableColumn, TableAction } from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { useTable } from '@/hooks/useTable'
import { Client } from '@/types/clients'

interface ClientsTableProps {
    clients: Client[]
    onEdit?: (client: Client) => void
    onDelete?: (client: Client) => void
    onView?: (client: Client) => void
    onCreate?: () => void
}

export function ClientsTable({
    clients,
    onEdit,
    onDelete,
    onView,
    onCreate
}: ClientsTableProps) {
    const [tableState, tableActions] = useTable<Client>({
        data: clients,
        initialPageSize: 10,
        searchFields: ['name', 'email', 'phone_number', 'identity_number'],
})

  // Definir columnas de la tabla
    const columns: TableColumn<Client>[] = [
    {
        key: 'name',
        title: 'Nombre',
        dataIndex: 'name',
        sortable: true,
        filterable: true,
    },
    {
        key: 'identity_type',
        title: 'T.D',
        dataIndex: 'identity_type',
        sortable: true,
        filterable: true,
        render: (value) => (
            <span className="capitalize">{value}</span>
      ),
    },
    {
        key: 'identity_number',
        title: 'Número Documento',
        dataIndex: 'identity_number',
        sortable: true,
        filterable: true,
    },
    {
        key: 'phone_number',
        title: 'Teléfono',
        dataIndex: 'phone_number',
        sortable: true,
        filterable: true,
    },
    {
        key: 'email',
        title: 'Email',
        dataIndex: 'email',
        sortable: true,
        filterable: true,
      render: (value) => (
        <a
          href={`mailto:${value}`}
          className="text-blue-600 hover:text-blue-800 hover:underline"
        >
          {value}
        </a>
      ),
    },
  ]

  // Definir acciones de la tabla
  const actions: TableAction<Client>[] = [
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

  // Acción crear (para integraciones futuras desde un toolbar externo)
  const handleCreate = () => onCreate?.()

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h2 className="text-2xl font-bold">Clientes</h2>
        <div className="text-sm text-muted-foreground">
          {tableState.filteredData.length} de {clients.length} clientes
        </div>
      </div>
      {onCreate && (
        <div className="flex justify-end">
          <Button onClick={handleCreate} size="sm">
            <Plus className="h-4 w-4 mr-2" />
            Crear Cliente
          </Button>
        </div>
      )}

      <Table
        data={tableState.paginatedData}
        columns={columns}
        actions={actions}
        searchable={true}
        searchPlaceholder="Buscar clientes..."
        onSearch={tableActions.setSearchQuery}
        sortable={true}
        onSort={tableActions.setSort}
        selectable={true}
        selectedRows={tableState.selectedRows}
        onSelectionChange={tableActions.setSelectedRows}
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
        rowClassName={(record, index) =>
          index % 2 === 0 ? 'bg-background' : 'bg-muted/20'
        }
        onRowClick={(record) => onView?.(record)}
      />

      {/* Información adicional */}
      {tableState.selectedRows.length > 0 && (
        <div className="flex items-center justify-between rounded-lg border bg-muted/50 p-4">
          <span className="text-sm text-muted-foreground">
            {tableState.selectedRows.length} cliente(s) seleccionado(s)
          </span>
          <div className="flex gap-2">
            <Button
              variant="outline"
              size="sm"
              onClick={tableActions.clearSelection}
            >
              Limpiar selección
            </Button>
            <Button
              variant="destructive"
              size="sm"
              onClick={() => {
                // Aquí podrías implementar una acción masiva
                console.log('Eliminar seleccionados:', tableState.selectedRows)
              }}
            >
              Eliminar seleccionados
            </Button>
          </div>
        </div>
      )}
    </div>
  )
}


