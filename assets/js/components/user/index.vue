<template>
	<b-container fluid>
		<b-row class="justify-content-between align-items-center my-3">
			<b-col md="auto"
				cols="12">
				<h1 class="mb-md-0 mb-2">Users</h1>
			</b-col>
		</b-row>
        <b-alert v-if="errors.length"
            show
            variant="danger">
            <b>Please correct the following error(s):</b>
            <ul>
                <li v-for="error in errors">
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
			:per-page="perPage">
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
            errors: [],
            success: '',
            dismissSecs: 5,
            dismissCountDown: 0,
		}
	},
	created() {
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
    methods: {
        countDownChanged (dismissCountDown) {
            this.dismissCountDown = dismissCountDown
        },
        showSuccessAlert () {
            this.dismissCountDown = this.dismissSecs
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
				})
				.catch(e => {
					this.errors = e.response.data.errors
				});
			}
        }
    },
    updated: function () {
        this.$nextTick(function () {
            let url = this.$routing.generate('api_user_list')
            this.$axios.get(url)
            .then(response => {
                this.items = response.data.users
                this.totalRows = this.items.length
            })
            .catch(e => {
                this.errors = e.response.data.errors
            });
        })
    }
}
</script>