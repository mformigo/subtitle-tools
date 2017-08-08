<template>
    <div>
        <div id="SubIdxLanguages">

        </div>
    </div>
</template>

<script>
    export default {

        data: () => ({

        }),

        props: [
            'pageId'
        ],

        mounted() {
            console.log(this.pageId);

            // axios.get('url').then(response => {
            //
            // });
        }
    }
</script>
