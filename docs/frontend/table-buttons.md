# Sistema de Botones Escalable para Tablas

## Descripción

El sistema de botones de la tabla ha sido diseñado para ser completamente escalable y flexible, permitiendo diferentes configuraciones según las necesidades de cada componente.

## Características Principales

### 1. Múltiples Botones
- Soporte para múltiples botones en el header de la tabla
- Cada botón puede tener su propia configuración (variante, tamaño, icono, etc.)
- Los botones se renderizan dinámicamente según las props proporcionadas

### 2. Compatibilidad hacia atrás
- Mantiene la prop `button` para compatibilidad con código existente
- La nueva prop `buttons` (array) permite múltiples botones
- Ambas props pueden usarse simultáneamente

### 3. Configuración Flexible
Cada botón puede configurarse con:
- `label`: Texto del botón
- `onClick`: Función a ejecutar
- `variant`: Estilo del botón (default, destructive, outline, etc.)
- `size`: Tamaño del botón (sm, default, lg, icon)
- `icon`: Icono a mostrar
- `className`: Clases CSS personalizadas
- `disabled`: Estado deshabilitado

## Ejemplos de Uso

### Tabla Básica (Sin Botones)
```tsx
<Table
  data={data}
  columns={columns}
  searchable={true}
  sortable={true}
/>
```

### Tabla con Un Botón (Compatibilidad)
```tsx
<Table
  data={data}
  columns={columns}
  button={{
    label: 'Crear',
    onClick: handleCreate,
    variant: 'default',
    icon: <Plus className="h-4 w-4" />
  }}
/>
```

### Tabla con Múltiples Botones
```tsx
<Table
  data={data}
  columns={columns}
  buttons={[
    {
      label: 'Crear',
      onClick: handleCreate,
      variant: 'default',
      icon: <Plus className="h-4 w-4" />
    },
    {
      label: 'Exportar',
      onClick: handleExport,
      variant: 'outline',
      icon: <Download className="h-4 w-4" />
    },
    {
      label: `Eliminar (${selectedCount})`,
      onClick: handleBulkDelete,
      variant: 'destructive',
      icon: <Trash2 className="h-4 w-4" />,
      disabled: selectedCount === 0
    }
  ]}
/>
```

### Tabla Completa con Todas las Funcionalidades
```tsx
<ClientsTable
  clients={clients}
  onEdit={handleEdit}
  onDelete={handleDelete}
  onView={handleView}
  onCreate={handleCreate}
  onExport={handleExport}
  onBulkDelete={handleBulkDelete}
  showHeader={true}
  title="Gestión de Clientes"
  showBulkActions={true}
/>
```

## Componentes Disponibles

### ClientsTable (Completo)
- Todas las funcionalidades habilitadas
- Botones dinámicos según selección
- Acciones masivas
- Exportación

### SimpleClientsTable (Básico)
- Solo funcionalidades esenciales
- Sin selección múltiple
- Sin botones adicionales
- Ideal para casos simples

## Ventajas del Sistema

1. **Escalabilidad**: Fácil agregar/quitar botones según necesidades
2. **Reutilización**: Un solo componente Table para todos los casos
3. **Flexibilidad**: Cada botón puede tener configuración independiente
4. **Mantenibilidad**: Código centralizado y bien estructurado
5. **UX Consistente**: Interfaz uniforme en toda la aplicación

## Mejores Prácticas

1. **Usar iconos**: Siempre incluir iconos para mejor UX
2. **Estados dinámicos**: Los botones pueden cambiar según el estado (ej: cantidad seleccionada)
3. **Confirmaciones**: Implementar confirmaciones para acciones destructivas
4. **Loading states**: Considerar estados de carga para acciones asíncronas
5. **Accesibilidad**: Usar labels descriptivos y mantener consistencia

## Casos de Uso Comunes

- **CRUD básico**: Crear, editar, eliminar
- **Acciones masivas**: Eliminar múltiples registros
- **Exportación**: Exportar datos a diferentes formatos
- **Filtros avanzados**: Botones para aplicar filtros específicos
- **Navegación**: Botones para navegar a otras secciones

