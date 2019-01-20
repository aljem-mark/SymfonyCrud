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
							label-for="registerName">
							<b-form-input id="registerName"
								type="text"
                                v-model="form.name"
								required></b-form-input>
						</b-form-group>
						<b-form-group label="Username"
							label-for="registerUsername">
							<b-form-input id="registerUsername"
								type="text"
                                v-model="form.username"
								required></b-form-input>
						</b-form-group>
						<b-form-group label="Email"
							label-for="registerEmail">
							<b-form-input id="registerEmail"
								type="email"
                                v-model="form.email"
								required></b-form-input>
						</b-form-group>
						<b-form-group label="Password"
							label-for="registerPassword">
							<b-form-input id="registerPassword"
								type="password"
                                v-model="form.plainPassword"
								required></b-form-input>
						</b-form-group>
						<b-form-group label="Repeat Password"
							label-for="registerPassword2">
							<b-form-input id="registerPassword2"
								type="password"
                                v-model="form.plainPassword2"
								required></b-form-input>
						</b-form-group>
						<b-form-group label="Gender"
							label-for="registerGender">
							<b-form-select id="registerGender"
                                v-model="form.gender"
								required
								:options="genderOptions"></b-form-select>
						</b-form-group>
						<div role="group" class="b-form-group form-group">
							<label for="registerDescription" class="col-form-label pt-0">Description</label>
							<div class="">
								<textarea id="registerDescription" rows="4" class="form-control" wrap="soft" v-model="form.description"></textarea>
							</div>
						</div>
						<b-button id="registerSave"
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
			genderOptions: [
                { value: 'm', text: 'Male' },
                { value: 'f', text: 'Female' },
			]
		}
	},
	methods: {
		onSubmit (evt) {
			evt.preventDefault();
			this.errors = [];
            this.success = '';

			if(this.form.plainPassword !== this.form.plainPassword2) {
				this.errors.push('Password does not match.');
			}

			if(!this.errors.length) {
                let url = this.$routing.generate('api_user_create')
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