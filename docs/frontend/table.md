# Componente de Tabla Reutilizable

Este componente de tabla está diseñado para ser escalable, reutilizable y compatible con el sistema de diseño existente.

## Características

- ✅ **Búsqueda global** con campos personalizables
- ✅ **Ordenamiento** por columnas
- ✅ **Paginación** configurable
- ✅ **Selección de filas** (individual y múltiple)
- ✅ **Acciones por fila** personalizables
- ✅ **Filtros avanzados** opcionales
- ✅ **Responsive** y accesible
- ✅ **TypeScript** con tipado completo
- ✅ **Compatible** con el sistema de diseño existente

## Uso Básico

```tsx
import { Table, TableColumn } from '@/components/ui/table'
import { useTable } from '@/hooks/useTable'

interface User {
  id: number
  name: string
  email: string
  role: string
}

function UsersTable({ users }: { users: User[] }) {
  const [tableState, tableActions] = useTable<User>({
    data: users,
    initialPageSize: 10,
    searchFields: ['name', 'email'],
  })

  const columns: TableColumn<User>[] = [
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
    {
      key: 'role',
      title: 'Rol',
      dataIndex: 'role',
      sortable: true,
    },
  ]

  return (
    <Table
      data={tableState.paginatedData}
      columns={columns}
      searchable={true}
      onSearch={tableActions.setSearchQuery}
      sortable={true}
      onSort={tableActions.setSort}
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
    />
  )
}
```

## Uso Avanzado con Acciones

```tsx
import { Table, TableColumn, TableAction } from '@/components/ui/table'
import { Edit, Trash2, Eye } from 'lucide-react'

function AdvancedTable({ users, onEdit, onDelete, onView }) {
  const [tableState, tableActions] = useTable<User>({
    data: users,
    initialPageSize: 10,
    searchFields: ['name', 'email'],
  })

  const columns: TableColumn<User>[] = [
    {
      key: 'name',
      title: 'Nombre',
      dataIndex: 'name',
      sortable: true,
      render: (value, record) => (
        <div>
          <div className="font-medium">{value}</div>
          <div className="text-sm text-muted-foreground">{record.email}</div>
        </div>
      ),
    },
    {
      key: 'role',
      title: 'Rol',
      dataIndex: 'role',
      sortable: true,
      render: (value) => (
        <span className="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
          {value}
        </span>
      ),
    },
  ]

  const actions: TableAction<User>[] = [
    {
      key: 'view',
      label: 'Ver',
      icon: <Eye className="h-4 w-4" />,
      onClick: (user) => onView(user),
      variant: 'ghost',
    },
    {
      key: 'edit',
      label: 'Editar',
      icon: <Edit className="h-4 w-4" />,
      onClick: (user) => onEdit(user),
      variant: 'ghost',
    },
    {
      key: 'delete',
      label: 'Eliminar',
      icon: <Trash2 className="h-4 w-4" />,
      onClick: (user) => onDelete(user),
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
      onRowClick={(user) => onView(user)}
    />
  )
}
```

## Uso con Filtros Avanzados

```tsx
import { TableFilters, FilterOption } from '@/components/ui/table-filters'

function FilteredTable({ users }) {
  const [tableState, tableActions] = useTable<User>({
    data: users,
    initialPageSize: 10,
    searchFields: ['name', 'email'],
  })

  const filterOptions: FilterOption[] = [
    {
      key: 'role',
      label: 'Rol',
      type: 'select',
      options: [
        { value: 'admin', label: 'Administrador' },
        { value: 'user', label: 'Usuario' },
        { value: 'moderator', label: 'Moderador' },
      ],
    },
    {
      key: 'created_at',
      label: 'Fecha de creación',
      type: 'date',
    },
    {
      key: 'age',
      label: 'Edad',
      type: 'number',
      placeholder: 'Edad mínima',
    },
  ]

  return (
    <div className="space-y-4">
      <TableFilters
        filters={filterOptions}
        values={tableState.filters}
        onChange={tableActions.setFilter}
        onClear={tableActions.clearFilters}
      />
      
      <Table
        data={tableState.paginatedData}
        columns={columns}
        // ... otras props
      />
    </div>
  )
}
```

## Hook useTable

El hook `useTable` maneja todo el estado de la tabla:

### Estado disponible:
- `currentPage`: Página actual
- `pageSize`: Tamaño de página
- `totalPages`: Total de páginas
- `sortColumn`: Columna ordenada
- `sortDirection`: Dirección del ordenamiento
- `searchQuery`: Consulta de búsqueda
- `filters`: Filtros aplicados
- `selectedRows`: Filas seleccionadas
- `filteredData`: Datos filtrados
- `paginatedData`: Datos paginados

### Acciones disponibles:
- `setCurrentPage(page)`: Cambiar página
- `setPageSize(size)`: Cambiar tamaño de página
- `setSort(column, direction)`: Establecer ordenamiento
- `toggleSort(column)`: Alternar ordenamiento
- `setSearchQuery(query)`: Establecer búsqueda
- `setFilter(key, value)`: Establecer filtro
- `clearFilters()`: Limpiar filtros
- `setSelectedRows(rows)`: Establecer filas seleccionadas
- `toggleRowSelection(row)`: Alternar selección de fila
- `selectAll()`: Seleccionar todo
- `clearSelection()`: Limpiar selección

## Integración con Backend

Para integrar con un backend, puedes usar los callbacks del hook:

```tsx
const [tableState, tableActions] = useTable<User>({
  data: users,
  initialPageSize: 10,
  searchFields: ['name', 'email'],
})

// Sincronizar con el backend
useEffect(() => {
  fetchUsers({
    page: tableState.currentPage,
    pageSize: tableState.pageSize,
    search: tableState.searchQuery,
    sort: tableState.sortColumn,
    direction: tableState.sortDirection,
    filters: tableState.filters,
  })
}, [tableState.currentPage, tableState.pageSize, tableState.searchQuery, tableState.sortColumn, tableState.sortDirection, tableState.filters])
```


