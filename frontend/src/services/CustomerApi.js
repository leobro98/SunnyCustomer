import Customer from '../models/Customer';

export default class CustomerApi {

    static async getCustomers() {
        const response = await fetch('/customers');

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message);
        }

        const body = await response.json();
        return body.map(json => new Customer(json));
    }

    static async createCustomer(request) {
        const response = await fetch('/customers', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(request)
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message);
        }
    }

    static async updateCustomer(id, request) {
        const response = await fetch(`/customers/${id}`, {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(request),
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message);
        }
    }

    static async deleteCustomer(id) {
        const response = await fetch(`/customers/${id}`, {
            method: 'DELETE'
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message);
        }
    }
}
