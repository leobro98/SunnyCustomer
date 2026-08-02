<template>
	<table>
		<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Birth date</th>
				<th>Username</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="customer in customers" :key="customer.id">
				<td>
					{{ customer.id }}
				</td>
				<td>
					{{ customer.firstName }}
					{{ customer.lastName }}
				</td>
				<td>
					{{ customer.birthDate }}
				</td>
				<td>
					{{ customer.userName }}
				</td>
				<td>
					<button @click="">
						Edit
					</button>
					<button @click="">
						Delete
					</button>
				</td>
			</tr>
		</tbody>
	</table>
</template>

<script setup>
const { customers } = defineProps({
	customers: Array,
});
</script>

<style scoped>
table {
	border-collapse: collapse;
	width: 100%;
}

th,
td {
	border: 1px solid #ccc;
	padding: 8px;
}

th {
	background: #f3f3f3;
	text-align: left;
}

button {
	margin-right: 6px;
}
</style>