<template>
    <form :action="this.url"
          method="post"
          enctype="multipart/form-data"
          target="_blank">
        <input type="hidden" name="_token" :value="this.csrfToken">

        <button class="plain" type="submit">{{ this.text }}</button>
    </form>
</template>

<script>
    export default {

        props: [
            'url',
            'text'
        ],

        computed: {
            csrfToken: () => window.Laravel.csrf_token
        },

    }
</script>
