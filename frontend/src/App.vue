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
			<div>
				<label>Password</label>
				<input type="password" v-model="password">
			</div>

			<button @click="submitForm()">
				{{ editId === null ? 'Create' : 'Save' }}
			</button>
		</div>

		<CustomerTable :customers="customers"/>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
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

function createCustomerRequest() {
	return {
		first_name: firstName.value,
		last_name: lastName.value,
		birth_date: birthDate.value,
		user_name: userName.value,
		password: password.value,
	};
}

async function submitForm() {
	await CustomerApi.createCustomer(
			createCustomerRequest()
	);
	await reloadCustomers();
	clearForm();
}
</script>

<style>
.container {
	width: 900px;
	margin: 40px auto;
	font-family: Arial, sans-serif;
}
.form {
	margin-bottom: 30px;
}
.form > div {
	margin-bottom: 10px;
}
label {
	display: inline-block;
	width: 120px;
}
input {
	width: 250px;
}
</style>
