<template>
	<b-container>
		<b-row class="justify-content-center my-5">
			<b-col md="8">
				<b-card header="Add New User"
					header-tag="h1">
					<b-form @submit="onSubmit">
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
							show
							variant="success">
								<p>{{ success }}</p>
						</b-alert>
						<b-form-group label="Name"
							label-for="registerName">
							<b-form-input id="registerName"
								type="text"
								name="register_name"
                v-model="form.name"
								required></b-form-input>
						</b-form-group>
						<b-form-group label="Username"
							label-for="registerUsername">
							<b-form-input id="registerUsername"
								type="text"
								name="register_username"
                v-model="form.username"
								required></b-form-input>
						</b-form-group>
						<b-form-group label="Email"
							label-for="registerEmail">
							<b-form-input id="registerEmail"
								type="email"
								name="register_email"
                v-model="form.email"
								required></b-form-input>
						</b-form-group>
						<b-form-group label="Password"
							label-for="registerPassword">
							<b-form-input id="registerPassword"
								type="password"
								name="register_password"
                v-model="form.plainPassword"
								required></b-form-input>
						</b-form-group>
						<b-form-group label="Repeat Password"
							label-for="registerPassword2">
							<b-form-input id="registerPassword2"
								type="password"
								name="register_password2"
                v-model="form.plainPassword2"
								required></b-form-input>
						</b-form-group>
						<b-form-group label="Gender"
							label-for="registerGender">
							<b-form-select id="registerGender"
								name="register_gender"
                v-model="form.gender"
								required
								:options="options"></b-form-select>
						</b-form-group>
						<!-- <b-form-group label="Description"
							label-for="registerDescription">
							<b-form-textarea id="registerDescription"
								name="register_description"
								:rows="4"></b-form-textarea>
						</b-form-group> -->
						<div role="group" class="b-form-group form-group">
							<label for="registerDescription" class="col-form-label pt-0">Description</label>
							<div class="">
								<textarea id="registerDescription" name="register_description" rows="4" class="form-control" wrap="soft" v-model="form.description"></textarea>
							</div>
						</div>
						<b-button id="registerSave"
							type="submit"
							name="register_save"
							variant="primary">Save</b-button>
					</b-form>
				</b-card>
			</b-col>
		</b-row>
	</b-container>
</template>

<script>

import axios from 'axios';

export default {
	data () {
		return {
      form: {
        name: '',
        username: '',
        email: '',
        plainPassword: '',
				plainPassword2: '',
        gender: '',
        description: '',
      },
			errors: [],
			success: '',
			selected: null,
			options: [
			{ value: 'm', text: 'Male' },
			{ value: 'f', text: 'Female' },
			]
		}
	},
	methods: {
		onSubmit (evt) {
			evt.preventDefault();
			this.errors = [];

			if(this.form.plainPassword !== this.form.plainPassword2) {
				this.errors.push('Password does not match.');
			}

			if(!this.errors.length) {
				axios.post('api/user/create', this.form)
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