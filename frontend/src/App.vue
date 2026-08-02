<template>
	<div class="container">
		<h1>Sunny Customer</h1>

		<div class="form">
			<div>
				<label>First name</label>
				<input v-model="firstName">
			</div>
			<div>
				<label>Last name</label>
				<input v-model="lastName">
			</div>
			<div>
				<label>Birth date</label>
				<input type="date" v-model="birthDate">
			</div>
			<div>
				<label>Username</label>
				<input v-model="userName">
			</div>
			<div class="password-row">
				<div v-if="editId === null">
					<label>Password</label>
					<input type="password" v-model="password">
				</div>
			</div>
			<div class="button-row">
				<button @click="submitForm()">
					{{ editId === null ? 'Create' : 'Save' }}
				</button>
				<button v-if="editId !== null" @click="clearForm()">
					Cancel
				</button>
			</div>
		</div>

		<CustomerTable
				:customers="customers"
				@edit="editCustomer"
				@delete="deleteCustomer"
		/>
	</div>
</template>

<script setup>
import {onMounted, ref} from 'vue';
import CustomerTable from './components/CustomerTable.vue';
import CustomerApi from "./services/CustomerApi.js";

const customers = ref([]);

const firstName = ref('');
const lastName = ref('');
const birthDate = ref('');
const userName = ref('');
const password = ref('');

const editId = ref(null);

async function reloadCustomers() {
	customers.value = await CustomerApi.getCustomers();
}

onMounted(async () => {
	await reloadCustomers();
});

function clearForm() {
	firstName.value = '';
	lastName.value = '';
	birthDate.value = '';
	userName.value = '';
	password.value = '';

	editId.value = null;
}

function createNewCustomerRequest() {
	return {
		first_name: firstName.value,
		last_name: lastName.value,
		birth_date: birthDate.value,
		user_name: userName.value,
		password: password.value
	};
}

function createUpdatedCustomerRequest() {
	return {
		first_name: firstName.value,
		last_name: lastName.value,
		birth_date: birthDate.value,
		user_name: userName.value
	};
}

async function submitForm() {
	if (editId.value === null) {
		await CustomerApi.createCustomer(
				createNewCustomerRequest()
		);
	} else {
		await CustomerApi.updateCustomer(
				editId.value,
				createUpdatedCustomerRequest()
		);
	}

	await reloadCustomers();
	clearForm();
}

function editCustomer(customer) {
	editId.value = customer.id;

	firstName.value = customer.firstName;
	lastName.value = customer.lastName;
	birthDate.value = customer.birthDate;
	userName.value = customer.userName;
	password.value = '';
}

function deleteCustomer(customer) {
	editId.value = customer.id;
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
.button-row {
	display: flex;
	gap: 10px;
}
.button-row > button {
	width: 80px;
}
.form {
	margin-bottom: 30px;
}
.form > div {
	margin-bottom: 10px;
}
.password-row {
	min-height: 40px;
}
</style>
