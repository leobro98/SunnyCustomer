<template>
	<div class="container">
		<h1>Sunny Customer</h1>

		<CustomerForm
				v-model:customer="formCustomer"
				:form-error="formError"
				@submit="submitForm"
				@cancel="clearForm"
		/>

		<CustomerTable
				:customers="customers"
				@edit="editCustomer"
				@delete="deleteCustomer"
		/>
	</div>
</template>

<script setup>
import {onMounted, ref, reactive} from 'vue';
import CustomerTable from './components/CustomerTable.vue';
import CustomerApi from "./services/CustomerApi.js";
import CustomerForm from "@/components/CustomerForm.vue";

const customers = ref([]);
const formCustomer = reactive({
	id: null,
	firstName: '',
	lastName: '',
	birthDate: '',
	userName: '',
	password: '',
});
const formError = ref('');

async function reloadCustomers() {
	customers.value = await CustomerApi.getCustomers();
}

onMounted(async () => {
	await reloadCustomers();
});

function clearForm() {
	formCustomer.id = null;
	formCustomer.firstName = '';
	formCustomer.lastName = '';
	formCustomer.birthDate = '';
	formCustomer.userName = '';
	formCustomer.password = '';

	formError.value = '';
}

function createNewCustomerRequest() {
	return {
		first_name: formCustomer.firstName,
		last_name: formCustomer.lastName,
		birth_date: formCustomer.birthDate,
		user_name: formCustomer.userName,
		password: formCustomer.password
	};
}

function createUpdatedCustomerRequest() {
	return {
		first_name: formCustomer.firstName,
		last_name: formCustomer.lastName,
		birth_date: formCustomer.birthDate,
		user_name: formCustomer.userName
	};
}

function editCustomer(customer) {
	formError.value = '';

	formCustomer.id = customer.id;
	formCustomer.firstName = customer.firstName;
	formCustomer.lastName = customer.lastName;
	formCustomer.birthDate = customer.birthDate;
	formCustomer.userName = customer.userName;
	formCustomer.password = '';
}

async function submitForm() {
	formError.value = '';

	try {
		if (formCustomer.id === null) {
			await CustomerApi.createCustomer(
					createNewCustomerRequest()
			);
		} else {
			await CustomerApi.updateCustomer(
					formCustomer.id,
					createUpdatedCustomerRequest()
			);
		}

		await reloadCustomers();
		clearForm();
	} catch (error) {
		formError.value = error instanceof Error
				? error.message
				: 'Unknown error.';
	}
}

async function deleteCustomer(customer) {
	if (!confirm(`Delete customer "${customer.firstName} ${customer.lastName}"?`)) {
		return;
	}

	try {
		await CustomerApi.deleteCustomer(customer.id);
		await reloadCustomers();
	} catch (error) {
		formError.value = error instanceof Error
				? error.message
				: 'Unknown error.';
	}
}
</script>

<style>
label {
	display: inline-block;
	width: 120px;
}
input {
	width: 250px;
}

.container {
	width: 900px;
	margin: 40px auto;
	font-family: Arial, sans-serif;
}
</style>
