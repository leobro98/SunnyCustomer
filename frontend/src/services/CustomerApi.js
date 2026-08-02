import Customer from '../models/Customer';

export default class CustomerApi {

    static async getCustomers() {

        const response = await fetch('/customers');

        if (!response.ok) {
            throw new Error('Unable to load customers');
        }

        const body = await response.json();
        return body.map(json => new Customer(json));
    }

    static async createCustomer(request) {
        const response = await fetch('/customers', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(request)
        });

        if (!response.ok) {
            throw new Error('Unable to create customer');
        }
    }
}
