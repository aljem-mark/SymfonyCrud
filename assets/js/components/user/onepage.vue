<template>
    <div>
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
                :per-page="perPage">
                <template slot="actions" slot-scope="row">
                    <b-button size="sm"
                        @click="editUser(row.item.id)"
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
            <b-row class="justify-content-center my-5">
                <b-col md="4"
                    sm="8"
                    cols="12">
                    <b-card header="Edit User"
                        header-tag="h1">
                        <b-form @submit="onSubmit">
                            <b-alert v-if="formErrors.length"
                                show
                                variant="danger">
                                <b>Please correct the following error(s):</b>
                                <ul>
                                    <li v-for="(error, index) in formErrors" :key="index">
                                        {{ error }}
                                    </li>
                                </ul>
                            </b-alert>
                            <b-alert v-if="formSuccess"
                                show
                                variant="success">
                                <p>{{ formSuccess }}</p>
                            </b-alert>
                            <b-form-group label="Name"
                                label-for="editName">
                                <b-form-input id="editName"
                                    type="text"
                                    v-model="form.name"
                                    required></b-form-input>
                            </b-form-group>
                            <b-form-group label="Username"
                                label-for="editUsername">
                                <b-form-input id="editUsername"
                                    type="text"
                                    v-model="form.username"
                                    required></b-form-input>
                            </b-form-group>
                            <b-form-group label="Email"
                                label-for="editEmail">
                                <b-form-input id="editEmail"
                                    type="email"
                                    v-model="form.email"
                                    required></b-form-input>
                            </b-form-group>
                            <b-form-group label="Gender"
                                label-for="editGender">
                                <b-form-select id="editGender"
                                    v-model="form.gender"
                                    required
                                    :options="genderOptions"></b-form-select>
                            </b-form-group>
                            <div role="group" class="b-form-group form-group">
                                <label for="editDescription" class="col-form-label pt-0">Description</label>
                                <div class="">
                                    <textarea id="editDescription" rows="4" class="form-control" wrap="soft" v-model="form.description"></textarea>
                                </div>
                            </div>
                            <b-button id="editSave"
                                type="submit"
                                variant="primary"
                                :disabled="this.uid == null">Save</b-button>
                            <b-button @click="resetForm"
                                variant="secondary">Reset</b-button>
                        </b-form>
                    </b-card>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
import { EventBus } from '../../app';

export default {
    name: 'user-onepage',
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
            uid: null,
            form: {
                name: null,
                username: null,
                email: null,
                gender: null,
                description: null,
            },
			genderOptions: [
                { value: 'm', text: 'Male' },
                { value: 'f', text: 'Female' },
            ],
            formErrors: [],
            formSuccess: null,
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
        forceRerender() {
            this.renderTable = false;
            
            this.$nextTick(() => {
                this.renderTable = true;
            });
        },
        countDownChanged (dismissCountDown) {
            this.dismissCountDown = dismissCountDown
        },
        showSuccessAlert () {
            this.dismissCountDown = this.dismissSecs
        },
        editUser(uid) {
            let url = this.$routing.generate('api_user_edit', { id: uid })
            this.$axios.post(url)
            .then(response => {
                this.form = response.data.user
                this.uid = uid
            })
            .catch(e => {
                this.errors.push(e)
            });
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
        },
        resetForm() {
            this.uid = null
            for (const key in this.form) {
                if (this.form.hasOwnProperty(key)) {
                    this.form[key] = null
                }
            }
        },
		onSubmit (evt) {
			evt.preventDefault();
			this.errors = [];
            this.success = '';

			if(!this.errors.length) {
                let url = this.$routing.generate('api_user_update', { id: this.uid })
				this.$axios.post(url, this.form)
				.then(response => {
                    this.formSuccess = response.data.success
                    this.fetchUsersList()
				})
				.catch(e => {
					this.formErrors = e.response.data.errors
				});
			}
		}
    },
	mounted: function () {
        const self = this
		EventBus.$on('modalAddUser', function (bool) {
			if(bool === true) {
				self.fetchUsersList()
			}
		});
	},
}
</script>