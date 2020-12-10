<template>
    <div>
        <login @loged="login = $event" v-if="!login"/>
        <home v-else/>
    </div>
</template>

<script>
    import home from './Home'
    import login from './Login'
import Login from './Login.vue'

    export default {

        components: {
            home,
            login
        },
        data() {
            return {
                login: true // conflicto
            }
        },
    }
</script>
