export interface Client {
    id: number;
    name: string;
    identity_type: string;
    identity_number: string;
    phone_number: string;
    email: string;
}

export interface IndexProps {
    clients: Client[];
}
