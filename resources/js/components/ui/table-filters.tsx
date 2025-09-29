import * as React from "react"
import { X, Filter } from "lucide-react"
import { cn } from "@/lib/utils"
import { Button } from "./button"
import { Input } from "./input"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "./select"
import { Card, CardContent, CardHeader, CardTitle } from "./card"

export interface FilterOption {
    key: string
    label: string
    type: 'text' | 'select' | 'date' | 'number'
    options?: { value: string; label: string }[]
    placeholder?: string
}

export interface TableFiltersProps {
  filters: FilterOption[]
  values: Record<string, any>
  onChange: (key: string, value: any) => void
  onClear: () => void
  className?: string
  showClearButton?: boolean
}

function TableFilters({
  filters,
  values,
  onChange,
  onClear,
  className,
  showClearButton = true,
}: TableFiltersProps) {
  const hasActiveFilters = Object.values(values).some(value =>
    value !== null && value !== undefined && value !== ''
  )

  const renderFilterInput = (filter: FilterOption) => {
    const value = values[filter.key] || ''

    switch (filter.type) {
      case 'select':
        return (
          <Select value={value} onValueChange={(newValue) => onChange(filter.key, newValue)}>
            <SelectTrigger className="w-full">
              <SelectValue placeholder={filter.placeholder || `Seleccionar ${filter.label}`} />
            </SelectTrigger>
            <SelectContent>
              {filter.options?.map((option) => (
                <SelectItem key={option.value} value={option.value}>
                  {option.label}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        )

      case 'date':
        return (
          <Input
            type="date"
            value={value}
            onChange={(e) => onChange(filter.key, e.target.value)}
            placeholder={filter.placeholder}
            className="w-full"
          />
        )

      case 'number':
        return (
          <Input
            type="number"
            value={value}
            onChange={(e) => onChange(filter.key, e.target.value)}
            placeholder={filter.placeholder}
            className="w-full"
          />
        )

      case 'text':
      default:
        return (
          <Input
            type="text"
            value={value}
            onChange={(e) => onChange(filter.key, e.target.value)}
            placeholder={filter.placeholder || `Buscar por ${filter.label}`}
            className="w-full"
          />
        )
    }
  }

  if (filters.length === 0) {
    return null
  }

  return (
    <Card className={cn("w-full", className)}>
      <CardHeader className="pb-3">
        <div className="flex items-center justify-between">
          <CardTitle className="text-sm font-medium flex items-center gap-2">
            <Filter className="h-4 w-4" />
            Filtros
          </CardTitle>
          {showClearButton && hasActiveFilters && (
            <Button
              variant="ghost"
              size="sm"
              onClick={onClear}
              className="h-8 px-2 text-muted-foreground hover:text-foreground"
            >
              <X className="h-4 w-4 mr-1" />
              Limpiar
            </Button>
          )}
        </div>
      </CardHeader>
      <CardContent className="pt-0">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          {filters.map((filter) => (
            <div key={filter.key} className="space-y-2">
              <label className="text-sm font-medium text-muted-foreground">
                {filter.label}
              </label>
              {renderFilterInput(filter)}
            </div>
          ))}
        </div>
      </CardContent>
    </Card>
  )
}

export { TableFilters }
