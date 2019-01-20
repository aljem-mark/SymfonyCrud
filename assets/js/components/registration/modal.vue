<template>
    <div>
        <b-btn v-b-modal.userModal
            class="nav-link"
            variant="link">
            Add User
        </b-btn>

        <b-modal id="userModal" title="Bootstrap-Vue"
            title-tag="h2"
            hide-footer>
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
                    label-for="modalName">
                    <b-form-input id="modalName"
                        type="text"
                        v-model="form.name"
                        required></b-form-input>
                </b-form-group>
                <b-form-group label="Username"
                    label-for="modalUsername">
                    <b-form-input id="modalUsername"
                        type="text"
                        v-model="form.username"
                        required></b-form-input>
                </b-form-group>
                <b-form-group label="Email"
                    label-for="modalEmail">
                    <b-form-input id="modalEmail"
                        type="email"
                        v-model="form.email"
                        required></b-form-input>
                </b-form-group>
                <b-form-group label="Password"
                    label-for="modalPassword">
                    <b-form-input id="modalPassword"
                        type="password"
                        v-model="form.plainPassword"
                        required></b-form-input>
                </b-form-group>
                <b-form-group label="Repeat Password"
                    label-for="modalPassword2">
                    <b-form-input id="modalPassword2"
                        type="password"
                        v-model="form.plainPassword2"
                        required></b-form-input>
                </b-form-group>
                <b-form-group label="Gender"
                    label-for="modalGender">
                    <b-form-select id="modalGender"
                        v-model="form.gender"
                        required
                        :options="genderOptions"></b-form-select>
                </b-form-group>
                <div role="group" class="b-form-group form-group">
                    <label for="modalDescription" class="col-form-label pt-0">Description</label>
                    <div class="">
                        <textarea id="modalDescription" rows="4" class="form-control" wrap="soft" v-model="form.description"></textarea>
                    </div>
                </div>
                <b-button id="modalSave"
                    type="submit"
                    variant="primary">Save</b-button>
            </b-form>
        </b-modal>
    </div>
</template>

<script>
export default {
    name: 'user-modal',
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
                $('#userModal').animate({ scrollTop: 0 }, 'slow')
			}

			if(!this.errors.length) {
                let url = this.$routing.generate('api_user_create')
				this.$axios.post(url, this.form)
				.then(response => {
                    this.success = response.data.success
                    this.$refs.modal.hide()
				})
				.catch(e => {
                    this.errors = e.response.data.errors
                    $('#userModal').animate({ scrollTop: 0 }, 'slow')
				});
			}
		}
	}
}
</script>