import * as React from "react"
import { ChevronUp, Search, MoreHorizontal } from "lucide-react"
import { cn } from "@/lib/utils"
import { Button } from "./button"
import { Input } from "./input"
import { Card, CardContent, CardHeader } from "./card"

// Tipos para el componente de tabla
export interface TableColumn<T = any> {
  key: string
  title: string
  dataIndex?: keyof T
  render?: (value: any, record: T, index: number) => React.ReactNode
  sortable?: boolean
  filterable?: boolean
  width?: string | number
  align?: 'left' | 'center' | 'right'
  className?: string
}

export interface TableButton {
  label: string
  icon?: React.ReactNode
  onClick: () => void // Para botones del header
  variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link'
  size?: 'default' | 'sm' | 'lg' | 'icon'
  className?: string
  disabled?: boolean
}

export interface TableRowButton<T = any> {
  key: string
  label: string
  icon?: React.ReactNode
  onClick: (record: T) => void // Para botones de fila
  variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link'
  className?: string
}

export interface TableProps<T = any> {
  data: T[]
  columns: TableColumn<T>[]
  actions?: TableRowButton<T>[]
  loading?: boolean
  searchable?: boolean
  searchPlaceholder?: string
  onSearch?: (query: string) => void
  sortable?: boolean
  onSort?: (column: string, direction: 'asc' | 'desc') => void
  selectable?: boolean
  selectedRows?: T[]
  onSelectionChange?: (selectedRows: T[]) => void
  buttons?: TableButton[]
  pagination?: {
    current: number
    pageSize: number
    total: number
    onChange: (page: number, pageSize: number) => void
  }
  emptyText?: string
  className?: string
  rowClassName?: (record: T, index: number) => string
  onRowClick?: (record: T, index: number) => void
}

