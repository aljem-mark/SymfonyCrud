<template>
    <b-navbar toggleable="sm" type="dark" variant="dark">
        <b-navbar-brand :href="homepageLink">Symfony CRUD</b-navbar-brand>
        <b-navbar-toggle target="nav_collapse"></b-navbar-toggle>
        <b-collapse is-nav id="nav_collapse">
            <b-navbar-nav class="ml-auto">
                <b-nav-item :href="usersLink">Users</b-nav-item>
                <b-nav-item :href="usersOnepageLink">Users Onepage</b-nav-item>
                <b-nav-item :href="registerLink">Register</b-nav-item>
                <slot name="modalLink"></slot>
            </b-navbar-nav>
        </b-collapse>
    </b-navbar>
</template>

<script>
export default {
    data () {
        return {
            homepageLink: this.$routing.generate('homepage'),
            usersLink: this.$routing.generate('user_list'),
            usersOnepageLink: this.$routing.generate('user_onepage'),
            registerLink: this.$routing.generate('registration'),
        }
    }
}
</script>
