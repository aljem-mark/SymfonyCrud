<template>
	<b-container fluid>
		<b-row class="justify-content-between align-items-center my-3">
			<b-col md="auto"
				cols="12">
				<h1 class="mb-md-0 mb-2">Users</h1>
			</b-col>
		</b-row>
		<b-table hover
			:items="items"
			:fields="fields"
			:current-page="currentPage"
			:per-page="perPage">
			<template slot="actions" slot-scope="row">
				<b-button size="sm"
					variant="primary"
					class="mr-1">
					<i class="fas fa-user-edit"></i>
				</b-button>
				<b-button size="sm"
					variant="danger">
					<i class="fas fa-trash-alt"></i>
				</b-button>
			</template>
		</b-table>
		<b-pagination align="center"
			:total-rows="totalRows"
			:per-page="perPage"
			v-model="currentPage"
			class="my-0"/>
	</b-container>
</template>

<script>
export default {
	data () {
		return {
			fields: [
				{
					key: 'id',
					sortable: true
				},
				{
					key: 'name',
					sortable: true
				},
				{
					key: 'username',
					sortable: true,
				},
				{
					key: 'email',
					sortable: true,
				},
				{
					key: 'gender',
					sortable: true,
				},
				{
					key: 'description',
					sortable: true,
				},
				{
					key: 'actions',
					label: 'Actions',
				}
			],
			items: [],
			currentPage: 1,
			perPage: 5,
			totalRows: 0
		}
	},
	created() {
		this.$axios.get('api/users')
        .then(response => {
            this.items = response.data.users
            this.totalRows = this.items.length
        })
        .catch(e => {
            this.errors.push(e)
        });
	}
}
</script>