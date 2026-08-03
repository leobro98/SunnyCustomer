<template>
	<div class="container">
		<h1>Sunny Customer</h1>

		<div class="form">
			<table>
				<tr>
					<td>
						<div>
							<label>First name</label>
							<input v-model="firstName">
						</div>
					</td>
					<td class="error">
						{{ errors.firstName }}
					</td>
				</tr>
				<tr>
					<td>
						<div>
							<label>Last name</label>
							<input v-model="lastName">
						</div>
					</td>
					<td class="error">
						{{ errors.lastName }}
					</td>
				</tr>
				<tr>
					<td>
						<div>
							<label>Birth date</label>
							<input type="date" v-model="birthDate">
						</div>
					</td>
					<td class="error">
						{{ errors.birthDate }}
					</td>
				</tr>
				<tr>
					<td>
						<div>
							<label>Username</label>
							<input v-model="userName">
						</div>
					</td>
					<td class="error">
						{{ errors.userName }}
					</td>
				</tr>
				<tr>
					<td>
						<div class="password-row">
							<div v-if="editId === null">
								<label>Password</label>
								<input type="password" v-model="password">
							</div>
						</div>
					</td>
					<td class="error">
						<div class="password-row">
							{{ errors.password }}
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div v-if="formError" class="form-error">
							{{ formError }}
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="button-row">
							<button @click="submitForm()">
								{{ editId === null ? 'Create' : 'Save' }}
							</button>
							<button v-if="editId !== null" @click="clearForm()">
								Cancel
							</button>
						</div>
					</td>
				</tr>
			</table>
		</div>

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

const customers = ref([]);

const firstName = ref('');
const lastName = ref('');
const birthDate = ref('');
const userName = ref('');
const password = ref('');
const editId = ref(null);

const errors = reactive({
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
	firstName.value = '';
	lastName.value = '';
	birthDate.value = '';
	userName.value = '';
	password.value = '';

	editId.value = null;

	clearErrors();
	formError.value = '';
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
	clearErrors();
	formError.value = '';

	if (!validate()) {
		return;
	}

	try {
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
	} catch (error) {
		formError.value = error instanceof Error
				? error.message
				: 'Unknown error.';
	}
}

function editCustomer(customer) {
	clearErrors();
	formError.value = '';

	editId.value = customer.id;

	firstName.value = customer.firstName;
	lastName.value = customer.lastName;
	birthDate.value = customer.birthDate;
	userName.value = customer.userName;
	password.value = '';
}

async function deleteCustomer(customer) {
	if (!confirm(`Delete customer "${customer.firstName} ${customer.lastName}"?`)) {
		return;
	}

	await CustomerApi.deleteCustomer(customer.id);
	await reloadCustomers();
}

function clearErrors() {
	errors.firstName = '';
	errors.lastName = '';
	errors.birthDate = '';
	errors.userName = '';
	errors.password = '';
}

function validate() {
	let isValid = true;
	clearErrors();

	if (!firstName.value.trim()) {
		errors.firstName = 'First name is required.';
		isValid = false;
	}
	if (!lastName.value.trim()) {
		errors.lastName = 'Last name is required.';
		isValid = false;
	}
	if (!birthDate.value.trim()) {
		errors.birthDate = 'Birth date is required.';
	}
	if (!userName.value.trim()) {
		errors.userName = 'Username is required.';
		isValid = false;
	}
	if (editId.value === null && !password.value.trim()) {
		errors.password = 'Password is required.';
		isValid = false;
	}
	return isValid;
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


.button-row {
	display: flex;
	gap: 10px;
}
.button-row > button {
	width: 80px;
}
.container {
	width: 900px;
	margin: 40px auto;
	font-family: Arial, sans-serif;
}
.error {
	width: 220px;
	padding-left: 10px;
	color: #d32f2f;
}
.form {
	margin-bottom: 30px;
}
.form div {
	margin-bottom: 5px;
}
.form-error {
	color: #d32f2f;
	font-weight: bold;
	margin-bottom: 10px;
}
.password-row {
	min-height: 30px;
}
</style>
