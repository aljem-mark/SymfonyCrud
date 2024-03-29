<template>
	<b-container fluid>
		<b-row class="justify-content-between align-items-center my-3">
			<b-col md="auto"
				cols="12">
				<h1 class="mb-md-0 mb-2">Users</h1>
			</b-col>
			<b-col md="auto"
				cols="12"
				class="my-1">
				<b-input-group>
					<b-form-input v-model="filter"
						placeholder="Type to Search" />
					<b-input-group-append>
						<b-btn :disabled="!filter"
							@click="filter = ''">Clear</b-btn>
					</b-input-group-append>
				</b-input-group>
			</b-col>
		</b-row>
        <b-alert v-if="errors.length"
            show
            variant="danger">
            <b>Please correct the following error(s):</b>
            <ul>
                <li v-for="(error, index) in errors" :key="index">
                    {{ error }}
                </li>
            </ul>
        </b-alert>
        <b-alert v-if="success"
            dismissible
            :show="dismissCountDown"
            @dismissed="dismissCountDown=0"
            @dismiss-count-down="countDownChanged"
            variant="success">
            <p>{{ success }}</p>
            <b-progress variant="warning"
                :max="dismissSecs"
                :value="dismissCountDown"
                height="4px">
            </b-progress>
        </b-alert>
		<b-table hover
			:items="items"
			:fields="fields"
			:current-page="currentPage"
			:per-page="perPage"
			:filter="filter"
			@filtered="onFiltered">
			<template slot="actions" slot-scope="row">
				<b-button size="sm"
                    :href="generateEditUrl(row.item.id)"
					variant="primary"
					class="mr-1">
					<i class="fas fa-user-edit"></i>
				</b-button>
				<b-button size="sm"
                    @click="deleteUser(row.item.id)"
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
import { EventBus } from '../../app';

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
            totalRows: 0,
			filter: null,
            errors: [],
			success: '',
            dismissSecs: 5,
            dismissCountDown: 0,
		}
	},
	created() {
        this.fetchUsersList()
    },
    methods: {
        fetchUsersList() {
            let url = this.$routing.generate('api_user_list')
            this.$axios.get(url)
            .then(response => {
                this.items = response.data.users
                this.totalRows = this.items.length
            })
            .catch(e => {
                this.errors = e.response.data.errors
            });
        },
        countDownChanged (dismissCountDown) {
            this.dismissCountDown = dismissCountDown
        },
        showSuccessAlert () {
            this.dismissCountDown = this.dismissSecs
		},
		onFiltered (filteredItems) {
			// Trigger pagination to update the number of buttons/pages due to filtering
			this.totalRows = filteredItems.length
			this.currentPage = 1
		},
        generateEditUrl(uid) {
            return this.$routing.generate('user_edit', { id: uid })
        },
        deleteUser(uid) {
            this.errors = [];
            this.success = '';

			if(!this.errors.length) {
				let url = this.$routing.generate('api_user_delete', { id: uid })
				this.$axios.post(url)
				.then(response => {
                    this.success = response.data.success
					this.showSuccessAlert()
					
					EventBus.$emit('modalAddUser', true);
				})
				.catch(e => {
					this.errors = e.response.data.errors
				});
			}
        },
		onFiltered (filteredItems) {
			this.totalRows = filteredItems.length
			this.currentPage = 1
		},
	},
	mounted: function () {
		const self = this
		EventBus.$on('modalAddUser', (bool) => {
			if(bool === true) {
				self.fetchUsersList()
			}
		});
	},
}
</script>