// Componente principal de tabla
function Table<T = any>({
  data,
  columns,
  actions = [],
  loading = false,
  searchable = true,
  searchPlaceholder = "Buscar...",
  onSearch,
  sortable = true,
  onSort,
  selectable = false,
  selectedRows = [],
  onSelectionChange,
  buttons = [],
  pagination,
  emptyText = "No hay datos disponibles",
  className,
  rowClassName,
  onRowClick,
}: TableProps<T>) {
  const [searchQuery, setSearchQuery] = React.useState("")
  const [sortColumn, setSortColumn] = React.useState<string | null>(null)
  const [sortDirection, setSortDirection] = React.useState<'asc' | 'desc'>('asc')

  // Manejo de búsqueda
  const handleSearch = (query: string) => {
    setSearchQuery(query)
    onSearch?.(query)
  }

  // Manejo de ordenamiento
  const handleSort = (columnKey: string) => {
    if (!sortable) return

    const newDirection = sortColumn === columnKey && sortDirection === 'asc' ? 'desc' : 'asc'
    setSortColumn(columnKey)
    setSortDirection(newDirection)
    onSort?.(columnKey, newDirection)
  }

  // Manejo de selección
  const handleSelectAll = () => {
    if (!selectable || !onSelectionChange) return

    if (selectedRows.length === data.length) {
      onSelectionChange([])
    } else {
      onSelectionChange([...data])
    }
  }

  const handleSelectRow = (record: T) => {
    if (!selectable || !onSelectionChange) return

    const isSelected = selectedRows.some(row => (row as any).id === (record as any).id)
    if (isSelected) {
      onSelectionChange(selectedRows.filter(row => (row as any).id !== (record as any).id))
    } else {
      onSelectionChange([...selectedRows, record])
    }
  }

  const isRowSelected = (record: T) => {
    return selectedRows.some(row => (row as any).id === (record as any).id)
  }

  const allButtons = React.useMemo(() => {
    const buttonList = [...buttons]
    return buttonList
  }, [buttons])

  return (
    <Card className={cn("w-full", className)}>
      {/* Header con búsqueda */}
      {(searchable || allButtons.length > 0) && (
        <CardHeader className="pb-4">
          <div className="flex items-center justify-between gap-4">
            {searchable && (
              <div className="relative flex-1 max-w-sm">
                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                  placeholder={searchPlaceholder}
                  value={searchQuery}
                  onChange={(e) => handleSearch(e.target.value)}
                  className="pl-9"
                />
              </div>
            )}
            {/* Botones */}
            {allButtons.length > 0 && (
              <div className="flex items-center gap-2">
                {allButtons.map((btn, index) => (
                  <Button
                    key={index}
                    variant={btn.variant || 'outline'}
                    size={btn.size || 'sm'}
                    onClick={btn.onClick}
                    disabled={btn.disabled}
                    className={btn.className}
                  >
                    {btn.icon && <span className="mr-2">{btn.icon}</span>}
                    {btn.label}
                  </Button>
                ))}
              </div>
            )}
          </div>
        </CardHeader>
      )}

      <CardContent className="p-0">
        {/* Tabla */}
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b border-border">
                {selectable && (
                  <th className="w-12 px-4 py-3 text-left">
                    <input
                      type="checkbox"
                      checked={selectedRows.length === data.length && data.length > 0}
                      onChange={handleSelectAll}
                      className="rounded border-input"
                    />
                  </th>
                )}
                {columns.map((column) => (
                  <th
                    key={column.key}
                    className={cn(
                      "px-4 py-3 text-sm font-medium text-muted-foreground",
                      column.align === 'center' && 'text-center',
                      column.align === 'right' && 'text-right',
                      sortable && column.sortable !== false && 'cursor-pointer hover:text-foreground',
                      column.className
                    )}
                    style={{ width: column.width }}
                    onClick={() => column.sortable !== false && handleSort(column.key)}
                  >
                    <div className="flex items-center gap-2">
                      <span>{column.title}</span>
                      {sortable && column.sortable !== false && (
                        <div className="flex flex-col">
                          <ChevronUp
                            className={cn(
                              "h-3 w-3",
                              sortColumn === column.key && sortDirection === 'asc'
                                ? 'text-foreground'
                                : 'text-muted-foreground'
                            )}
                          />                          
                        </div>
                      )}
                    </div>
                  </th>
                ))}
                {actions.length > 0 && (
                  <th className="w-12 px-4 py-3 text-right">
                    <span className="sr-only">Acciones</span>
                  </th>
                )}
              </tr>
            </thead>
            <tbody>
              {loading ? (
                <tr>
                  <td
                    colSpan={columns.length + (selectable ? 1 : 0) + (actions.length > 0 ? 1 : 0)}
                    className="px-4 py-8 text-center text-muted-foreground"
                  >
                    Cargando...
                  </td>
                </tr>
              ) : data.length === 0 ? (
                <tr>
                  <td
                    colSpan={columns.length + (selectable ? 1 : 0) + (actions.length > 0 ? 1 : 0)}
                    className="px-4 py-8 text-center text-muted-foreground"
                  >
                    {emptyText}
                  </td>
                </tr>
              ) : (
                data.map((record, index) => (
                  <tr
                    key={(record as any).id || index}
                    className={cn(
                      "border-b border-border hover:bg-muted/50 transition-colors",
                      onRowClick && "cursor-pointer",
                      rowClassName?.(record, index)
                    )}
                    onClick={() => onRowClick?.(record, index)}
                  >
                    {selectable && (
                      <td className="px-4 py-3">
                        <input
                          type="checkbox"
                          checked={isRowSelected(record)}
                          onChange={() => handleSelectRow(record)}
                          className="rounded border-input"
                        />
                      </td>
                    )}
                    {columns.map((column) => (
                      <td
                        key={column.key}
                        className={cn(
                          "px-4 py-3 text-sm",
                          column.align === 'center' && 'text-center',
                          column.align === 'right' && 'text-right',
                          column.className
                        )}
                      >
                        {column.render
                          ? column.render(
                              column.dataIndex ? (record as any)[column.dataIndex] : record,
                              record,
                              index
                            )
                          : column.dataIndex
                            ? (record as any)[column.dataIndex]
                            : ''
                        }
                      </td>
                    ))}
                    {actions.length > 0 && (
                      <td className="px-4 py-3 text-right">
                        <div className="flex items-center justify-end gap-1">
                          {actions.map((action) => (
                            <Button
                              key={action.key}
                              variant={action.variant || 'ghost'}
                              size="sm"
                              onClick={(e) => {
                                e.stopPropagation()
                                action.onClick(record)
                              }}
                              className={cn("h-8 w-8 p-0", action.className)}
                            >
                              {action.icon || <MoreHorizontal className="h-4 w-4" />}
                            </Button>
                          ))}
                        </div>
                      </td>
                    )}
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>

        {/* Paginación */}
        {pagination && (
          <div className="flex items-center justify-between px-4 py-3 border-t border-border">
            <div className="text-sm text-muted-foreground">
              Mostrando {((pagination.current - 1) * pagination.pageSize) + 1} a{' '}
              {Math.min(pagination.current * pagination.pageSize, pagination.total)} de{' '}
              {pagination.total} resultados
            </div>
            <div className="flex items-center gap-2">
              <Button
                variant="outline"
                size="sm"
                onClick={() => pagination.onChange(pagination.current - 1, pagination.pageSize)}
                disabled={pagination.current <= 1}
              >
                Anterior
              </Button>
              <span className="text-sm">
                Página {pagination.current} de {Math.ceil(pagination.total / pagination.pageSize)}
              </span>
              <Button
                variant="outline"
                size="sm"
                onClick={() => pagination.onChange(pagination.current + 1, pagination.pageSize)}
                disabled={pagination.current >= Math.ceil(pagination.total / pagination.pageSize)}
              >
                Siguiente
              </Button>
            </div>
          </div>
        )}
      </CardContent>
    </Card>
  )
}

export { Table }
