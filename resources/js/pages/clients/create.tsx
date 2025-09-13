import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

interface FormData {
    name: string;
    identity_type: string;
    identity_number: string;
    phone_number: string;
    email: string;
}

export default function create() {
    const { data, setData, post, processing, errors, reset } = useForm<FormData>({
        name: '',
        identity_type: '',
        identity_number: '',
        phone_number: '',
        email: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        
        // Validar que se haya seleccionado un tipo de identificación
        if (!data.identity_type) {
            alert('Por favor selecciona un tipo de identificación');
            return;
        }
        
        post('/clients', {
            onSuccess: () => {
                reset();
            },
        });
    };

    return (
        <>
            <Head title="Crear Cliente" />
            
            <div className="container mx-auto py-6">
                <Card className="max-w-2xl mx-auto">
                    <CardHeader>
                        <CardTitle>Crear Nuevo Cliente</CardTitle>
                        <CardDescription>
                            Ingresa la información del cliente para crear un nuevo registro
                        </CardDescription>
                    </CardHeader>
                    
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <div className="grid gap-2">
                                <Label htmlFor="name">Nombre Completo</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    required
                                    autoFocus
                                    placeholder="Ingresa el nombre completo"
                                    autoComplete="name"
                                />
                                <InputError message={errors.name} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="identity_type">Tipo de Identificación</Label>
                                <Select 
                                    value={data.identity_type} 
                                    onValueChange={(value) => setData('identity_type', value)}
                                    required
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecciona el tipo de identificación" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="CC">Cédula de Ciudadanía (CC)</SelectItem>
                                        <SelectItem value="CE">Cédula de Extranjería (CE)</SelectItem>
                                        <SelectItem value="NIT">NIT</SelectItem>
                                        <SelectItem value="Passport">Pasaporte</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.identity_type} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="identity_number">Número de Identificación</Label>
                                <Input
                                    id="identity_number"
                                    type="text"
                                    value={data.identity_number}
                                    onChange={(e) => setData('identity_number', e.target.value)}
                                    required
                                    placeholder="Ingresa el número de cédula o documento"
                                />
                                <InputError message={errors.identity_number} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="phone_number">Número de Teléfono</Label>
                                <Input
                                    id="phone_number"
                                    type="tel"
                                    value={data.phone_number}
                                    onChange={(e) => setData('phone_number', e.target.value)}
                                    required
                                    placeholder="Ingresa el número de teléfono"
                                    autoComplete="tel"
                                />
                                <InputError message={errors.phone_number} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">Correo Electrónico</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    required
                                    placeholder="correo@ejemplo.com"
                                    autoComplete="email"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="flex gap-4 pt-4">
                                <Button type="submit" disabled={processing} className="flex-1">
                                    {processing && <LoaderCircle className="animate-spin" />}
                                    Crear Cliente
                                </Button>
                                
                                <Button 
                                    type="button" 
                                    variant="outline" 
                                    onClick={() => window.history.back()}
                                    className="flex-1"
                                >
                                    Cancelar
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
