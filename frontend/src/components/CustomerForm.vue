<template>
	<div class="form">
		<table>
			<tr>
				<td>
					<div>
						<label>First name</label>
						<input v-model="customer.firstName"/>
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
						<input v-model="customer.lastName">
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
						<input type="date" v-model="customer.birthDate">
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
						<input v-model="customer.userName">
					</div>
				</td>
				<td class="error">
					{{ errors.userName }}
				</td>
			</tr>
			<tr>
				<td>
					<div class="password-row">
						<div v-if="customer.id === null">
							<label>Password</label>
							<input type="password" v-model="customer.password">
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
						<button @click="handleSubmit()">
							{{ customer.id === null ? 'Create' : 'Save' }}
						</button>
						<button v-if="customer.id !== null" @click="clearForm()">
							Cancel
						</button>
					</div>
				</td>
			</tr>
		</table>
	</div>
</template>

<script setup>
import { reactive, watch } from 'vue';

const customer = defineModel('customer');

const props = defineProps({
	formError: String
});

const errors = reactive({
	firstName: '',
	lastName: '',
	birthDate: '',
	userName: '',
	password: '',
});

watch(() => customer.value.id,
		() => { clearErrors(); }
);

const emit = defineEmits([
	'submit',
	'cancel',
]);

function handleSubmit() {
	if (validate()) {
		emit('submit');
	}
}

function validate() {
	let isValid = true;
	clearErrors();

	if (!customer.value.firstName.trim()) {
		errors.firstName = 'First name is required.';
		isValid = false;
	}
	if (!customer.value.lastName.trim()) {
		errors.lastName = 'Last name is required.';
		isValid = false;
	}
	if (!customer.value.birthDate.trim()) {
		errors.birthDate = 'Birth date is required.';
		isValid = false;
	}
	if (!customer.value.userName.trim()) {
		errors.userName = 'Username is required.';
		isValid = false;
	}
	if (customer.value.id === null && !customer.value.password.trim()) {
		errors.password = 'Password is required.';
		isValid = false;
	}
	return isValid;
}

function clearForm() {
	customer.value.firstName = '';
	customer.value.lastName = '';
	customer.value.birthDate = '';
	customer.value.userName = '';
	customer.value.password = '';

	clearErrors();
	emit('cancel');
}

function clearErrors() {
	errors.firstName = '';
	errors.lastName = '';
	errors.birthDate = '';
	errors.userName = '';
	errors.password = '';
}
</script>

<style scoped>
.button-row {
	display: flex;
	gap: 10px;
}
.button-row > button {
	width: 80px;
}
.error {
	width: 220px;
	padding-left: 10px;
	color: #d32f2f;
	font-size: 0.9rem;
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
	margin-top: 10px;
	margin-bottom: 10px;
}
.password-row {
	min-height: 30px;
}
</style>