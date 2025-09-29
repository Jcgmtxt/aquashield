import { useState, useMemo, useCallback } from 'react'

export interface UseTableOptions<T> {
  data: T[]
  initialPageSize?: number
  initialSortColumn?: string
  initialSortDirection?: 'asc' | 'desc'
  searchFields?: (keyof T)[]
  filterFn?: (data: T[], filters: Record<string, any>) => T[]
}

export interface TableState<T> {
  // Paginación
  currentPage: number
  pageSize: number
  totalPages: number

  // Ordenamiento
  sortColumn: string | null
  sortDirection: 'asc' | 'desc'

  // Búsqueda
  searchQuery: string

  // Filtros
  filters: Record<string, any>

  // Selección
  selectedRows: T[]

  // Datos procesados
  filteredData: T[]
  paginatedData: T[]
}

export interface TableActions<T> {
  // Paginación
  setCurrentPage: (page: number) => void
  setPageSize: (size: number) => void

  // Ordenamiento
  setSort: (column: string, direction: 'asc' | 'desc') => void
  toggleSort: (column: string) => void

  // Búsqueda
  setSearchQuery: (query: string) => void

  // Filtros
  setFilter: (key: string, value: any) => void
  clearFilters: () => void

  // Selección
  setSelectedRows: (rows: T[]) => void
  toggleRowSelection: (row: T) => void
  selectAll: () => void
  clearSelection: () => void
}

export function useTable<T extends Record<string, any>>({
  data,
  initialPageSize = 10,
  initialSortColumn,
  initialSortDirection = 'asc',
  searchFields,
  filterFn,
}: UseTableOptions<T>): [TableState<T>, TableActions<T>] {

  // Estado
  const [currentPage, setCurrentPage] = useState(1)
  const [pageSize, setPageSize] = useState(initialPageSize)
  const [sortColumn, setSortColumn] = useState<string | null>(initialSortColumn || null)
  const [sortDirection, setSortDirection] = useState<'asc' | 'desc'>(initialSortDirection)
  const [searchQuery, setSearchQuery] = useState('')
  const [filters, setFilters] = useState<Record<string, any>>({})
  const [selectedRows, setSelectedRows] = useState<T[]>([])

  // Función de búsqueda
  const searchData = useCallback((data: T[], query: string): T[] => {
    if (!query.trim()) return data

    const lowercaseQuery = query.toLowerCase()

    return data.filter(item => {
      if (searchFields) {
        return searchFields.some(field => {
          const value = item[field]
          return value && value.toString().toLowerCase().includes(lowercaseQuery)
        })
      }

      // Búsqueda en todos los campos si no se especifican campos
      return Object.values(item).some(value =>
        value && value.toString().toLowerCase().includes(lowercaseQuery)
      )
    })
  }, [searchFields])

  // Función de ordenamiento
  const sortData = useCallback((data: T[], column: string, direction: 'asc' | 'desc'): T[] => {
    if (!column) return data

    return [...data].sort((a, b) => {
      const aValue = a[column]
      const bValue = b[column]

      if (aValue === null || aValue === undefined) return 1
      if (bValue === null || bValue === undefined) return -1

      if (typeof aValue === 'string' && typeof bValue === 'string') {
        return direction === 'asc'
          ? aValue.localeCompare(bValue)
          : bValue.localeCompare(aValue)
      }

      if (typeof aValue === 'number' && typeof bValue === 'number') {
        return direction === 'asc' ? aValue - bValue : bValue - aValue
      }

      if (aValue instanceof Date && bValue instanceof Date) {
        return direction === 'asc'
          ? aValue.getTime() - bValue.getTime()
          : bValue.getTime() - aValue.getTime()
      }

      // Comparación por defecto
      const aStr = String(aValue)
      const bStr = String(bValue)
      return direction === 'asc'
        ? aStr.localeCompare(bStr)
        : bStr.localeCompare(aStr)
    })
  }, [])

  // Datos procesados
  const filteredData = useMemo(() => {
    let result = [...data]

    // Aplicar filtros personalizados
    if (filterFn) {
      result = filterFn(result, filters)
    }

    // Aplicar búsqueda
    if (searchQuery) {
      result = searchData(result, searchQuery)
    }

    // Aplicar ordenamiento
    if (sortColumn) {
      result = sortData(result, sortColumn, sortDirection)
    }

    return result
  }, [data, filters, searchQuery, sortColumn, sortDirection, filterFn, searchData, sortData])

  // Datos paginados
  const paginatedData = useMemo(() => {
    const startIndex = (currentPage - 1) * pageSize
    const endIndex = startIndex + pageSize
    return filteredData.slice(startIndex, endIndex)
  }, [filteredData, currentPage, pageSize])

  // Total de páginas
  const totalPages = Math.ceil(filteredData.length / pageSize)

  // Acciones
  const actions: TableActions<T> = {
    // Paginación
    setCurrentPage: (page: number) => {
      setCurrentPage(Math.max(1, Math.min(page, totalPages)))
    },

    setPageSize: (size: number) => {
      setPageSize(size)
      setCurrentPage(1) // Reset a la primera página
    },

    // Ordenamiento
    setSort: (column: string, direction: 'asc' | 'desc') => {
      setSortColumn(column)
      setSortDirection(direction)
      setCurrentPage(1) // Reset a la primera página
    },

    toggleSort: (column: string) => {
      if (sortColumn === column) {
        setSortDirection(sortDirection === 'asc' ? 'desc' : 'asc')
      } else {
        setSortColumn(column)
        setSortDirection('asc')
      }
      setCurrentPage(1) // Reset a la primera página
    },

    // Búsqueda
    setSearchQuery: (query: string) => {
      setSearchQuery(query)
      setCurrentPage(1) // Reset a la primera página
    },

    // Filtros
    setFilter: (key: string, value: any) => {
      setFilters(prev => ({
        ...prev,
        [key]: value
      }))
      setCurrentPage(1) // Reset a la primera página
    },

    clearFilters: () => {
      setFilters({})
      setCurrentPage(1) // Reset a la primera página
    },

    // Selección
    setSelectedRows: (rows: T[]) => {
      setSelectedRows(rows)
    },

    toggleRowSelection: (row: T) => {
      setSelectedRows(prev => {
        const isSelected = prev.some(item => item.id === row.id)
        if (isSelected) {
          return prev.filter(item => item.id !== row.id)
        } else {
          return [...prev, row]
        }
      })
    },

    selectAll: () => {
      setSelectedRows([...filteredData])
    },

    clearSelection: () => {
      setSelectedRows([])
    },
  }

  const state: TableState<T> = {
    currentPage,
    pageSize,
    totalPages,
    sortColumn,
    sortDirection,
    searchQuery,
    filters,
    selectedRows,
    filteredData,
    paginatedData,
  }

  return [state, actions]
}
