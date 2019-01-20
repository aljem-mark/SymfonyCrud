<template>
    <b-container>
        <b-row class="justify-content-center my-5">
            <b-col md="8">
                <b-card header="Edit User"
                    header-tag="h1">
                    <b-form @submit="onSubmit">
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
							show
							variant="success">
                            <p>{{ success }}</p>
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
							variant="primary">Save</b-button>
					</b-form>
                </b-card>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
export default {
    name: 'user-edit',
    props: ['id'],
    data () {
        return {
            form: {
                name: '',
                username: '',
                email: '',
                gender: '',
                description: '',
            },
			errors: [],
			success: '',
			genderOptions: [
                { value: 'm', text: 'Male' },
                { value: 'f', text: 'Female' },
			]
        }
    },
    created() {
        let url = this.$routing.generate('api_user_edit', { id: this.id })
        this.$axios.post(url)
        .then(response => {
            this.form = response.data.user
        })
        .catch(e => {
            this.errors.push(e)
        });
    },
    methods: {
		onSubmit (evt) {
			evt.preventDefault();
			this.errors = [];
            this.success = '';

			if(!this.errors.length) {
                let url = this.$routing.generate('api_user_update', { id: this.id })
				this.$axios.post(url, this.form)
				.then(response => {
					this.success = response.data.success
				})
				.catch(e => {
					this.errors = e.response.data.errors
				});
			}
		}
	}
}
</script>